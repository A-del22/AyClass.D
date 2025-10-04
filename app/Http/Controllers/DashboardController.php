<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TingkatKelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function viewDashboard()
    {
        $today = today();

        // Get basic statistics
        $totalSiswa = Siswa::count();
        $hadirHariIni = Attendance::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();
        $terlambatHariIni = Attendance::whereDate('tanggal', $today)
            ->where('status', 'terlambat')
            ->count();
        $izinHariIni = Attendance::whereDate('tanggal', $today)
            ->where('status', 'izin')
            ->count();
        $sakitHariIni = Attendance::whereDate('tanggal', $today)
            ->where('status', 'sakit')
            ->count();

        // Total yang sudah absen
        $totalAbsenHariIni = $hadirHariIni + $terlambatHariIni + $izinHariIni + $sakitHariIni;

        // Hitung tidak hadir hanya jika ada yang absen hari ini
        $tidakHadirHariIni = $totalAbsenHariIni > 0 ? ($totalSiswa - $totalAbsenHariIni) : 0;

        // Get classes list for filter
        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        // Weekly attendance data for chart
        $weeklyData = $this->getWeeklyAttendanceData();

        // Monthly attendance breakdown
        $monthlyBreakdown = $this->getMonthlyBreakdown($today);

        return view('dashboard.index', compact(
            'totalSiswa',
            'hadirHariIni',
            'terlambatHariIni',
            'tidakHadirHariIni',
            'kelasList',
            'weeklyData',
            'monthlyBreakdown',
            'today'
        ));
    }

    public function getAttendanceData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $kelasId = $request->get('kelas_id');
        $period = $request->get('period', 'daily'); // daily, monthly, yearly
        $date = $request->get('date', today());

        $query = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply period filter
        switch ($period) {
            case 'daily':
                $query->whereDate('tanggal', $date);
                break;
            case 'monthly':
                $query->whereYear('tanggal', Carbon::parse($date)->year)
                    ->whereMonth('tanggal', Carbon::parse($date)->month);
                break;
            case 'yearly':
                $query->whereYear('tanggal', Carbon::parse($date)->year);
                break;
        }

        // Apply class filter
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
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal ? $row->tanggal->format('d/m/Y') : '-';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match ($row->status) {
                    'hadir' => 'bg-success',
                    'izin' => 'bg-info',
                    'sakit' => 'bg-secondary',
                    default => 'bg-secondary'
                };

                $statusText = match ($row->status) {
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    default => ucfirst($row->status)
                };

                return '<span class="badge ' . $badgeClass . '"><i class="ri-checkbox-circle-line me-1"></i>' . $statusText . '</span>';
            })
            ->addColumn('waktu_masuk', function ($row) {
                return $row->waktu_masuk ? $row->waktu_masuk->format('H:i:s') : '-';
            })
            ->addColumn('method', function ($row) {
                $icon = $row->method === 'qr_scan' ? 'ri-qr-code-line' : 'ri-edit-line';
                $text = $row->method === 'qr_scan' ? 'QR Scan' : 'Manual';
                return '<span class="badge bg-light text-dark"><i class="' . $icon . ' me-1"></i>' . $text . '</span>';
            })
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?
                    '<span class="text-muted">' . Str::limit($row->keterangan, 30) . '</span>' :
                    '<span class="text-muted">-</span>';
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
            ->rawColumns(['status', 'method', 'keterangan'])
            ->make(true);
    }

    public function getStatistics(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $kelasId = $request->get('kelas_id');
        $period = $request->get('period', 'daily');
        $date = $request->get('date', today());

        // Base query for students
        $siswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $siswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $siswaQuery->count();

        // Base query for attendance
        $attendanceQuery = Attendance::query();

        // Apply period filter
        switch ($period) {
            case 'daily':
                $attendanceQuery->whereDate('tanggal', $date);
                break;
            case 'monthly':
                $attendanceQuery->whereYear('tanggal', Carbon::parse($date)->year)
                    ->whereMonth('tanggal', Carbon::parse($date)->month);
                break;
            case 'yearly':
                $attendanceQuery->whereYear('tanggal', Carbon::parse($date)->year);
                break;
        }

        // Apply class filter for attendance
        if ($kelasId && $kelasId !== 'all') {
            $attendanceQuery->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $hadir = (clone $attendanceQuery)->where('status', 'hadir')->count();
        $terlambat = (clone $attendanceQuery)->where('status', 'terlambat')->count();
        $izin = (clone $attendanceQuery)->where('status', 'izin')->count();
        $sakit = (clone $attendanceQuery)->where('status', 'sakit')->count();

        $totalAbsen = $hadir + $terlambat + $izin + $sakit;

        // Hitung tidak hadir berdasarkan periode
        $tidakHadir = 0;
        if ($period === 'daily') {
            // Hanya hitung tidak hadir jika ada yang absen di hari itu
            $tidakHadir = $totalAbsen > 0 ? max(0, $totalSiswa - $totalAbsen) : 0;
        } else {
            // Untuk monthly/yearly, hitung per hari yang ada absensi
            $dates = [];

            if ($period === 'monthly') {
                $start = Carbon::parse($date)->startOfMonth();
                $end = Carbon::parse($date)->endOfMonth();
            } else { // yearly
                $start = Carbon::parse($date)->startOfYear();
                $end = Carbon::parse($date)->endOfYear();
            }

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if ($d->isWeekday()) {
                    $dailyQuery = Attendance::whereDate('tanggal', $d);

                    if ($kelasId && $kelasId !== 'all') {
                        $dailyQuery->whereHas('siswa', function ($q) use ($kelasId) {
                            $q->where('kelas_id', $kelasId);
                        });
                    }

                    $dailyCount = $dailyQuery->count();
                    // Hanya hitung tidak hadir jika ada yang absen di hari itu
                    if ($dailyCount > 0) {
                        $tidakHadir += ($totalSiswa - $dailyCount);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_siswa' => $totalSiswa,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'tidak_hadir' => $tidakHadir,
            ]
        ]);
    }

    private function getWeeklyAttendanceData()
    {
        $endDate = today();
        $startDate = $endDate->copy()->subDays(6);
        $totalSiswa = Siswa::count();

        $data = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $hadir = Attendance::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->count();
            $terlambat = Attendance::whereDate('tanggal', $date)
                ->where('status', 'terlambat')
                ->count();
            $izin = Attendance::whereDate('tanggal', $date)
                ->where('status', 'izin')
                ->count();
            $sakit = Attendance::whereDate('tanggal', $date)
                ->where('status', 'sakit')
                ->count();

            $totalAbsen = $hadir + $terlambat + $izin + $sakit;
            $tidakHadir = $totalAbsen > 0 ? ($totalSiswa - $totalAbsen) : 0;

            $data[] = [
                'date' => $date->format('d/m'),
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'tidak_hadir' => $tidakHadir
            ];
        }

        return $data;
    }

    private function getMonthlyBreakdown($date)
    {
        $hadir = Attendance::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->where('status', 'hadir')
            ->count();
        $terlambat = Attendance::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->where('status', 'terlambat')
            ->count();
        $izin = Attendance::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->where('status', 'izin')
            ->count();
        $sakit = Attendance::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->where('status', 'sakit')
            ->count();

        return [
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'sakit' => $sakit,
        ];
    }

    public function getChartData(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Akses tidak diizinkan'], 403);
        }

        $kelasId = $request->get('kelas_id');
        $period = $request->get('period', 'daily');
        $date = $request->get('date', today());

        // Get weekly data for trend chart
        $weeklyData = $this->getWeeklyChartData($kelasId, $period, $date);

        // Get status distribution data
        $statusData = $this->getStatusChartData($kelasId, $period, $date);

        return response()->json([
            'success' => true,
            'data' => [
                'weekly' => $weeklyData,
                'status' => $statusData
            ]
        ]);
    }

    private function getWeeklyChartData($kelasId, $period, $date)
    {
        // Get total students
        $siswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $siswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $siswaQuery->count();

        switch ($period) {
            case 'daily':
                // Last 7 days
                $endDate = Carbon::parse($date);
                $startDate = $endDate->copy()->subDays(6);
                break;
            case 'monthly':
                // Last 7 weeks (by week)
                $endDate = Carbon::parse($date)->endOfMonth();
                $startDate = $endDate->copy()->subWeeks(6)->startOfWeek();
                break;
            case 'yearly':
                // Last 7 months
                $endDate = Carbon::parse($date)->endOfYear();
                $startDate = $endDate->copy()->subMonths(6)->startOfMonth();
                break;
        }

        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $query = Attendance::query();

            // Apply class filter
            if ($kelasId && $kelasId !== 'all') {
                $query->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }

            // Apply date range based on period
            switch ($period) {
                case 'daily':
                    $query->whereDate('tanggal', $current);
                    $label = $current->format('d/m');
                    $current->addDay();
                    break;
                case 'monthly':
                    $weekStart = $current->copy()->startOfWeek();
                    $weekEnd = $current->copy()->endOfWeek();
                    $query->whereBetween('tanggal', [$weekStart, $weekEnd]);
                    $label = 'W' . $current->weekOfMonth;
                    $current->addWeek();
                    break;
                case 'yearly':
                    $query->whereYear('tanggal', $current->year)
                        ->whereMonth('tanggal', $current->month);
                    $label = $current->format('M');
                    $current->addMonth();
                    break;
            }

            $hadir = (clone $query)->where('status', 'hadir')->count();
            $terlambat = (clone $query)->where('status', 'terlambat')->count();
            $izin = (clone $query)->where('status', 'izin')->count();
            $sakit = (clone $query)->where('status', 'sakit')->count();

            $totalAbsen = $hadir + $terlambat + $izin + $sakit;
            $tidakHadir = $totalAbsen > 0 ? ($totalSiswa - $totalAbsen) : 0;

            $data[] = [
                'date' => $label,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'tidak_hadir' => $tidakHadir
            ];
        }

        return $data;
    }

    private function getStatusChartData($kelasId, $period, $date)
    {
        $query = Attendance::query();

        // Apply class filter
        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Apply period filter
        switch ($period) {
            case 'daily':
                $query->whereDate('tanggal', $date);
                break;
            case 'monthly':
                $query->whereYear('tanggal', Carbon::parse($date)->year)
                    ->whereMonth('tanggal', Carbon::parse($date)->month);
                break;
            case 'yearly':
                $query->whereYear('tanggal', Carbon::parse($date)->year);
                break;
        }

        $hadir = (clone $query)->where('status', 'hadir')->count();
        $terlambat = (clone $query)->where('status', 'terlambat')->count();
        $izin = (clone $query)->where('status', 'izin')->count();
        $sakit = (clone $query)->where('status', 'sakit')->count();

        return [
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'sakit' => $sakit,
        ];
    }
}
