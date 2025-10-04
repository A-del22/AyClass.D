@extends('layouts.app')

@section('title', 'Laporan Presensi Harian')

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
                        <li class="breadcrumb-item active">Laporan Harian</li>
                    </ol>
                </div>
                <h4 class="page-title">Laporan Presensi Harian</h4>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('attendance.reports.daily') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $date->format('Y-m-d') }}">
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
                            <button type="button" class="btn btn-success" onclick="exportDaily()">
                                <i class="ri-download-line me-1"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Siswa">Total Siswa</h5>
                            <h3 class="my-2 py-1">{{ $totalSiswa }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-nowrap">{{ $kelasId && $kelasId !== 'all' ? 'Kelas dipilih' : 'Semua kelas' }}</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="total-siswa-chart" data-colors="#727cf5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Hadir">Hadir</h5>
                            <h3 class="my-2 py-1 text-success">{{ ($attendanceStats['total_hadir'] ?? 0) + ($attendanceStats['total_terlambat'] ?? 0) }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success">
                                    {{ $totalSiswa > 0 ? round(((($attendanceStats['total_hadir'] ?? 0) + ($attendanceStats['total_terlambat'] ?? 0)) / $totalSiswa) * 100, 1) : 0 }}%
                                </span>
                                <span class="text-nowrap">dari total</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="hadir-chart" data-colors="#0acf97"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Tidak Hadir">Tidak Hadir</h5>
                            <h3 class="my-2 py-1 text-danger">{{ $attendanceStats['tidak_hadir'] }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-danger">
                                    {{ $totalSiswa > 0 ? round(($attendanceStats['tidak_hadir'] / $totalSiswa) * 100, 1) : 0 }}%
                                </span>
                                <span class="text-nowrap">dari total</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="tidak-hadir-chart" data-colors="#fa5c7c"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Terlambat">Terlambat</h5>
                            <h3 class="my-2 py-1 text-warning">{{ $attendanceStats['total_terlambat'] }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-warning">
                                    {{ $totalSiswa > 0 ? round(($attendanceStats['total_terlambat'] / $totalSiswa) * 100, 1) : 0 }}%
                                </span>
                                <span class="text-nowrap">dari total</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="terlambat-chart" data-colors="#ffbc00"></div>
                            </div>
                        </div>
                    </div>
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
                                    <td class="text-end"><strong>{{ $attendanceStats['total_hadir'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Terlambat</span></td>
                                    <td class="text-end"><strong>{{ $attendanceStats['total_terlambat'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Izin</span></td>
                                    <td class="text-end"><strong>{{ $attendanceStats['total_izin'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Sakit</span></td>
                                    <td class="text-end"><strong>{{ $attendanceStats['total_sakit'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Tidak Hadir</span></td>
                                    <td class="text-end"><strong>{{ $attendanceStats['tidak_hadir'] }}</strong></td>
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
                    <h5 class="card-title">Grafik Kehadiran</h5>
                    <div id="attendance-pie-chart" style="height: 300px;"></div>
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
                        <h5 class="card-title">Data Presensi {{ $date->format('d F Y') }}</h5>
                        <small class="text-muted">
                            {{ $kelasId && $kelasId !== 'all' ? 'Kelas: ' . optional($kelasList->firstWhere('id', $kelasId))->nama_kelas : 'Semua Kelas' }}
                        </small>
                    </div>

                    <div class="table-responsive">
                        <table id="attendance-table" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
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
    $('#attendance-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('attendance.reports.daily.data') }}",
            data: {
                date: "{{ $date->format('Y-m-d') }}",
                kelas_id: "{{ $kelasId }}"
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama', name: 'nama'},
            {data: 'nis', name: 'nis'},
            {data: 'kelas', name: 'kelas'},
            {data: 'status', name: 'status'},
            {data: 'waktu_masuk', name: 'waktu_masuk'},
            {data: 'keterangan', name: 'keterangan'},
        ],
        order: [[1, 'asc']],
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

    // Create pie chart
    var pieOptions = {
        series: [
            {{ ($attendanceStats['total_hadir'] ?? 0) + ($attendanceStats['total_terlambat'] ?? 0) }},
            {{ $attendanceStats['total_izin'] }},
            {{ $attendanceStats['total_sakit'] }},
            {{ $attendanceStats['tidak_hadir'] }}
        ],
        chart: {
            type: 'pie',
            height: 300
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

    var pieChart = new ApexCharts(document.querySelector("#attendance-pie-chart"), pieOptions);
    pieChart.render();
});

function exportDaily() {
    var date = document.getElementById('date').value;
    var kelasId = document.getElementById('kelas_id').value;

    // Create PDF export URL
    var url = "{{ route('attendance.reports.daily.export-pdf') }}?date=" + date + "&kelas_id=" + kelasId;

    // Open PDF in new window
    window.open(url, '_blank');
}
</script>
@endpush