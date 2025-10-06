@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <style>
        .stats-card {
            border: none;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .widget-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Dashboard" />

        <!-- Filters -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label for="filter-kelas" class="form-label">Kelas</label>
                    <select class="form-select" id="filter-kelas">
                        <option value="all">Semua Kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-period" class="form-label">Periode</label>
                    <select class="form-select" id="filter-period">
                        <option value="daily">Harian</option>
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-date" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="filter-date" value="{{ $today->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-secondary w-100" id="reset-filter">
                        <i class="ri-refresh-line me-1"></i>Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Widgets -->
        <div class="row mb-4" style="position: relative;">
            <div class="loading-overlay" id="stats-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-start border-primary border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Murid</h6>
                                <h3 class="mb-0 text-primary" id="total-siswa">{{ number_format($totalSiswa) }}</h3>
                                <small class="text-muted">Terdaftar aktif</small>
                            </div>
                            <div class="text-primary">
                                <i class="ri-group-line widget-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-start border-success border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Hadir Hari Ini</h6>
                                <h3 class="mb-0 text-success" id="hadir-count">{{ number_format($hadirHariIni) }}</h3>
                                <small class="text-muted">
                                    <span class="badge bg-success-subtle text-success" id="hadir-percentage">
                                        {{ $totalSiswa > 0 ? number_format(($hadirHariIni / $totalSiswa) * 100, 1) : 0 }}%
                                    </span>
                                </small>
                            </div>
                            <div class="text-success">
                                <i class="ri-user-add-line widget-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-start border-warning border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Terlambat</h6>
                                <h3 class="mb-0 text-warning" id="terlambat-count">{{ number_format($terlambatHariIni) }}
                                </h3>
                                <small class="text-muted">
                                    <span class="badge bg-warning-subtle text-warning" id="terlambat-percentage">
                                        {{ $totalSiswa > 0 ? number_format(($terlambatHariIni / $totalSiswa) * 100, 1) : 0 }}%
                                    </span>
                                </small>
                            </div>
                            <div class="text-warning">
                                <i class="ri-time-line widget-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-start border-danger border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Tidak Hadir</h6>
                                <h3 class="mb-0 text-danger" id="tidak-hadir-count">
                                    {{ number_format($tidakHadirHariIni) }}</h3>
                                <small class="text-muted">
                                    <span class="badge bg-danger-subtle text-danger" id="tidak-hadir-percentage">
                                        {{ $totalSiswa > 0 ? number_format(($tidakHadirHariIni / $totalSiswa) * 100, 1) : 0 }}%
                                    </span>
                                </small>
                            </div>
                            <div class="text-danger">
                                <i class="ri-user-unfollow-line widget-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="header-title mb-0">Trend Presensi Mingguan</h5>
                        <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="header-title mb-0">Distribusi Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-success-subtle rounded">
                                        <small class="text-muted d-block">Hadir</small>
                                        <h6 class="text-success mb-0" id="chart-hadir">{{ $monthlyBreakdown['hadir'] }}
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-warning-subtle rounded">
                                        <small class="text-muted d-block">Terlambat</small>
                                        <h6 class="text-warning mb-0" id="chart-terlambat">
                                            {{ $monthlyBreakdown['terlambat'] }}
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-info-subtle rounded">
                                        <small class="text-muted d-block">Izin</small>
                                        <h6 class="text-info mb-0" id="chart-izin">
                                            {{ $monthlyBreakdown['izin'] }}
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-secondary-subtle rounded">
                                        <small class="text-muted d-block">Sakit</small>
                                        <h6 class="text-secondary mb-0" id="chart-sakit">
                                            {{ $monthlyBreakdown['sakit'] }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title mb-0">Data Presensi</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="refresh-table">
                                <i class="ri-refresh-line me-1"></i>Refresh
                            </button>
                            {{-- <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="ri-download-line me-1"></i>Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="export-excel">
                                            <i class="ri-file-excel-line me-2"></i>Excel
                                        </a></li>
                                    <li><a class="dropdown-item" href="#" id="export-pdf">
                                            <i class="ri-file-pdf-line me-2"></i>PDF
                                        </a></li>
                                </ul>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendance-table" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Waktu Masuk</th>
                                        <th>Method</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize charts
            initWeeklyChart();
            initStatusChart();

            // Initialize DataTable
            let attendanceTable = $('#attendance-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('dashboard.attendance-data') }}',
                    data: function(d) {
                        d.kelas_id = $('#filter-kelas').val();
                        d.period = $('#filter-period').val();
                        d.date = $('#filter-date').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nis',
                        name: 'nis'
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'waktu_masuk',
                        name: 'waktu_masuk'
                    },
                    {
                        data: 'method',
                        name: 'method',
                        orderable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false
                    }
                ],
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data Presensi"
                },
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });

            // Filter functionality - Auto filter on change
            $('#filter-kelas, #filter-period').on('change', function() {
                applyFilters();
            });

            // Debounce for date input
            let dateTimeout;
            $('#filter-date').on('input change', function() {
                clearTimeout(dateTimeout);
                dateTimeout = setTimeout(function() {
                    applyFilters();
                }, 300);
            });

            // Reset filter functionality
            $('#reset-filter').on('click', function() {
                $('#filter-kelas').val('all');
                $('#filter-period').val('daily');
                $('#filter-date').val('{{ $today->format('Y-m-d') }}');

                // Trigger change to update data
                applyFilters();
            });

            function applyFilters() {
                // Show loading
                $('#stats-loading').show();

                // Update data
                updateStatistics();
                attendanceTable.ajax.reload();
                updateCharts();

                // Hide loading after a short delay
                setTimeout(function() {
                    $('#stats-loading').hide();
                }, 500);
            }

            $('#refresh-table').on('click', function() {
                attendanceTable.ajax.reload();
            });

            // Auto refresh every 5 minutes
            setInterval(function() {
                attendanceTable.ajax.reload(null, false);
                updateStatistics();
            }, 300000);

            function updateStatistics() {
                $.ajax({
                    url: '{{ route('dashboard.statistics') }}',
                    data: {
                        kelas_id: $('#filter-kelas').val(),
                        period: $('#filter-period').val(),
                        date: $('#filter-date').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;

                            // Update widgets
                            $('#total-siswa').text(formatNumber(data.total_siswa));
                            $('#hadir-count').text(formatNumber(data.hadir));
                            $('#terlambat-count').text(formatNumber(data.terlambat));
                            $('#tidak-hadir-count').text(formatNumber(data.tidak_hadir));

                            // Update percentages
                            const totalSiswa = data.total_siswa;
                            if (totalSiswa > 0) {
                                $('#hadir-percentage').text(((data.hadir / totalSiswa) * 100).toFixed(
                                    1) + '%');
                                $('#terlambat-percentage').text(((data.terlambat / totalSiswa) * 100)
                                    .toFixed(
                                        1) + '%');
                                $('#tidak-hadir-percentage').text(((data.tidak_hadir / totalSiswa) *
                                    100).toFixed(1) + '%');
                            }

                            // Update chart data
                            $('#chart-hadir').text(formatNumber(data.hadir));
                            $('#chart-terlambat').text(formatNumber(data.terlambat));
                            $('#chart-izin').text(formatNumber(data.izin));
                            $('#chart-sakit').text(formatNumber(data.sakit));
                        }
                    }
                });
            }

            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            function initWeeklyChart() {
                const ctx = document.getElementById('weeklyChart').getContext('2d');

                const weeklyData = @json($weeklyData);
                const labels = weeklyData.map(item => item.date);
                const hadirData = weeklyData.map(item => item.hadir);
                const terlambatData = weeklyData.map(item => item.terlambat);
                const tidakHadirData = weeklyData.map(item => item.tidak_hadir);

                window.weeklyChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Hadir',
                            data: hadirData,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Terlambat',
                            data: terlambatData,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Tidak Hadir',
                            data: tidakHadirData,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            function initStatusChart() {
                const ctx = document.getElementById('statusChart').getContext('2d');

                const monthlyData = @json($monthlyBreakdown);

                window.statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit'],
                        datasets: [{
                            data: [
                                monthlyData.hadir,
                                monthlyData.terlambat,
                                monthlyData.izin,
                                monthlyData.sakit
                            ],
                            backgroundColor: [
                                '#28a745',
                                '#ffc107',
                                '#17a2b8',
                                '#6c757d'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            function updateCharts() {
                $.ajax({
                    url: '{{ route('dashboard.chart-data') }}',
                    data: {
                        kelas_id: $('#filter-kelas').val(),
                        period: $('#filter-period').val(),
                        date: $('#filter-date').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            updateWeeklyChart(response.data.weekly);
                            updateStatusChart(response.data.status);
                        }
                    },
                    error: function() {
                        alert('Gagal memuat data grafik');
                    }
                });
            }

            function updateWeeklyChart(data) {
                const labels = data.map(item => item.date);
                const hadirData = data.map(item => item.hadir);
                const terlambatData = data.map(item => item.terlambat);
                const tidakHadirData = data.map(item => item.tidak_hadir);

                window.weeklyChart.data.labels = labels;
                window.weeklyChart.data.datasets[0].data = hadirData;
                window.weeklyChart.data.datasets[1].data = terlambatData;
                window.weeklyChart.data.datasets[2].data = tidakHadirData;

                // Update chart title based on period
                const period = $('#filter-period').val();
                let title = 'Trend Presensi ';
                switch (period) {
                    case 'daily':
                        title += 'Mingguan (7 Hari)';
                        break;
                    case 'monthly':
                        title += 'Bulanan (Per Minggu)';
                        break;
                    case 'yearly':
                        title += 'Tahunan (Per Bulan)';
                        break;
                }

                $('.col-lg-8 .card-header h5').text(title);
                window.weeklyChart.update();
            }

            function updateStatusChart(data) {
                window.statusChart.data.datasets[0].data = [
                    data.hadir,
                    data.terlambat,
                    data.izin,
                    data.sakit
                ];

                // Update summary numbers
                $('#chart-hadir').text(formatNumber(data.hadir));
                $('#chart-terlambat').text(formatNumber(data.terlambat));
                $('#chart-izin').text(formatNumber(data.izin));
                $('#chart-sakit').text(formatNumber(data.sakit));

                window.statusChart.update();
            }
        });
    </script>
@endpush
