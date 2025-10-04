@extends('layouts.app')

@section('title', 'Laporan Presensi per Siswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Presensi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.reports.index') }}">Rekap & Laporan</a></li>
                        <li class="breadcrumb-item active">Laporan per Siswa</li>
                    </ol>
                </div>
                <h4 class="page-title">Laporan Presensi per Siswa</h4>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('attendance.reports.student') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="siswa_id" class="form-label">Pilih Siswa</label>
                            <select class="form-select" id="siswa_id" name="siswa_id" required>
                                <option value="">-- Pilih Siswa --</option>
                                @if($siswa)
                                    <option value="{{ $siswa->id }}" selected>
                                        {{ $siswa->user->name }} - {{ $siswa->nis }} ({{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }})
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-search-line me-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($siswa)
    <!-- Student Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h5 class="card-title">Informasi Siswa</h5>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Nama:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $siswa->user->name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>NIS:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $siswa->nis }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Kelas:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Periode:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 text-end">
                            <button class="btn btn-success" onclick="exportStudentReport()">
                                <i class="ri-download-line me-1"></i> Export Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Hari Sekolah</h6>
                    <h4 class="my-2 py-1">{{ $statistics['total_hari_sekolah'] ?? 0 }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">Hari kerja</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Hadir</h6>
                    <h4 class="my-2 py-1 text-success">{{ ($statistics['total_hadir'] ?? 0) + ($statistics['total_terlambat'] ?? 0) }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-success">
                            {{ ($statistics['total_hari_sekolah'] ?? 0) > 0 ? round(((($statistics['total_hadir'] ?? 0) + ($statistics['total_terlambat'] ?? 0)) / $statistics['total_hari_sekolah']) * 100, 1) : 0 }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>


        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Izin</h6>
                    <h4 class="my-2 py-1 text-info">{{ $statistics['total_izin'] ?? 0 }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-info">
                            {{ ($statistics['total_hari_sekolah'] ?? 0) > 0 ? round((($statistics['total_izin'] ?? 0) / $statistics['total_hari_sekolah']) * 100, 1) : 0 }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Sakit</h6>
                    <h4 class="my-2 py-1 text-secondary">{{ $statistics['total_sakit'] ?? 0 }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-secondary">
                            {{ ($statistics['total_hari_sekolah'] ?? 0) > 0 ? round((($statistics['total_sakit'] ?? 0) / $statistics['total_hari_sekolah']) * 100, 1) : 0 }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Persentase Kehadiran</h6>
                    <h4 class="my-2 py-1 text-{{ ($statistics['persentase_kehadiran'] ?? 0) >= 75 ? 'success' : 'danger' }}">
                        {{ $statistics['persentase_kehadiran'] ?? 0 }}%
                    </h4>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">
                            {{ ($statistics['persentase_kehadiran'] ?? 0) >= 75 ? 'Baik' : 'Perlu Perhatian' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grafik Kehadiran Harian</h5>
                    <div id="student-attendance-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribusi Kehadiran</h5>
                    <div id="student-pie-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Riwayat Kehadiran</h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Status</th>
                                    <th>Waktu Masuk</th>
                                    <th>Keterangan</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceData as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $attendance->tanggal->format('d/m/Y') }}</td>
                                    <td>{{ $attendance->tanggal->format('l') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($attendance->status) {
                                                'hadir' => 'bg-success',
                                                'terlambat' => 'bg-warning',
                                                'izin' => 'bg-info',
                                                'sakit' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };

                                            $statusText = match($attendance->status) {
                                                'hadir' => 'Hadir',
                                                'terlambat' => 'Terlambat',
                                                'izin' => 'Izin',
                                                'sakit' => 'Sakit',
                                                default => ucfirst($attendance->status)
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>{{ $attendance->waktu_masuk ? $attendance->waktu_masuk->format('H:i:s') : '-' }}</td>
                                    <td>{{ $attendance->keterangan ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $attendance->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data kehadiran dalam periode ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-user-search-line font-48 text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih siswa untuk melihat laporan kehadiran</h5>
                    <p class="text-muted">Gunakan form di atas untuk memilih siswa dan periode laporan</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for student search
    $('#siswa_id').select2({
        placeholder: '-- Pilih Siswa --',
        allowClear: true,
        ajax: {
            url: "{{ route('attendance.reports.student.search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    @if($siswa && !empty($attendanceData))
    // Create attendance chart
    createStudentChart();

    // Create pie chart
    createStudentPieChart();
    @endif
});

function createStudentChart() {
    var attendanceData = @json($attendanceData);
    var dates = [];
    var statusData = {
        'hadir': [],
        'izin': [],
        'sakit': []
    };

    // Process data for chart
    attendanceData.forEach(function(item) {
        dates.push(item.tanggal);
        // Add 1 for the status, 0 for others
        statusData.hadir.push((item.status === 'hadir' || item.status === 'terlambat') ? 1 : 0);
        statusData.izin.push(item.status === 'izin' ? 1 : 0);
        statusData.sakit.push(item.status === 'sakit' ? 1 : 0);
    });

    var options = {
        series: [
            {
                name: 'Hadir',
                data: statusData.hadir
            },
            {
                name: 'Izin',
                data: statusData.izin
            },
            {
                name: 'Sakit',
                data: statusData.sakit
            }
        ],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: true
            }
        },
        colors: ['#0acf97', '#39afd1', '#6c757d'],
        plotOptions: {
            bar: {
                horizontal: false,
            },
        },
        xaxis: {
            categories: dates.reverse(),
            title: {
                text: 'Tanggal'
            }
        },
        yaxis: {
            title: {
                text: 'Status Kehadiran'
            },
            max: 1
        },
        legend: {
            position: 'top'
        },
        fill: {
            opacity: 1
        }
    };

    var chart = new ApexCharts(document.querySelector("#student-attendance-chart"), options);
    chart.render();
}

function createStudentPieChart() {
    var statistics = @json($statistics);

    var pieOptions = {
        series: [
            (statistics.total_hadir || 0) + (statistics.total_terlambat || 0),
            statistics.total_izin || 0,
            statistics.total_sakit || 0
        ],
        chart: {
            type: 'donut',
            height: 300
        },
        labels: ['Hadir', 'Izin', 'Sakit'],
        colors: ['#0acf97', '#39afd1', '#6c757d'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var pieChart = new ApexCharts(document.querySelector("#student-pie-chart"), pieOptions);
    pieChart.render();
}

function exportStudentReport() {
    var siswaId = "{{ $siswa->id ?? '' }}";
    var startDate = "{{ $startDate }}";
    var endDate = "{{ $endDate }}";

    if (!siswaId) {
        alert('Silakan pilih siswa terlebih dahulu');
        return;
    }

    // Create PDF export URL
    var url = "{{ route('attendance.reports.student.export-pdf') }}?siswa_id=" + siswaId + "&start_date=" + startDate + "&end_date=" + endDate;

    // Open PDF in new window
    window.open(url, '_blank');
}
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('assets/vendor/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
@endpush