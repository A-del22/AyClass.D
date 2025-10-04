<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AppSettings;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(403, 'Akses ditolak. Anda bukan siswa.');
        }

        // Get today's attendance
        $todayAttendance = Attendance::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', today())
            ->first();

        // Get this month statistics
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlyStats = $this->getAttendanceStats($siswa->id, $startOfMonth, $endOfMonth);

        // Get recent attendance (last 7 days)
        $recentAttendance = Attendance::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [Carbon::now()->subDays(6), Carbon::now()])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('student.dashboard.index', compact(
            'siswa',
            'todayAttendance',
            'monthlyStats',
            'recentAttendance'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(403, 'Akses ditolak. Anda bukan siswa.');
        }

        return view('student.profile.index', compact('siswa'));
    }

    public function attendanceHistory(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(403, 'Akses ditolak. Anda bukan siswa.');
        }

        // Get parameters with proper filtering - default to daily for better UX
        $type = $request->input('type', 'daily');
        $year = $request->input('year', date('Y'));
        $semester = $request->input('semester', date('n') <= 6 ? 1 : 2);

        // Get date parameter - filter out empty values
        $dateParam = $request->input('date');
        $date = !empty($dateParam) ? $dateParam : null;

        // Initialize variables
        $attendanceData = null;
        $stats = null;
        $displayDate = null;

        try {
            if ($type === 'daily') {
                // Daily view - always shows today's data (no date selection needed)
                $targetDate = Carbon::now();
                $displayDate = $targetDate->format('Y-m-d');

                $attendanceData = Attendance::where('siswa_id', $siswa->id)
                    ->whereDate('tanggal', $targetDate)
                    ->first();

            } elseif ($type === 'monthly') {
                // Monthly view
                if ($date) {
                    $targetDate = Carbon::createFromFormat('Y-m', $date);
                } else {
                    $targetDate = Carbon::now();
                }

                $startDate = $targetDate->copy()->startOfMonth();
                $endDate = $targetDate->copy()->endOfMonth();
                $displayDate = $targetDate->format('Y-m');

                $attendanceData = Attendance::where('siswa_id', $siswa->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->orderBy('tanggal', 'desc')
                    ->get();

                $stats = $this->getAttendanceStats($siswa->id, $startDate, $endDate);

            } elseif ($type === 'semester') {
                // Semester view
                if ($semester == 1) {
                    $startDate = Carbon::create($year, 1, 1);
                    $endDate = Carbon::create($year, 6, 30);
                } else {
                    $startDate = Carbon::create($year, 7, 1);
                    $endDate = Carbon::create($year, 12, 31);
                }

                $attendanceData = Attendance::where('siswa_id', $siswa->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->orderBy('tanggal', 'desc')
                    ->get();

                $stats = $this->getAttendanceStats($siswa->id, $startDate, $endDate);
            }

        } catch (\Exception $e) {
            // Fallback to monthly current month
            $now = Carbon::now();
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();

            $attendanceData = Attendance::where('siswa_id', $siswa->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'desc')
                ->get();

            $stats = $this->getAttendanceStats($siswa->id, $startDate, $endDate);
            $type = 'monthly';
            $displayDate = $now->format('Y-m');
        }

        return view('student.attendance.history', compact(
            'siswa',
            'attendanceData',
            'stats',
            'type',
            'date',
            'year',
            'semester',
            'displayDate'
        ));
    }

    public function qrCode()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(403, 'Akses ditolak. Anda bukan siswa.');
        }

        return view('student.qrcode.index', compact('siswa'));
    }

    public function generateQrCode($nis)
    {
        try {
            $user = Auth::user();
            $siswa = $user->siswa;

            if (!$siswa) {
                return response('Student not found for user', 404);
            }

            if ($siswa->nis !== (string) $nis) {
                abort(403, 'Akses ditolak.');
            }

            // Generate QR Code with the student NIS using SVG format
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->errorCorrection('M')
                ->generate($siswa->nis);

            return response($qrCode, 200, [
                'Content-Type' => 'image/svg+xml',
                'Content-Disposition' => 'inline; filename="qr-code-' . $siswa->nis . '.svg"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return a simple text response for debugging
            return response('QR Code generation failed: ' . $e->getMessage(), 500, ['Content-Type' => 'text/plain']);
        }
    }

    public function downloadQrCode($nis)
    {
        try {
            $user = Auth::user();
            $siswa = $user->siswa;

            if (!$siswa || $siswa->nis !== (string) $nis) {
                abort(403, 'Akses ditolak.');
            }

            // Load with same relationships as original
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($siswa->id);
            $appSettings = AppSettings::first();
            return view('student.qrcode.download', compact('siswa', 'appSettings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh kartu: ' . $e->getMessage());
        }
    }

    public function printQrCode($nis)
    {
        try {
            $user = Auth::user();
            $siswa = $user->siswa;

            if (!$siswa || $siswa->nis !== (string) $nis) {
                abort(403, 'Akses ditolak.');
            }

            // Load with same relationships as original
            $siswa = Siswa::with(['user', 'kelas.tingkatKelas', 'kelas.jurusan'])->findOrFail($siswa->id);
            $appSettings = AppSettings::first();
            return view('student.qrcode.print', compact('siswa', 'appSettings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan halaman cetak kartu');
        }
    }

    public function getAttendanceData(Request $request)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->get('type', 'monthly');
        $date = $request->get('date', Carbon::now()->format('Y-m'));

        try {
            switch ($type) {
                case 'monthly':
                    $dateValue = !empty($date) ? $date : Carbon::now()->format('Y-m');
                    $selectedDate = Carbon::createFromFormat('Y-m', $dateValue);
                    if (!$selectedDate) {
                        $selectedDate = Carbon::now();
                    }
                    $startDate = $selectedDate->copy()->startOfMonth();
                    $endDate = $selectedDate->copy()->endOfMonth();
                    break;

                case 'semester':
                    $year = $request->get('year', Carbon::now()->year);
                    $semester = $request->get('semester', Carbon::now()->month <= 6 ? 1 : 2);

                    if ($semester == 1) {
                        $startDate = Carbon::create($year, 1, 1);
                        $endDate = Carbon::create($year, 6, 30);
                    } else {
                        $startDate = Carbon::create($year, 7, 1);
                        $endDate = Carbon::create($year, 12, 31);
                    }
                    break;

                case 'daily':
                    $dateParam = $request->get('date');
                    $dateValue = !empty($dateParam) ? $dateParam : Carbon::now()->format('Y-m-d');
                    $selectedDate = Carbon::createFromFormat('Y-m-d', $dateValue);
                    if (!$selectedDate) {
                        $selectedDate = Carbon::now();
                    }
                    $startDate = $selectedDate->copy()->startOfDay();
                    $endDate = $selectedDate->copy()->endOfDay();
                    break;

                default:
                    // Default to monthly if type is not recognized
                    $dateValue = !empty($date) ? $date : Carbon::now()->format('Y-m');
                    $selectedDate = Carbon::createFromFormat('Y-m', $dateValue);
                    if (!$selectedDate) {
                        $selectedDate = Carbon::now();
                    }
                    $startDate = $selectedDate->copy()->startOfMonth();
                    $endDate = $selectedDate->copy()->endOfMonth();
                    break;
            }
        } catch (\Exception $e) {
            // Fallback to current month if any date parsing fails
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $attendanceData = Attendance::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($attendance) {
                return [
                    'tanggal' => $attendance->tanggal->format('Y-m-d'),
                    'hari' => $attendance->tanggal->format('l'),
                    'status' => $attendance->status,
                    'waktu_masuk' => $attendance->waktu_masuk ? $attendance->waktu_masuk->format('H:i:s') : null,
                    'keterangan' => $attendance->keterangan,
                    'method' => $attendance->method
                ];
            });

        return response()->json($attendanceData);
    }

    private function getAttendanceStats($siswaId, $startDate, $endDate)
    {
        $attendanceQuery = Attendance::where('siswa_id', $siswaId)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $totalHadir = $attendanceQuery->clone()->where('status', 'hadir')->count();
        $totalIzin = $attendanceQuery->clone()->where('status', 'izin')->count();
        $totalSakit = $attendanceQuery->clone()->where('status', 'sakit')->count();

        // Calculate school days (weekdays only)
        $totalHariSekolah = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $totalHariSekolah++;
            }
            $current->addDay();
        }

        $totalAbsen = $totalHadir + $totalIzin + $totalSakit;
        $persentaseKehadiran = $totalHariSekolah > 0 ? round(($totalHadir / $totalHariSekolah) * 100, 1) : 0;

        return [
            'total_hari_sekolah' => $totalHariSekolah,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_absen' => $totalAbsen,
            'persentase_kehadiran' => $persentaseKehadiran
        ];
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            abort(403, 'Akses ditolak. Anda bukan siswa.');
        }

        return view('student.change-password', compact('siswa'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Check if new password is same as current password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('student.change-password')->with('success', 'Password berhasil diubah!');
    }
}
