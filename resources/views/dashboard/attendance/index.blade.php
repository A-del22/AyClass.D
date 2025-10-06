@extends('layouts.app')
@section('title', 'Presensi Siswa')
@push('styles')
    <link href="{{ asset('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <style>
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.375rem;
        }

        @media (max-width: 992px) {
            .card-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .card-header>div:last-child {
                flex-direction: column;
                gap: 0.75rem;
            }

            .filter-controls {
                flex-direction: column;
                gap: 0.75rem;
            }

            .filter-group {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .filter-group label {
                min-width: 80px;
                text-align: left;
            }

            .filter-group select,
            .filter-group input {
                flex: 1;
                min-width: 0;
            }
        }

        @media (max-width: 768px) {
            .card-header {
                padding: 1rem;
            }

            .card-header h4 {
                font-size: 1.1rem;
            }

            .card-header p {
                font-size: 0.875rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }

            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            .filter-controls {
                gap: 0.5rem;
            }

            .filter-group {
                flex-direction: column;
                align-items: stretch;
                gap: 0.25rem;
            }

            .filter-group label {
                font-size: 0.875rem;
                min-width: auto;
                text-align: left;
            }

            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length {
                text-align: center;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 576px) {
            .card-header {
                padding: 0.75rem;
            }

            .filter-group select,
            .filter-group input {
                font-size: 0.875rem;
            }

            .action-buttons .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
                min-height: 38px;
            }

            .action-buttons .btn i {
                font-size: 1rem;
            }

            .filter-group label {
                font-size: 0.8rem;
            }

            .filter-group select {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            .filter-group input[type="date"] {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
        }

        /* Additional improvements for button states */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        /* Hover effects for enabled buttons */
        .btn:not(:disabled):hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        /* Focus styles for accessibility */
        .btn:focus,
        .form-select:focus,
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            border-color: #80bdff;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        @media (max-width: 576px) {
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.5rem;
                margin: 0 0.125rem;
            }
        }

        .stats-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.875rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            font-weight: 600;
        }

        .icon-shape {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .quick-actions {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .action-btn {
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.25rem;
            font-size: 0.875rem;
        }

        .action-btn:hover {
            background-color: #0056b3;
            color: white;
            text-decoration: none;
        }

        .action-btn.qr-btn {
            background-color: #28a745;
        }

        .action-btn.qr-btn:hover {
            background-color: #1e7e34;
        }

        .action-btn.manual-btn {
            background-color: #ffc107;
            color: #212529;
        }

        .action-btn.manual-btn:hover {
            background-color: #e0a800;
            color: #212529;
        }

        .table-container {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin-top: 1rem;
            overflow: hidden;
        }

        .table-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        .table-header h5 {
            margin: 0;
            color: #495057;
            font-weight: 600;
        }

        .date-filter {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .date-filter input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            min-width: 150px;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-hadir {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }


        .status-izin,
        .status-sakit {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .stats-card .card-body {
                padding: 1rem;
            }

            .stats-number {
                font-size: 1.5rem;
            }

            .quick-actions {
                padding: 1rem;
            }

            .action-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
                width: 100%;
                margin: 0.25rem 0;
                justify-content: center;
            }

            .table-header {
                padding: 1rem;
            }

            .date-filter {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .date-filter input {
                width: 100%;
                min-width: auto;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }

            .stats-number {
                font-size: 1.25rem;
            }

            .icon-shape {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Presensi Siswa" />

        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-group-line text-primary" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="totalSiswa">{{ $totalSiswa }}</h3>
                        <p class="text-muted mb-0">Total Siswa</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-check-line text-success" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="hadir">{{ $hadir }}</h3>
                        <p class="text-muted mb-0">Hadir</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-time-line text-warning" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="terlambat">{{ $terlambat ?? 0 }}</h3>
                        <p class="text-muted mb-0">Terlambat</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-close-line text-danger" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="tidakMasuk">{{ $tidakHadir ?? 0 }}</h3>
                        <p class="text-muted mb-0">Tidak Hadir</p>
                        {{-- <small class="text-muted">Hanya dihitung jika ada aktivitas presensi</small> --}}
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h4 class="header-title mb-2">Data Presensi Siswa</h4>
                            <p class="text-muted mb-0">
                                Daftar presensi siswa dengan filter tanggal dan kelas. Data dapat dicari berdasarkan nama,
                                NIS, atau kelas.
                            </p>
                        </div>
                        <div>
                            <div class="action-buttons d-flex gap-2 mb-3 mt-2">
                                <button id="qrScanBtn" class="btn btn-secondary" disabled
                                    title="Pilih kelas terlebih dahulu">
                                    <i class="ri-qr-scan-line me-1"></i>
                                    <span class="d-none d-sm-inline">Scan QR Code</span>
                                    <span class="d-sm-none">QR Scan</span>
                                </button>
                                <button id="manualAttendanceBtn" class="btn btn-secondary" disabled
                                    title="Pilih kelas terlebih dahulu">
                                    <i class="ri-edit-line me-1"></i>
                                    <span class="d-none d-sm-inline">Presensi Manual</span>
                                    <span class="d-sm-none">Manual</span>
                                </button>
                            </div>

                            <!-- Help Text -->
                            <div id="helpText" class="alert alert-info alert-sm mb-3" style="font-size: 0.875rem;">
                                <i class="ri-information-line me-1"></i>
                                Pilih kelas untuk mengaktifkan tombol QR Scan dan Presensi Manual
                            </div>

                            <!-- Filter Controls -->
                            <div class="filter-controls d-flex gap-3">
                                <div class="filter-group d-flex align-items-center gap-2">
                                    <label for="filterKelas" class="text-muted mb-0 flex-shrink-0">
                                        <i class="ri-team-line me-1"></i> Kelas:
                                    </label>
                                    <select id="filterKelas" class="form-select">
                                        <option value="all">Semua Kelas</option>
                                        @foreach ($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}
                                                ({{ $kelas->siswas_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-group d-flex align-items-center gap-2">
                                    <label for="filterDate" class="text-muted mb-0 flex-shrink-0">
                                        <i class="ri-calendar-line me-1"></i> Tanggal:
                                    </label>
                                    <input type="date" id="filterDate" class="form-control"
                                        value="{{ $today->format('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="attendanceTable" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama</th>
                                        <th width="15%">NIS</th>
                                        <th width="15%">Kelas</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Waktu</th>
                                        <th width="10%">Method</th>
                                        <th width="10%">Keterangan</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan dimuat via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buat Sesi Presensi -->
    <div class="modal fade" id="sessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="ri-time-line me-2"></i>Buat Sesi Presensi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="sessionForm">
                    @csrf
                    <input type="hidden" id="session_kelas_id" name="kelas_id">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Keterangan:</strong> Siswa yang presensi <strong>setelah waktu selesai</strong> akan
                            otomatis dicatat sebagai <span class="badge bg-warning">Terlambat</span>
                        </div>

                        <div class="mb-3">
                            <label for="waktu_mulai" class="form-label">Waktu Mulai <span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="modal_waktu_mulai" name="waktu_mulai"
                                value="07:00" required>
                            <small class="text-muted">Waktu mulai bisa presensi</small>
                        </div>

                        <div class="mb-3">
                            <label for="waktu_selesai" class="form-label">Waktu Selesai <span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="modal_waktu_selesai" name="waktu_selesai"
                                value="08:00" required>
                            <small class="text-muted">Batas waktu tepat waktu (setelah ini = terlambat)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-play-circle-line me-1"></i> Mulai Sesi & Scan QR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const table = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'Detail Presensi: ' + data.nama;
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                            tableClass: 'table'
                        })
                    }
                },
                deferRender: true,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                pageLength: 10,
                ajax: {
                    url: '{{ route('attendance.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.date = $('#filterDate').val();
                        d.kelas_id = $('#filterKelas').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '50px',
                        className: 'text-center',
                        responsivePriority: 1
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        orderable: false,
                        searchable: true,
                        className: 'fw-medium',
                        responsivePriority: 2
                    },
                    {
                        data: 'nis',
                        name: 'nis',
                        orderable: false,
                        searchable: true,
                        responsivePriority: 3
                    },
                    {
                        data: 'kelas',
                        name: 'kelas',
                        orderable: false,
                        searchable: true,
                        responsivePriority: 4
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 5
                    },
                    {
                        data: 'waktu_masuk',
                        name: 'waktu_masuk',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 6
                    },
                    {
                        data: 'method',
                        name: 'method',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 7
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 8
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '80px',
                        className: 'text-center',
                        responsivePriority: 9
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'asc']
                ],
                searchDelay: 400,
                stateSave: false,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: "Memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'></i>",
                        next: "<i class='ri-arrow-right-s-line'></i>",
                        first: "Pertama",
                        last: "Terakhir"
                    },
                    emptyTable: "Tidak ada data presensi tersedia",
                    loadingRecords: "Memuat data..."
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                }
            });

            // Handle filter changes
            $('#filterDate, #filterKelas').on('change', function() {
                table.ajax.reload();
                loadStatistics();
            });

            // Handle class selection for buttons
            $('#filterKelas').on('change', function() {
                const selectedKelas = $(this).val();
                const qrScanBtn = $('#qrScanBtn');
                const manualBtn = $('#manualAttendanceBtn');
                const helpText = $('#helpText');

                if (selectedKelas === 'all' || !selectedKelas) {
                    qrScanBtn.prop('disabled', true);
                    manualBtn.prop('disabled', true);
                    qrScanBtn.removeClass('btn-success').addClass('btn-secondary');
                    manualBtn.removeClass('btn-warning').addClass('btn-secondary');
                    qrScanBtn.attr('title', 'Pilih kelas terlebih dahulu');
                    manualBtn.attr('title', 'Pilih kelas terlebih dahulu');
                    helpText.show();
                } else {
                    qrScanBtn.prop('disabled', false);
                    manualBtn.prop('disabled', false);
                    qrScanBtn.removeClass('btn-secondary').addClass('btn-success');
                    manualBtn.removeClass('btn-secondary').addClass('btn-warning');
                    qrScanBtn.attr('title', 'Scan QR Code untuk Presensi');
                    manualBtn.attr('title', 'Input Presensi secara manual');
                    helpText.hide();
                }
            });

            // Handle button clicks
            $('#qrScanBtn').on('click', function() {
                const selectedKelas = $('#filterKelas').val();
                if (selectedKelas && selectedKelas !== 'all') {
                    // Set kelas_id di modal
                    $('#session_kelas_id').val(selectedKelas);
                    // Show modal
                    $('#sessionModal').modal('show');
                }
            });

            // Handle session form submit
            $('#sessionForm').on('submit', function(e) {
                e.preventDefault();

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

                $.ajax({
                    url: '{{ route('attendance.create-session') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Redirect to QR scan page
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        const error = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message ||
                                'Terjadi kesalahan saat membuat sesi presensi'
                        });
                    }
                });
            });

            $('#manualAttendanceBtn').on('click', function() {
                const selectedKelas = $('#filterKelas').val();
                if (selectedKelas && selectedKelas !== 'all') {
                    window.location.href = '{{ route('attendance.manual') }}?kelas_id=' + selectedKelas;
                }
            });

            // Load statistics function
            function loadStatistics() {
                const selectedKelas = $('#filterKelas').val();
                const selectedDate = $('#filterDate').val();

                $.ajax({
                    url: '{{ route('attendance.statistics-by-class') }}',
                    type: 'GET',
                    data: {
                        kelas_id: selectedKelas,
                        date: selectedDate
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#totalSiswa').text(data.total_siswa);
                            $('#hadir').text(data.hadir);
                            $('#terlambat').text(data.terlambat);
                            $('#tidakMasuk').text(data.tidak_masuk);
                        }
                    },
                    error: function() {
                        alert('Gagal memuat data statistik');
                    }
                });
            }

            // Initial statistics load
            loadStatistics();
        });
    </script>
@endpush
