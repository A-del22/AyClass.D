<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReportController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        return view('dashboard.attendance.reports.index', compact('kelasList'));
    }

    public function dailyReport(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : today();
        $kelasId = $request->get('kelas_id');

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        // Get attendance statistics
        $query = Attendance::whereDate('tanggal', $date);
        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $attendanceStats = [
            'total_hadir' => (clone $query)->where('status', 'hadir')->count(),
            'total_izin' => (clone $query)->where('status', 'izin')->count(),
            'total_sakit' => (clone $query)->where('status', 'sakit')->count(),
            'total_terlambat' => (clone $query)->where('status', 'terlambat')->count(),
        ];

        $attendanceStats['total_absen'] = array_sum($attendanceStats);
        $attendanceStats['total_hadir_keseluruhan'] = $attendanceStats['total_hadir'] + $attendanceStats['total_terlambat'];

        // Get total students
        $totalSiswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $totalSiswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $totalSiswaQuery->count();

        // Hitung siswa tidak hadir hanya jika ada yang absen di hari itu
        if ($attendanceStats['total_absen'] > 0) {
            $attendanceStats['tidak_hadir'] = $totalSiswa - $attendanceStats['total_absen'];
        } else {
            $attendanceStats['tidak_hadir'] = 0;
        }

        $attendanceStats['belum_absen'] = $totalSiswa - $attendanceStats['total_absen'];

        return view('dashboard.attendance.reports.daily', compact('date', 'kelasId', 'kelasList', 'attendanceStats', 'totalSiswa'));
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $kelasId = $request->get('kelas_id');

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Get monthly statistics
        $monthlyStats = $this->getMonthlyStatistics($startDate, $endDate, $kelasId);

        return view('dashboard.attendance.reports.monthly', compact('month', 'kelasId', 'kelasList', 'monthlyStats', 'startDate', 'endDate'));
    }

    public function yearlyReport(Request $request)
    {
        $year = $request->get('year', now()->format('Y'));
        $kelasId = $request->get('kelas_id');

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
        $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();

        // Get yearly statistics
        $yearlyStats = $this->getYearlyStatistics($startDate, $endDate, $kelasId);

        return view('dashboard.attendance.reports.yearly', compact('year', 'kelasId', 'kelasList', 'yearlyStats', 'startDate', 'endDate'));
    }

    public function studentReport(Request $request)
    {
        $siswaId = $request->get('siswa_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $siswa = null;
        $attendanceData = [];
        $statistics = [];

        if ($siswaId) {
            $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);

            $attendances = Attendance::where('siswa_id', $siswaId)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'desc')
                ->get();

            $attendanceData = $attendances;

            // Calculate statistics
            $statistics = [
                'total_hadir' => $attendances->where('status', 'hadir')->count(),
                'total_izin' => $attendances->where('status', 'izin')->count(),
                'total_sakit' => $attendances->where('status', 'sakit')->count(),
                'total_terlambat' => $attendances->where('status', 'terlambat')->count(),
            ];

            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $weekdays = 0;
            for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
                if ($date->isWeekday()) {
                    $weekdays++;
                }
            }

            $statistics['total_hari_sekolah'] = $weekdays;
            $statistics['total_absen'] = array_sum(array_slice($statistics, 0, 5));
            $statistics['persentase_kehadiran'] = $weekdays > 0 ? round((($statistics['total_hadir'] + $statistics['total_terlambat']) / $weekdays) * 100, 2) : 0;
        }

        return view('dashboard.attendance.reports.student', compact('siswa', 'attendanceData', 'statistics', 'startDate', 'endDate'));
    }

    public function classReport(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        $kelas = null;
        $classStats = [];

        if ($kelasId) {
            $kelas = Kelas::with(['tingkatKelas', 'jurusan'])->findOrFail($kelasId);
            $classStats = $this->getClassStatistics($kelasId, $startDate, $endDate);
        }

        return view('dashboard.attendance.reports.class', compact('kelasId', 'kelasList', 'kelas', 'classStats', 'startDate', 'endDate'));
    }

    public function getDailyReportData(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : today();
        $kelasId = $request->get('kelas_id');

        $query = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->whereDate('tanggal', $date);

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
                $badgeClass = match($row->status) {
                    'hadir' => 'bg-success',
                    'terlambat' => 'bg-warning',
                    'izin' => 'bg-info',
                    'sakit' => 'bg-secondary',
                    default => 'bg-secondary'
                };

                $statusText = match($row->status) {
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
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function getMonthlyReportData(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $kelasId = $request->get('kelas_id');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $query = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

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
                return $row->tanggal ? $row->tanggal->format('d M Y') : '-';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match($row->status) {
                    'hadir' => 'bg-success',
                    'terlambat' => 'bg-warning',
                    'izin' => 'bg-info',
                    'sakit' => 'bg-secondary',
                    default => 'bg-secondary'
                };

                $statusText = match($row->status) {
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
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function getYearlyReportData(Request $request)
    {
        $year = $request->get('year', now()->format('Y'));
        $kelasId = $request->get('kelas_id');

        $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
        $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();

        $query = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

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
                return $row->tanggal ? $row->tanggal->format('d M Y') : '-';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match($row->status) {
                    'hadir' => 'bg-success',
                    'terlambat' => 'bg-warning',
                    'izin' => 'bg-info',
                    'sakit' => 'bg-secondary',
                    default => 'bg-secondary'
                };

                $statusText = match($row->status) {
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
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function getMonthlyChartData(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $kelasId = $request->get('kelas_id');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Get total students
        $totalSiswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $totalSiswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $totalSiswaQuery->count();

        $dailyStats = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) { // Only weekdays
                $query = Attendance::whereDate('tanggal', $date);

                if ($kelasId && $kelasId !== 'all') {
                    $query->whereHas('siswa', function ($q) use ($kelasId) {
                        $q->where('kelas_id', $kelasId);
                    });
                }

                $hadir = (clone $query)->where('status', 'hadir')->count();
                $terlambat = (clone $query)->where('status', 'terlambat')->count();
                $izin = (clone $query)->where('status', 'izin')->count();
                $sakit = (clone $query)->where('status', 'sakit')->count();
                $totalAbsen = $hadir + $terlambat + $izin + $sakit;

                // Hitung tidak hadir hanya jika ada yang absen di hari itu
                $tidakHadir = $totalAbsen > 0 ? ($totalSiswa - $totalAbsen) : 0;

                $dailyStats[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('d'),
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'tidak_hadir' => $tidakHadir,
                ];
            }
        }

        return response()->json($dailyStats);
    }

    public function getYearlyChartData(Request $request)
    {
        $year = $request->get('year', now()->format('Y'));
        $kelasId = $request->get('kelas_id');

        $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
        $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();

        // Get total students
        $totalSiswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $totalSiswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $totalSiswaQuery->count();

        $monthlyStats = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

            $query = Attendance::whereBetween('tanggal', [$monthStart, $monthEnd]);

            if ($kelasId && $kelasId !== 'all') {
                $query->whereHas('siswa', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }

            // Hitung siswa tidak hadir per hari yang ada absensi dalam bulan ini
            $monthlyTidakHadir = 0;
            for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
                if ($date->isWeekday()) {
                    $dailyQuery = Attendance::whereDate('tanggal', $date);
                    if ($kelasId && $kelasId !== 'all') {
                        $dailyQuery->whereHas('siswa', function ($q) use ($kelasId) {
                            $q->where('kelas_id', $kelasId);
                        });
                    }

                    $dailyCount = $dailyQuery->count();
                    // Hanya hitung tidak hadir jika ada yang absen di hari itu
                    if ($dailyCount > 0) {
                        $monthlyTidakHadir += ($totalSiswa - $dailyCount);
                    }
                }
            }

            $monthlyStats[] = [
                'month' => $monthStart->format('M'),
                'month_name' => $monthStart->translatedFormat('F'),
                'hadir' => (clone $query)->where('status', 'hadir')->count(),
                'terlambat' => (clone $query)->where('status', 'terlambat')->count(),
                'izin' => (clone $query)->where('status', 'izin')->count(),
                'sakit' => (clone $query)->where('status', 'sakit')->count(),
                'tidak_hadir' => $monthlyTidakHadir,
            ];
        }

        return response()->json($monthlyStats);
    }

    public function getStudentSearchData(Request $request)
    {
        $search = $request->get('search');

        $query = Siswa::with(['user', 'kelas'])
            ->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhere('nis', 'like', "%{$search}%")
            ->limit(10);

        $students = $query->get()->map(function ($siswa) {
            return [
                'id' => $siswa->id,
                'text' => $siswa->user->name . ' - ' . $siswa->nis . ' (' . ($siswa->kelas->nama_kelas ?? 'Belum ada kelas') . ')'
            ];
        });

        return response()->json($students);
    }

    public function getClassReportData(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if (!$kelasId) {
            return response()->json(['error' => 'Kelas harus dipilih'], 400);
        }

        $students = Siswa::with(['user', 'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }])
            ->where('kelas_id', $kelasId)
            ->get();

        $data = $students->map(function ($siswa) use ($startDate, $endDate) {
            $attendances = $siswa->attendances;

            $stats = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
            ];

            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $weekdays = 0;
            for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
                if ($date->isWeekday()) {
                    $weekdays++;
                }
            }

            $totalAbsen = array_sum($stats);
            $persentaseKehadiran = $weekdays > 0 ? round((($stats['hadir'] + $stats['terlambat']) / $weekdays) * 100, 2) : 0;

            return [
                'nama' => $siswa->user->name,
                'nis' => $siswa->nis,
                'hadir' => $stats['hadir'] + $stats['terlambat'],
                'izin' => $stats['izin'],
                'sakit' => $stats['sakit'],
                'total_absen' => $totalAbsen,
                'persentase_kehadiran' => $persentaseKehadiran
            ];
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('persentase_kehadiran', function ($row) {
                $class = $row['persentase_kehadiran'] >= 75 ? 'text-success' : 'text-danger';
                return '<span class="' . $class . '">' . $row['persentase_kehadiran'] . '%</span>';
            })
            ->rawColumns(['persentase_kehadiran'])
            ->make(true);
    }

    private function getMonthlyStatistics($startDate, $endDate, $kelasId = null)
    {
        $query = Attendance::whereBetween('tanggal', [$startDate, $endDate]);

        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $stats = [
            'total_hadir' => (clone $query)->where('status', 'hadir')->count(),
            'total_izin' => (clone $query)->where('status', 'izin')->count(),
            'total_sakit' => (clone $query)->where('status', 'sakit')->count(),
            'total_terlambat' => (clone $query)->where('status', 'terlambat')->count(),
        ];

        $stats['total_absen'] = array_sum($stats);

        // Calculate weekdays in the month
        $weekdays = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $weekdays++;
            }
        }

        $totalSiswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $totalSiswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $totalSiswaQuery->count();

        // Hitung siswa tidak hadir per hari yang ada absensi
        $daysWithAttendance = 0;
        $totalTidakHadir = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $dailyQuery = Attendance::whereDate('tanggal', $date);
                if ($kelasId && $kelasId !== 'all') {
                    $dailyQuery->whereHas('siswa', function ($q) use ($kelasId) {
                        $q->where('kelas_id', $kelasId);
                    });
                }

                $dailyCount = $dailyQuery->count();
                // Hanya hitung tidak hadir jika ada yang absen di hari itu
                if ($dailyCount > 0) {
                    $daysWithAttendance++;
                    $totalTidakHadir += ($totalSiswa - $dailyCount);
                }
            }
        }

        $stats['tidak_hadir'] = $totalTidakHadir;
        $stats['total_hari_sekolah'] = $weekdays;
        $stats['total_siswa'] = $totalSiswa;
        $expectedAttendance = $weekdays * $totalSiswa;
        $stats['persentase_kehadiran'] = $expectedAttendance > 0 ? round((($stats['total_hadir'] + $stats['total_terlambat']) / $expectedAttendance) * 100, 2) : 0;

        return $stats;
    }

    private function getYearlyStatistics($startDate, $endDate, $kelasId = null)
    {
        $query = Attendance::whereBetween('tanggal', [$startDate, $endDate]);

        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $stats = [
            'total_hadir' => (clone $query)->where('status', 'hadir')->count(),
            'total_izin' => (clone $query)->where('status', 'izin')->count(),
            'total_sakit' => (clone $query)->where('status', 'sakit')->count(),
            'total_terlambat' => (clone $query)->where('status', 'terlambat')->count(),
        ];

        $stats['total_absen'] = array_sum($stats);

        // Calculate weekdays in the year
        $weekdays = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $weekdays++;
            }
        }

        $totalSiswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $totalSiswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $totalSiswaQuery->count();

        // Hitung siswa tidak hadir per hari yang ada absensi
        $daysWithAttendance = 0;
        $totalTidakHadir = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $dailyQuery = Attendance::whereDate('tanggal', $date);
                if ($kelasId && $kelasId !== 'all') {
                    $dailyQuery->whereHas('siswa', function ($q) use ($kelasId) {
                        $q->where('kelas_id', $kelasId);
                    });
                }

                $dailyCount = $dailyQuery->count();
                // Hanya hitung tidak hadir jika ada yang absen di hari itu
                if ($dailyCount > 0) {
                    $daysWithAttendance++;
                    $totalTidakHadir += ($totalSiswa - $dailyCount);
                }
            }
        }

        $stats['tidak_hadir'] = $totalTidakHadir;
        $stats['total_hari_sekolah'] = $weekdays;
        $stats['total_siswa'] = $totalSiswa;
        $expectedAttendance = $weekdays * $totalSiswa;
        $stats['persentase_kehadiran'] = $expectedAttendance > 0 ? round((($stats['total_hadir'] + $stats['total_terlambat']) / $expectedAttendance) * 100, 2) : 0;

        return $stats;
    }

    private function getClassStatistics($kelasId, $startDate, $endDate)
    {
        $students = Siswa::with(['user', 'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }])
            ->where('kelas_id', $kelasId)
            ->get();

        $totalStudents = $students->count();

        $weekdays = 0;
        for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
            if ($date->isWeekday()) {
                $weekdays++;
            }
        }

        $classStats = [
            'total_siswa' => $totalStudents,
            'total_hari_sekolah' => $weekdays,
            'total_hadir' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_terlambat' => 0,
        ];

        foreach ($students as $siswa) {
            $attendances = $siswa->attendances;
            $classStats['total_hadir'] += $attendances->where('status', 'hadir')->count();
            $classStats['total_izin'] += $attendances->where('status', 'izin')->count();
            $classStats['total_sakit'] += $attendances->where('status', 'sakit')->count();
            $classStats['total_terlambat'] += $attendances->where('status', 'terlambat')->count();
        }

        $expectedAttendance = $weekdays * $totalStudents;
        $classStats['persentase_kehadiran'] = $expectedAttendance > 0 ? round((($classStats['total_hadir'] + $classStats['total_terlambat']) / $expectedAttendance) * 100, 2) : 0;

        return $classStats;
    }

    public function exportDailyReportPdf(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : today();
        $kelasId = $request->get('kelas_id');

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        // Get attendance statistics
        $query = Attendance::whereDate('tanggal', $date);
        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $attendanceStats = [
            'total_hadir' => (clone $query)->where('status', 'hadir')->count(),
            'total_izin' => (clone $query)->where('status', 'izin')->count(),
            'total_sakit' => (clone $query)->where('status', 'sakit')->count(),
            'total_terlambat' => (clone $query)->where('status', 'terlambat')->count(),
        ];

        $attendanceStats['total_absen'] = array_sum($attendanceStats);
        $attendanceStats['total_hadir_keseluruhan'] = $attendanceStats['total_hadir'] + $attendanceStats['total_terlambat'];

        // Get total students
        $totalSiswaQuery = Siswa::query();
        if ($kelasId && $kelasId !== 'all') {
            $totalSiswaQuery->where('kelas_id', $kelasId);
        }
        $totalSiswa = $totalSiswaQuery->count();

        // Hitung siswa tidak hadir hanya jika ada yang absen di hari itu
        if ($attendanceStats['total_absen'] > 0) {
            $attendanceStats['tidak_hadir'] = $totalSiswa - $attendanceStats['total_absen'];
        } else {
            $attendanceStats['tidak_hadir'] = 0;
        }

        $attendanceStats['belum_absen'] = $totalSiswa - $attendanceStats['total_absen'];

        // Get detailed attendance data
        $attendanceData = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->whereDate('tanggal', $date);

        if ($kelasId && $kelasId !== 'all') {
            $attendanceData->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $attendanceData = $attendanceData->orderBy('waktu_masuk')->get();

        $kelasName = $kelasId && $kelasId !== 'all'
            ? $kelasList->find($kelasId)->nama_kelas ?? 'Semua Kelas'
            : 'Semua Kelas';

        $appSetting = AppSetting::first();

        $pdf = Pdf::loadView('dashboard.attendance.reports.pdf.daily', compact(
            'date', 'kelasName', 'attendanceStats', 'totalSiswa', 'attendanceData', 'appSetting'
        ));

        $filename = 'laporan-harian-' . $date->format('Y-m-d') . '-' . str_replace(' ', '-', strtolower($kelasName)) . '.pdf';

        return $pdf->download($filename);
    }

    public function exportMonthlyReportPdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $kelasId = $request->get('kelas_id');

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Get monthly statistics
        $monthlyStats = $this->getMonthlyStatistics($startDate, $endDate, $kelasId);

        // Get detailed attendance data
        $query = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $attendanceData = $query->orderBy('tanggal', 'desc')->get();

        $kelasName = $kelasId && $kelasId !== 'all'
            ? $kelasList->find($kelasId)->nama_kelas ?? 'Semua Kelas'
            : 'Semua Kelas';

        $appSetting = AppSetting::first();

        $pdf = Pdf::loadView('dashboard.attendance.reports.pdf.monthly', compact(
            'month', 'kelasName', 'monthlyStats', 'startDate', 'endDate', 'appSetting', 'attendanceData'
        ));

        $filename = 'laporan-bulanan-' . $startDate->format('Y-m') . '-' . str_replace(' ', '-', strtolower($kelasName)) . '.pdf';

        return $pdf->download($filename);
    }

    public function exportYearlyReportPdf(Request $request)
    {
        $year = $request->get('year', now()->format('Y'));
        $kelasId = $request->get('kelas_id');

        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])
            ->withCount('siswas')
            ->having('siswas_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get();

        $startDate = Carbon::createFromFormat('Y', $year)->startOfYear();
        $endDate = Carbon::createFromFormat('Y', $year)->endOfYear();

        // Get yearly statistics
        $yearlyStats = $this->getYearlyStatistics($startDate, $endDate, $kelasId);

        // Get detailed attendance data
        $query = Attendance::with(['siswa.user', 'siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kelasId && $kelasId !== 'all') {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $attendanceData = $query->orderBy('tanggal', 'desc')->get();

        $kelasName = $kelasId && $kelasId !== 'all'
            ? $kelasList->find($kelasId)->nama_kelas ?? 'Semua Kelas'
            : 'Semua Kelas';

        $appSetting = AppSetting::first();

        $pdf = Pdf::loadView('dashboard.attendance.reports.pdf.yearly', compact(
            'year', 'kelasName', 'yearlyStats', 'startDate', 'endDate', 'appSetting', 'attendanceData'
        ));

        $filename = 'laporan-tahunan-' . $year . '-' . str_replace(' ', '-', strtolower($kelasName)) . '.pdf';

        return $pdf->download($filename);
    }

    public function exportStudentReportPdf(Request $request)
    {
        $siswaId = $request->get('siswa_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if (!$siswaId) {
            return redirect()->back()->with('error', 'Siswa harus dipilih untuk export PDF');
        }

        $siswa = Siswa::with(['user', 'kelas'])->findOrFail($siswaId);

        $attendances = Attendance::where('siswa_id', $siswaId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get();

        $attendanceData = $attendances;

        // Calculate statistics
        $statistics = [
            'total_hadir' => $attendances->where('status', 'hadir')->count(),
            'total_izin' => $attendances->where('status', 'izin')->count(),
            'total_sakit' => $attendances->where('status', 'sakit')->count(),
            'total_terlambat' => $attendances->where('status', 'terlambat')->count(),
        ];

        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $weekdays = 0;
        for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
            if ($date->isWeekday()) {
                $weekdays++;
            }
        }

        $statistics['total_hari_sekolah'] = $weekdays;
        $statistics['total_absen'] = array_sum(array_slice($statistics, 0, 5));
        $statistics['persentase_kehadiran'] = $weekdays > 0 ? round((($statistics['total_hadir'] + $statistics['total_terlambat']) / $weekdays) * 100, 2) : 0;

        $appSetting = AppSetting::first();

        $pdf = Pdf::loadView('dashboard.attendance.reports.pdf.student', compact(
            'siswa', 'attendanceData', 'statistics', 'startDate', 'endDate', 'appSetting'
        ));

        $filename = 'laporan-siswa-' . $siswa->nis . '-' . Carbon::parse($startDate)->format('Y-m-d') . '-' . Carbon::parse($endDate)->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportClassReportPdf(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        if (!$kelasId) {
            return redirect()->back()->with('error', 'Kelas harus dipilih untuk export PDF');
        }

        $kelas = Kelas::with(['tingkatKelas', 'jurusan'])->findOrFail($kelasId);
        $classStats = $this->getClassStatistics($kelasId, $startDate, $endDate);

        // Get detailed class data
        $students = Siswa::with(['user', 'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }])
            ->where('kelas_id', $kelasId)
            ->get();

        $studentData = $students->map(function ($siswa) use ($startDate, $endDate) {
            $attendances = $siswa->attendances;

            $stats = [
                'hadir' => $attendances->where('status', 'hadir')->count(),
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
            ];

            $weekdays = 0;
            for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
                if ($date->isWeekday()) {
                    $weekdays++;
                }
            }

            $totalAbsen = array_sum($stats);
            $persentaseKehadiran = $weekdays > 0 ? round((($stats['hadir'] + $stats['terlambat']) / $weekdays) * 100, 2) : 0;

            return [
                'nama' => $siswa->user->name,
                'nis' => $siswa->nis,
                'hadir' => $stats['hadir'] + $stats['terlambat'],
                'izin' => $stats['izin'],
                'sakit' => $stats['sakit'],
                'total_absen' => $totalAbsen,
                'persentase_kehadiran' => $persentaseKehadiran
            ];
        });

        $appSetting = AppSetting::first();

        $pdf = Pdf::loadView('dashboard.attendance.reports.pdf.class', compact(
            'kelas', 'classStats', 'studentData', 'startDate', 'endDate', 'appSetting'
        ));

        $filename = 'laporan-kelas-' . str_replace(' ', '-', strtolower($kelas->nama_kelas)) . '-' . Carbon::parse($startDate)->format('Y-m-d') . '-' . Carbon::parse($endDate)->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}