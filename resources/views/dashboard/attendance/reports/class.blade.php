@extends('layouts.app')

@section('title', 'Laporan Presensi per Kelas')

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
                        <li class="breadcrumb-item active">Laporan per Kelas</li>
                    </ol>
                </div>
                <h4 class="page-title">Laporan Presensi per Kelas</h4>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('attendance.reports.class') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="kelas_id" class="form-label">Pilih Kelas</label>
                            <select class="form-select" id="kelas_id" name="kelas_id" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelasList as $kelasItem)
                                    <option value="{{ $kelasItem->id }}" {{ $kelasId == $kelasItem->id ? 'selected' : '' }}>
                                        {{ $kelasItem->nama_kelas }}
                                    </option>
                                @endforeach
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

    @if($kelas)
    <!-- Class Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h5 class="card-title">Informasi Kelas</h5>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Nama Kelas:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $kelas->nama_kelas }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Tingkat:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $kelas->tingkatKelas->tingkat ?? '-' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Jurusan:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $kelas->jurusan->nama_jurusan ?? '-' }}
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
                            <button class="btn btn-success" onclick="exportClassReport()">
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
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Total Siswa</h6>
                    <h4 class="my-2 py-1">{{ $classStats['total_siswa'] ?? 0 }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">Siswa terdaftar</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Hari Sekolah</h6>
                    <h4 class="my-2 py-1">{{ $classStats['total_hari_sekolah'] ?? 0 }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">Hari kerja</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Total Hadir</h6>
                    <h4 class="my-2 py-1 text-success">{{ ($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0) }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-success">Kehadiran</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Izin & Sakit</h6>
                    <h4 class="my-2 py-1 text-info">{{ ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0) }}</h4>
                    <p class="mb-0 text-muted">
                        <span class="text-info">Izin/Sakit</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mt-0 text-truncate">Persentase Kehadiran</h6>
                    <h4 class="my-2 py-1 text-{{ ($classStats['persentase_kehadiran'] ?? 0) >= 75 ? 'success' : 'danger' }}">
                        {{ $classStats['persentase_kehadiran'] ?? 0 }}%
                    </h4>
                    <p class="mb-0 text-muted">
                        <span class="text-nowrap">
                            {{ ($classStats['persentase_kehadiran'] ?? 0) >= 75 ? 'Baik' : 'Perlu Perhatian' }}
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
                    <h5 class="card-title">Statistik Detail</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">Hadir</span></td>
                                    <td class="text-end"><strong>{{ number_format(($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ (($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) + ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0) > 0
                                                ? round(((($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) / ((($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) + ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0))) * 100, 1)
                                                : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Izin</span></td>
                                    <td class="text-end"><strong>{{ number_format($classStats['total_izin'] ?? 0) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ (($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) + ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0) > 0
                                                ? round((($classStats['total_izin'] ?? 0) / ((($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) + ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0))) * 100, 1)
                                                : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Sakit</span></td>
                                    <td class="text-end"><strong>{{ number_format($classStats['total_sakit'] ?? 0) }}</strong></td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            {{ (($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) + ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0) > 0
                                                ? round((($classStats['total_sakit'] ?? 0) / ((($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) + ($classStats['total_izin'] ?? 0) + ($classStats['total_sakit'] ?? 0))) * 100, 1)
                                                : 0 }}%
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribusi Kehadiran</h5>
                    <div id="class-pie-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Attendance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Kehadiran per Siswa</h5>

                    <div class="table-responsive">
                        <table id="class-report-table" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Hadir</th>
                                    <th>Izin</th>
                                    <th>Sakit</th>
                                    <th>Total Absen</th>
                                    <th>Persentase Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
                    <i class="ri-group-line font-48 text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih kelas untuk melihat laporan kehadiran</h5>
                    <p class="text-muted">Gunakan form di atas untuk memilih kelas dan periode laporan</p>
                </div>
            </div>
        </div>
    </div>
    @endif
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
    @if($kelas)
    // Initialize DataTable
    $('#class-report-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('attendance.reports.class.data') }}",
            data: {
                kelas_id: "{{ $kelasId }}",
                start_date: "{{ $startDate }}",
                end_date: "{{ $endDate }}"
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama', name: 'nama'},
            {data: 'nis', name: 'nis'},
            {data: 'hadir', name: 'hadir'},
            {data: 'izin', name: 'izin'},
            {data: 'sakit', name: 'sakit'},
            {data: 'total_absen', name: 'total_absen'},
            {data: 'persentase_kehadiran', name: 'persentase_kehadiran'},
        ],
        order: [[7, 'desc']], // Sort by persentase_kehadiran descending
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
    createClassPieChart();
    @endif
});

function createClassPieChart() {
    var classStats = @json($classStats);

    var pieOptions = {
        series: [
            (classStats.total_hadir || 0) + (classStats.total_terlambat || 0),
            classStats.total_izin || 0,
            classStats.total_sakit || 0
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

    var pieChart = new ApexCharts(document.querySelector("#class-pie-chart"), pieOptions);
    pieChart.render();
}

function exportClassReport() {
    var kelasId = "{{ $kelasId ?? '' }}";
    var startDate = "{{ $startDate }}";
    var endDate = "{{ $endDate }}";

    if (!kelasId) {
        alert('Silakan pilih kelas terlebih dahulu');
        return;
    }

    // Create PDF export URL
    var url = "{{ route('attendance.reports.class.export-pdf') }}?kelas_id=" + kelasId + "&start_date=" + startDate + "&end_date=" + endDate;

    // Open PDF in new window
    window.open(url, '_blank');
}
</script>
@endpush