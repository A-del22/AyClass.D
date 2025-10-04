@extends('layouts.app')

@section('title', 'Laporan Presensi Bulanan')

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
                        <li class="breadcrumb-item active">Laporan Bulanan</li>
                    </ol>
                </div>
                <h4 class="page-title">Laporan Presensi Bulanan</h4>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('attendance.reports.monthly') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="month" class="form-label">Bulan</label>
                            <input type="month" class="form-control" id="month" name="month" value="{{ $month }}">
                        </div>
                        <div class="col-md-4">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select class="form-select" id="kelas_id" name="kelas_id">
                                <option value="all" {{ $kelasId == 'all' || !$kelasId ? 'selected' : '' }}>Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ri-search-line me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportMonthly()">
                                <i class="ri-download-line me-1"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ringkasan Bulan {{ $startDate->format('F Y') }}</h5>
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $monthlyStats['total_hari_sekolah'] }}</h3>
                                <p class="text-muted mb-0">Hari Sekolah</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h3 class="text-info">{{ $monthlyStats['total_siswa'] }}</h3>
                                <p class="text-muted mb-0">Total Siswa</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h3 class="text-success">{{ $monthlyStats['total_hadir'] + $monthlyStats['total_terlambat'] }}</h3>
                                <p class="text-muted mb-0">Total Kehadiran</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center">
                                <h3 class="text-{{ $monthlyStats['persentase_kehadiran'] >= 75 ? 'success' : 'danger' }}">
                                    {{ $monthlyStats['persentase_kehadiran'] }}%
                                </h3>
                                <p class="text-muted mb-0">Persentase Kehadiran</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grafik Kehadiran Harian</h5>
                    <div id="monthly-chart" style="height: 400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistik Kehadiran</h5>
                    <div id="monthly-pie-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detail Statistik</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">Hadir</span></td>
                                    <td class="text-end"><strong>{{ number_format($monthlyStats['total_hadir'] + $monthlyStats['total_terlambat']) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ $monthlyStats['total_absen'] > 0 ? round((($monthlyStats['total_hadir'] + $monthlyStats['total_terlambat']) / $monthlyStats['total_absen']) * 100, 1) : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Izin</span></td>
                                    <td class="text-end"><strong>{{ number_format($monthlyStats['total_izin']) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ $monthlyStats['total_absen'] > 0 ? round(($monthlyStats['total_izin'] / $monthlyStats['total_absen']) * 100, 1) : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Sakit</span></td>
                                    <td class="text-end"><strong>{{ number_format($monthlyStats['total_sakit']) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ $monthlyStats['total_absen'] > 0 ? round(($monthlyStats['total_sakit'] / $monthlyStats['total_absen']) * 100, 1) : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Tidak Hadir</span></td>
                                    <td class="text-end"><strong>{{ number_format($monthlyStats['tidak_hadir']) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ $monthlyStats['total_absen'] > 0 ? round(($monthlyStats['tidak_hadir'] / $monthlyStats['total_absen']) * 100, 1) : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <td><strong>Total Absensi</strong></td>
                                    <td class="text-end"><strong>{{ number_format($monthlyStats['total_absen']) }}</strong></td>
                                    <td class="text-end"><small class="text-muted">100%</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Periode</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Periode</td>
                                    <td class="text-end"><strong>{{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Total Hari</td>
                                    <td class="text-end">{{ $startDate->diffInDays($endDate) + 1 }} hari</td>
                                </tr>
                                <tr>
                                    <td>Hari Sekolah</td>
                                    <td class="text-end">{{ $monthlyStats['total_hari_sekolah'] }} hari</td>
                                </tr>
                                <tr>
                                    <td>Hari Libur</td>
                                    <td class="text-end">{{ ($startDate->diffInDays($endDate) + 1) - $monthlyStats['total_hari_sekolah'] }} hari</td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td class="text-end">
                                        {{ $kelasId && $kelasId !== 'all' ? optional($kelasList->firstWhere('id', $kelasId))->nama_kelas : 'Semua Kelas' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Siswa</td>
                                    <td class="text-end">{{ number_format($monthlyStats['total_siswa']) }} siswa</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Data Presensi {{ $startDate->format('F Y') }}</h5>
                        <small class="text-muted">
                            {{ $kelasId && $kelasId !== 'all' ? 'Kelas: ' . optional($kelasList->firstWhere('id', $kelasId))->nama_kelas : 'Semua Kelas' }}
                        </small>
                    </div>

                    <div class="table-responsive">
                        <table id="monthly-attendance-table" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Waktu Masuk</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/vendor/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#monthly-attendance-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('attendance.reports.monthly.data') }}",
            data: {
                month: "{{ $month }}",
                kelas_id: "{{ $kelasId }}"
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'nama', name: 'nama'},
            {data: 'nis', name: 'nis'},
            {data: 'kelas', name: 'kelas'},
            {data: 'status', name: 'status'},
            {data: 'waktu_masuk', name: 'waktu_masuk'},
            {data: 'keterangan', name: 'keterangan'},
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            "decimal":        "",
            "emptyTable":     "Tidak ada data tersedia",
            "info":           "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty":      "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered":   "(disaring dari _MAX_ total entri)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Tampilkan _MENU_ entri",
            "loadingRecords": "Memuat...",
            "processing":     "Memproses...",
            "search":         "Cari:",
            "zeroRecords":    "Tidak ditemukan data yang sesuai",
            "paginate": {
                "first":      "Pertama",
                "last":       "Terakhir",
                "next":       "Selanjutnya",
                "previous":   "Sebelumnya"
            }
        }
    });

    // Load daily chart data
    loadMonthlyChart();

    // Create pie chart
    createPieChart();
});

function loadMonthlyChart() {
    $.ajax({
        url: "{{ route('attendance.reports.monthly.chart') }}",
        data: {
            month: "{{ $month }}",
            kelas_id: "{{ $kelasId }}"
        },
        success: function(data) {
            createDailyChart(data);
        }
    });
}

function createDailyChart(data) {
    var dates = data.map(item => item.day);
    var hadir = data.map(item => item.hadir + item.terlambat);
    var izin = data.map(item => item.izin);
    var sakit = data.map(item => item.sakit);
    var tidakHadir = data.map(item => item.tidak_hadir);

    var options = {
        series: [
            {
                name: 'Hadir',
                data: hadir
            },
            {
                name: 'Izin',
                data: izin
            },
            {
                name: 'Sakit',
                data: sakit
            },
            {
                name: 'Tidak Hadir',
                data: tidakHadir
            }
        ],
        chart: {
            type: 'line',
            height: 400,
            toolbar: {
                show: true
            }
        },
        colors: ['#0acf97', '#39afd1', '#6c757d', '#fa5c7c'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: dates,
            title: {
                text: 'Tanggal'
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Siswa'
            }
        },
        legend: {
            position: 'top'
        },
        grid: {
            borderColor: '#f1f3fa'
        }
    };

    var chart = new ApexCharts(document.querySelector("#monthly-chart"), options);
    chart.render();
}

function createPieChart() {
    var pieOptions = {
        series: [
            {{ $monthlyStats['total_hadir'] + $monthlyStats['total_terlambat'] }},
            {{ $monthlyStats['total_izin'] }},
            {{ $monthlyStats['total_sakit'] }},
            {{ $monthlyStats['tidak_hadir'] }}
        ],
        chart: {
            type: 'donut',
            height: 350
        },
        labels: [
            'Hadir',
            'Izin',
            'Sakit',
            'Tidak Hadir'
        ],
        colors: ['#0acf97', '#39afd1', '#6c757d', '#fa5c7c'],
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

    var pieChart = new ApexCharts(document.querySelector("#monthly-pie-chart"), pieOptions);
    pieChart.render();
}

function exportMonthly() {
    var month = document.getElementById('month').value;
    var kelasId = document.getElementById('kelas_id').value;

    // Create PDF export URL
    var url = "{{ route('attendance.reports.monthly.export-pdf') }}?month=" + month + "&kelas_id=" + kelasId;

    // Open PDF in new window
    window.open(url, '_blank');
}
</script>
@endpush