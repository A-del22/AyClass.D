<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Siswa;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    public function index()
    {
        $today = today();
        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        // Default statistics (all classes)
        $totalSiswa = Siswa::count();
        $hadir = Attendance::today()->where('status', 'hadir')->count();
        $terlambat = Attendance::today()->where('status', 'terlambat')->count();
        $izinSakit = Attendance::today()->tidakHadir()->count();

        // Cek apakah ada aktivitas presensi hari ini
        $totalAbsensiHariIni = $hadir + $terlambat + $izinSakit;

        // Tidak Hadir hanya dihitung jika ada aktivitas presensi
        $tidakHadir = $totalAbsensiHariIni > 0 ? $totalSiswa - $totalAbsensiHariIni : 0;
        $belumAbsen = $tidakHadir; // Untuk compatibility dengan view

        return view('dashboard.attendance.index', compact('today', 'totalSiswa', 'hadir', 'terlambat', 'tidakHadir', 'belumAbsen', 'kelasList'));
    }

    public function createSession(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        // Check if session already exists for this class today
        $existingSession = AttendanceSession::today()
            ->forKelas($request->kelas_id)
            ->active()
            ->first();
        if ($existingSession) {
            $existingSession->update(['is_active' => false]);
        }

        // Create new session
        $session = AttendanceSession::create([
            'tanggal' => today(),
            'kelas_id' => $request->kelas_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'created_by' => Auth::id(),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi presensi berhasil dibuat!',
            'redirect' => route('attendance.qr-scan', ['kelas_id' => $request->kelas_id])
        ]);
    }

    public function endSession(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        AttendanceSession::today()
            ->forKelas($request->kelas_id)
            ->active()
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi presensi telah diakhiri!'
        ]);
    }

    public function qrScan(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        if (!$kelasId || $kelasId === 'all') {
            return redirect()->route('attendance.index')
                ->with('error', 'Silakan pilih kelas terlebih dahulu untuk melakukan scan QR code.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        return view('dashboard.attendance.qr-scan', compact('kelas'));
    }

    public function processQrScan(Request $request)
    {
        // Log incoming request for debugging
        Log::info('QR Scan Request Data:', $request->all());

        // Get NIS from QR code and selected class
        $nis = $request->input('nis');
        $kelasId = $request->input('kelas_id');

        if (!$nis) {
            return response()->json([
                'success' => false,
                'message' => 'NIS tidak ditemukan dalam QR Code. Data yang diterima: ' . json_encode($request->all())
            ], 422);
        }

        if (!$kelasId) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak dipilih. Silakan pilih kelas terlebih dahulu.'
            ], 422);
        }

        $today = today();

        // Find student by NIS
        $siswa = Siswa::with(['user', 'kelas'])->where('nis', $nis)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => "Siswa dengan NIS '{$nis}' tidak ditemukan. Pastikan QR Code valid."
            ], 404);
        }

        // Check if student belongs to the selected class
        if ($siswa->kelas_id != $kelasId) {
            $kelasName = $siswa->kelas->nama_kelas ?? 'Belum ada kelas';
            return response()->json([
                'success' => false,
                'message' => "Siswa {$siswa->user->name} tidak terdaftar di kelas yang dipilih. Siswa terdaftar di kelas: {$kelasName}."
            ], 403);
        }

        // Check active session and validate time
        $activeSession = AttendanceSession::today()
            ->forKelas($kelasId)
            ->active()
            ->first();

        if ($activeSession) {
            $now = now();
            $currentTimeOnly = $now->format('H:i');
            $waktuMulai = Carbon::parse($activeSession->waktu_mulai)->format('H:i');
            $waktuSelesai = Carbon::parse($activeSession->waktu_selesai)->format('H:i');

            if ($currentTimeOnly < $waktuMulai) {
                return response()->json([
                    'success' => false,
                    'message' => "Belum waktunya absen. Sesi presensi dimulai pada pukul {$waktuSelesai}."
                ], 403);
            }
        }

        $existingAttendance = Attendance::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan absensi hari ini',
                'data' => [
                    'status' => $existingAttendance->status,
                    'waktu' => $existingAttendance->waktu_masuk ? $existingAttendance->waktu_masuk->format('H:i:s') : null
                ]
            ], 409);
        }

        $now = now();
        $status = $this->determineAttendanceStatus($now, $kelasId);

        try {
            Attendance::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $today,
                'waktu_masuk' => $now,
                'status' => $status,
                'method' => 'qr_scan',
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dicatat',
                'data' => [
                    'nama' => $siswa->user->name,
                    'nis' => $siswa->nis,
                    'kelas' => $siswa->kelas->nama_kelas ?? 'Belum ada kelas',
                    'status' => $status,
                    'waktu' => $now->format('H:i:s'),
                    'tanggal' => $today->format('d/m/Y')
                ]
            ]);
        } catch (\Exception $exception) {
            Log::error('Error saving attendance: ' . $exception->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan absensi'
            ], 500);
        }
    }

    /**
     * Determine attendance status based on current time and active session
     */
    private function determineAttendanceStatus($currentTime, $kelasId = null)
    {
        $query = AttendanceSession::today()->active();

        if ($kelasId) {
            $query->forKelas($kelasId);
        }

        $activeSession = $query->first();

        if (!$activeSession) {
            return 'hadir'; // Default to hadir if no active session
        }

        // Get time only (H:i:s format)
        $currentTimeOnly = $currentTime->format('H:i:s');
        $waktuSelesai = Carbon::parse($activeSession->waktu_selesai)->format('H:i:s');

        // If before or at waktu_selesai -> hadir
        // If after waktu_selesai -> terlambat
        return $currentTimeOnly <= $waktuSelesai ? 'hadir' : 'terlambat';
    }

    public function manualAttendance(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        if (!$kelasId || $kelasId === 'all') {
            return redirect()->route('attendance.index')
                ->with('error', 'Silakan pilih kelas terlebih dahulu untuk melakukan absensi manual.');
        }

        $today = today();
        $kelas = Kelas::findOrFail($kelasId);

        // Get all students in the class who haven't attended today
        $siswas = Siswa::with(['user', 'kelas'])
            ->where('kelas_id', $kelasId)
            ->whereDoesntHave('attendances', function ($query) use ($today) {
                $query->whereDate('tanggal', $today);
            })
            ->orderBy('nis')
            ->get();

        return view('dashboard.attendance.manual', compact('siswas', 'today', 'kelas'));
    }

    public function storeManualAttendance(Request $request)
    {
        // Base validation rules
        $rules = [
            'siswa_id' => 'required|exists:siswas,id',
            'status' => 'required|in:hadir,terlambat,izin,sakit',
        ];

        // Add conditional validation based on status
        if (in_array($request->status, ['izin', 'sakit'])) {
            $rules['keterangan'] = 'required|string|max:500';

            // Surat izin required only for 'izin' and 'sakit' status
            if (in_array($request->status, ['izin', 'sakit'])) {
                $rules['surat_izin'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
            } else {
                $rules['surat_izin'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
            }
        } else {
            $rules['keterangan'] = 'nullable|string|max:500';
            $rules['surat_izin'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $today = today();
        $siswa = Siswa::findOrFail($request->siswa_id);

        $existingAttendance = Attendance::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('error', 'Siswa sudah memiliki data absensi hari ini')
                ->withInput();
        }

        try {
            $data = [
                'siswa_id' => $siswa->id,
                'tanggal' => $today,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'method' => 'manual',
                'created_by' => Auth::id()
            ];

            // Add waktu_masuk for hadir and terlambat status
            if (in_array($request->status, ['hadir', 'terlambat'])) {
                $data['waktu_masuk'] = now();
            }

            if ($request->hasFile('surat_izin')) {
                $file = $request->file('surat_izin');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('surat-izin', $fileName, 'public');
                $data['surat_izin'] = $filePath;
            }

            Attendance::create($data);

            return redirect()->back()
                ->with('success', 'Absensi manual berhasil dicatat');
        } catch (\Exception $exception) {
            Log::error('Error saving manual attendance: ' . $exception->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan absensi')
                ->withInput();
        }
    }

    public function getAttendanceData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $date = $request->get('date', today());
        $kelasId = $request->get('kelas_id');

        $query = Attendance::with(['siswa.user', 'siswa.kelas', 'createdBy'])
            ->whereDate('tanggal', $date)
            ->orderBy('created_at', 'desc');

        // Filter by class if specified
        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                return $row->siswa->user->name ?? '-';
            })
            ->addColumn('nis', function ($row) {
                return $row->siswa->nis ?? '-';
            })
            ->addColumn('kelas', function ($row) {
                return $row->siswa->kelas->nama_kelas ?? 'Belum ada kelas';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match ($row->status) {
                    'hadir' => 'bg-success',
                    'terlambat' => 'bg-warning',
                    'izin' => 'bg-info',
                    'sakit' => 'bg-secondary',
                    default => 'bg-secondary'
                };

                $statusText = match ($row->status) {
                    'hadir' => 'Hadir',
                    'terlambat' => 'Terlambat',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    default => ucfirst($row->status)
                };

                return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('waktu_masuk', function ($row) {
                return $row->waktu_masuk ? $row->waktu_masuk->format('H:i:s') : '-';
            })
            ->addColumn('method', function ($row) {
                return $row->method === 'qr_scan' ? 'QR Scan' : 'Manual';
            })
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if ($row->surat_izin) {
                    $actions .= '<a href="' . route('attendance.download-surat', $row->id) . '" class="btn btn-sm btn-outline-info" title="Download Surat Izin" target="_blank">
                        <i class="ri-download-line"></i>
                    </a>';
                } else {
                    $actions .= '<span class="text-muted">-</span>';
                }
                return $actions;
            })
            ->filterColumn('nama', function ($query, $keyword) {
                $query->whereHas('siswa.user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('nis', function ($query, $keyword) {
                $query->whereHas('siswa', function ($q) use ($keyword) {
                    $q->where('nis', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('kelas', function ($query, $keyword) {
                $query->whereHas('siswa.kelas', function ($q) use ($keyword) {
                    $q->where('nama_kelas', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function getStatisticsByClass(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $kelasId = $request->get('kelas_id');
        $date = $request->get('date', today());

        if ($kelasId && $kelasId !== 'all') {
            // Statistics for specific class
            $totalSiswa = Siswa::where('kelas_id', $kelasId)->count();

            $hadir = Attendance::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                })
                ->count();

            $terlambat = Attendance::whereDate('tanggal', $date)
                ->where('status', 'terlambat')
                ->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                })
                ->count();

            $izinSakit = Attendance::whereDate('tanggal', $date)
                ->whereIn('status', ['izin', 'sakit'])
                ->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                })
                ->count();

            // Cek apakah ada aktivitas presensi di kelas ini pada tanggal tersebut
            $totalAbsensiHariIni = $hadir + $terlambat + $izinSakit;

            // Tidak Hadir hanya dihitung jika ada aktivitas presensi
            $tidakMasuk = $totalAbsensiHariIni > 0 ? $totalSiswa - $totalAbsensiHariIni : 0;
            $belumAbsen = $tidakMasuk;
        } else {
            // All classes statistics
            $totalSiswa = Siswa::count();
            $hadir = Attendance::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->count();
            $terlambat = Attendance::whereDate('tanggal', $date)
                ->where('status', 'terlambat')
                ->count();
            $izinSakit = Attendance::whereDate('tanggal', $date)
                ->whereIn('status', ['izin', 'sakit'])
                ->count();

            // Cek apakah ada aktivitas presensi hari ini
            $totalAbsensiHariIni = $hadir + $terlambat + $izinSakit;

            // Tidak Hadir hanya dihitung jika ada aktivitas presensi
            $tidakMasuk = $totalAbsensiHariIni > 0 ? $totalSiswa - $totalAbsensiHariIni : 0;
            $belumAbsen = $tidakMasuk;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_siswa' => $totalSiswa,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'tidak_masuk' => $tidakMasuk
            ]
        ]);
    }

    public function downloadSuratIzin($id)
    {
        $attendance = Attendance::findOrFail($id);

        if (!$attendance->surat_izin || !Storage::disk('public')->exists($attendance->surat_izin)) {
            abort(404, 'File surat izin tidak ditemukan');
        }

        return response()->download(storage_path('app/public/' . $attendance->surat_izin));
    }
}
