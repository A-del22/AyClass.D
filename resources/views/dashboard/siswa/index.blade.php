@extends('layouts.app')
@section('title', 'Data Siswa')
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

        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .card-header .btn {
                width: 100%;
            }

            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length {
                text-align: center;
                margin-bottom: 1rem;
            }
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
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Data Siswa" />

        <!-- Statistics Cards -->
        <div class="row mb-3" id="statisticsCards">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-group-line text-primary" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="totalSiswa">0</h3>
                        <p class="text-muted mb-0">Total Siswa</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-user-line text-info" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="siswaLaki">0</h3>
                        <p class="text-muted mb-0">Laki-laki</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-user-2-line text-success" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="siswaPerempuan">0</h3>
                        <p class="text-muted mb-0">Perempuan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">Data Siswa</h4>
                            <p class="text-muted mb-0">
                                Daftar semua siswa yang terdaftar di sekolah beserta informasi kelas dan data pribadi.
                                Data dapat diurutkan, dicari, dan difilter sesuai kebutuhan.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('siswa.add') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Tambah Siswa
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ri-file-excel-line me-1"></i> Excel
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('siswa.template') }}">
                                            <i class="ri-download-line me-1"></i> Download Template
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <i class="ri-upload-line me-1"></i> Import Data
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ri-id-card-line me-1"></i> Kartu Siswa
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#cardByClassModal">
                                            <i class="ri-group-line me-1"></i> Generate Kartu per Kelas
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama</th>
                                        <th width="15%">NIS</th>
                                        <th width="15%">Kelas</th>
                                        <th width="10%">L/P</th>
                                        <th width="10%">Umur</th>
                                        <th width="15%">No. Telepon</th>
                                        <th width="10%">Aksi</th>
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

    <!-- Modal Detail Siswa -->
    <div class="modal fade" id="detailSiswaModal" tabindex="-1" aria-labelledby="detailSiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailSiswaModalLabel">Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailSiswaContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Siswa -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">
                            <i class="ri-upload-line me-2"></i>Import Data Siswa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="ri-information-line me-1"></i>Petunjuk Import
                            </h6>
                            <ul class="mb-0">
                                <li>Download template Excel terlebih dahulu</li>
                                <li>Isi data siswa sesuai format yang ada di template</li>
                                <li>Pastikan nama kelas sudah sesuai dengan yang ada di sistem</li>
                                <li>Format tanggal: DD/MM/YYYY (contoh: 15/08/2005)</li>
                                <li>Jenis kelamin: L atau P</li>
                                <li>Email harus unik dan belum terdaftar</li>
                                <li>NIS harus unik untuk setiap siswa</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept=".xlsx,.xls,.csv" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Format yang didukung: .xlsx, .xls, .csv (Maksimal 2MB)</small>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('siswa.template') }}" class="btn btn-outline-success">
                                <i class="ri-download-line me-1"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="importBtn">
                            <i class="ri-upload-line me-1"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Generate Kartu per Kelas -->
    <div class="modal fade" id="cardByClassModal" tabindex="-1" aria-labelledby="cardByClassModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cardByClassModalLabel">
                        <i class="ri-id-card-line me-2"></i>Generate Kartu per Kelas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="ri-information-line me-1"></i>Informasi
                        </h6>
                        <ul class="mb-0">
                            <li>Pilih kelas untuk generate kartu siswa secara massal</li>
                            <li>Semua siswa dalam kelas yang dipilih akan dibuatkan kartunya</li>
                            <li>Kartu dapat langsung dicetak atau disimpan</li>
                            <li>Format kartu akan otomatis menyesuaikan untuk pencetakan</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label for="kelasSelect" class="form-label">Pilih Kelas <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="kelasSelect" required>
                            <option value="">-- Pilih Kelas --</option>
                        </select>
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">Hanya kelas yang memiliki siswa yang akan ditampilkan</small>
                    </div>

                    <div id="classInfo" class="alert alert-light d-none">
                        <strong>Informasi Kelas:</strong>
                        <div id="classDetails"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="generateCardsBtn" disabled>
                        <i class="ri-id-card-line me-1"></i> Generate Kartu
                    </button>
                </div>
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
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        $(document).ready(function() {
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            // Load statistics
            function loadStatistics() {
                $.ajax({
                    url: "{{ route('siswa.statistics') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#totalSiswa').text(response.data.total_siswa);
                            $('#siswaLaki').text(response.data.siswa_laki);
                            $('#siswaPerempuan').text(response.data.siswa_perempuan);
                        }
                    },
                    error: function() {
                        alert('Gagal memuat data statistik');
                    }
                });
            }

            const table = $('#datatable1').DataTable({
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'Detail Siswa: ' + data.nama;
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
                    url: "{{ route('siswa.data') }}",
                    type: "GET",
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
                        data: 'jenis_kelamin',
                        name: 'jenis_kelamin',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 6
                    },
                    {
                        data: 'umur',
                        name: 'umur',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 7
                    },
                    {
                        data: 'no_telepon',
                        name: 'no_telepon',
                        orderable: false,
                        searchable: true,
                        responsivePriority: 8
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '120px',
                        className: 'text-center',
                        responsivePriority: 5
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
                    emptyTable: "Tidak ada data siswa tersedia",
                    loadingRecords: "Memuat data..."
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                initComplete: function() {
                    // Event handler untuk tombol view detail
                    $('#datatable1').on('click', '.view-btn', function() {
                        const id = $(this).data('id');
                        window.location.href = "{{ route('siswa.show', ':id') }}".replace(
                            ':id', id);
                    });

                    // Event handler untuk tombol edit
                    $('#datatable1').on('click', '.edit-btn', function() {
                        const id = $(this).data('id');
                        window.location.href = "{{ route('siswa.edit', ':id') }}".replace(
                            ':id', id);
                    });

                    // Event handler untuk tombol delete
                    $('#datatable1').on('click', '.delete-btn', function() {
                        const id = $(this).data('id');
                        const siswaName = $(this).closest('tr').find('td:eq(1)').text();

                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `Apakah Anda yakin ingin menghapus siswa <strong>${siswaName}</strong>?<br><small class="text-danger">Data yang dihapus tidak dapat dikembalikan dan akan menghapus akun user.</small>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Ya, Hapus!',
                            cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Sedang memproses...',
                                    html: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: "{{ route('siswa.destroy', ':id') }}"
                                        .replace(':id', id),
                                    type: 'DELETE',
                                    data: {
                                        "_token": "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        Swal.close();
                                        if (response.success) {
                                            Toast.fire({
                                                icon: 'success',
                                                title: response
                                                    .message ||
                                                    'Data siswa berhasil dihapus'
                                            });
                                            table.ajax.reload();
                                            loadStatistics
                                                (); // Refresh statistics
                                        } else {
                                            Toast.fire({
                                                icon: 'error',
                                                title: response
                                                    .message ||
                                                    'Gagal menghapus data'
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.close();
                                        let errorMessage =
                                            'Terjadi kesalahan pada server';
                                        if (xhr.responseJSON && xhr
                                            .responseJSON.message) {
                                            errorMessage = xhr.responseJSON
                                                .message;
                                        } else if (xhr.status === 404) {
                                            errorMessage =
                                                'Data siswa tidak ditemukan.';
                                        } else if (xhr.status === 403) {
                                            errorMessage =
                                                'Anda tidak memiliki izin untuk menghapus data ini.';
                                        } else if (xhr.status === 422) {
                                            errorMessage =
                                                'Data tidak dapat dihapus karena masih memiliki relasi dengan data lain.';
                                        }
                                        Toast.fire({
                                            icon: 'error',
                                            title: errorMessage
                                        });
                                    }
                                });
                            }
                        });
                    });

                    // Custom search dengan debounce
                    const searchInput = $('div.dataTables_filter input');
                    searchInput.unbind();
                    searchInput.bind('input', debounce(function(e) {
                        table.search(this.value).draw();
                    }, 400));

                    // Auto refresh functionality
                    let autoRefreshEnabled = true;
                    let autoRefreshInterval = 30000; // 30 seconds
                    let refreshTimer;

                    function startAutoRefresh() {
                        if (autoRefreshEnabled) {
                            refreshTimer = setInterval(function() {
                                if (autoRefreshEnabled) {
                                    table.ajax.reload(null, false);
                                    loadStatistics();
                                }
                            }, autoRefreshInterval);
                        }
                    }

                    function stopAutoRefresh() {
                        clearInterval(refreshTimer);
                    }

                    startAutoRefresh();

                    // Stop auto refresh on user interaction
                    $('#datatable1').on('page.dt search.dt order.dt', function() {
                        stopAutoRefresh();
                        autoRefreshEnabled = false;
                    });
                }
            });

            // Function to show detail siswa in modal
            function showDetailSiswa(id) {
                $('#detailSiswaContent').html(
                    '<div class="text-center"><div class="spinner-border"></div><p class="mt-2">Memuat data...</p></div>'
                );
                $('#detailSiswaModal').modal('show');

                $.ajax({
                    url: "{{ route('siswa.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const siswa = response.data;
                            const umur = siswa.tanggal_lahir ? new Date().getFullYear() - new Date(siswa
                                .tanggal_lahir).getFullYear() : '-';

                            $('#detailSiswaContent').html(`
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">Informasi Pribadi</h6>
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td width="40%"><strong>Nama Lengkap</strong></td>
                                                <td>: ${siswa.user ? siswa.user.name : '-'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>NIS</strong></td>
                                                <td>: ${siswa.nis}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email</strong></td>
                                                <td>: ${siswa.user ? siswa.user.email : '-'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jenis Kelamin</strong></td>
                                                <td>: ${siswa.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Lahir</strong></td>
                                                <td>: ${siswa.tanggal_lahir ? new Date(siswa.tanggal_lahir).toLocaleDateString('id-ID') : '-'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Umur</strong></td>
                                                <td>: ${umur} tahun</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">Informasi Sekolah</h6>
                                        <table class="table table-borderless table-sm">
                                            <tr>
                                                <td width="40%"><strong>Kelas</strong></td>
                                                <td>: ${siswa.kelas ? siswa.kelas.nama_kelas : 'Belum ada kelas'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Masuk</strong></td>
                                                <td>: ${siswa.tanggal_masuk ? new Date(siswa.tanggal_masuk).toLocaleDateString('id-ID') : '-'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>No. Telepon</strong></td>
                                                <td>: ${siswa.no_telepon}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Alamat</strong></td>
                                                <td>: ${siswa.alamat}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#detailSiswaContent').html(
                                '<div class="alert alert-danger">Gagal memuat data siswa</div>');
                        }
                    },
                    error: function() {
                        $('#detailSiswaContent').html(
                            '<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>'
                        );
                    }
                });
            }

            // Load initial statistics
            loadStatistics();

            // Handle import form submission
            $('#importForm').on('submit', function(e) {
                e.preventDefault();

                const fileInput = $('#file')[0];
                if (!fileInput.files.length) {
                    $('#file').addClass('is-invalid');
                    $('#file').next('.invalid-feedback').text('File harus dipilih');
                    return;
                }

                const file = fileInput.files[0];
                const allowedTypes = ['application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'
                ];

                if (!allowedTypes.includes(file.type)) {
                    $('#file').addClass('is-invalid');
                    $('#file').next('.invalid-feedback').text(
                        'Format file tidak valid. Gunakan .xlsx, .xls, atau .csv');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) { // 2MB
                    $('#file').addClass('is-invalid');
                    $('#file').next('.invalid-feedback').text('Ukuran file maksimal 2MB');
                    return;
                }

                // Show loading
                $('#importBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Importing...'
                );

                // Submit form
                this.submit();
            });

            // Remove validation on file change
            $('#file').on('change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').text('');
            });

            // Handle card generation modal
            $('#cardByClassModal').on('show.bs.modal', function() {
                // Load available classes
                $.ajax({
                    url: "{{ route('siswa.available-classes') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let options = '<option value="">-- Pilih Kelas --</option>';
                            response.data.forEach(function(kelas) {
                                options += '<option value="' + kelas.id +
                                    '" data-count="' + kelas.siswas_count + '">' + kelas
                                    .nama_kelas + ' (' + kelas.siswas_count +
                                    ' siswa)</option>';
                            });
                            $('#kelasSelect').html(options);
                        } else {
                            $('#kelasSelect').html(
                                '<option value="">Tidak ada kelas dengan siswa</option>');
                        }
                    },
                    error: function() {
                        $('#kelasSelect').html('<option value="">Error loading data</option>');
                    }
                });
            });

            // Handle class selection change
            $('#kelasSelect').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const kelasId = $(this).val();
                const studentCount = selectedOption.data('count');
                const kelasName = selectedOption.text();

                if (kelasId) {
                    $('#classInfo').removeClass('d-none');
                    $('#classDetails').html(
                        '<div class="row">' +
                        '<div class="col-6"><strong>Nama Kelas:</strong></div>' +
                        '<div class="col-6">' + kelasName.split('(')[0].trim() + '</div>' +
                        '<div class="col-6"><strong>Jumlah Siswa:</strong></div>' +
                        '<div class="col-6">' + studentCount + ' siswa</div>' +
                        '</div>'
                    );
                    $('#generateCardsBtn').prop('disabled', false);
                } else {
                    $('#classInfo').addClass('d-none');
                    $('#generateCardsBtn').prop('disabled', true);
                }

                // Remove validation
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').text('');
            });

            // Handle generate cards button
            $('#generateCardsBtn').on('click', function() {
                const kelasId = $('#kelasSelect').val();

                if (!kelasId) {
                    $('#kelasSelect').addClass('is-invalid');
                    $('#kelasSelect').next('.invalid-feedback').text('Kelas harus dipilih');
                    return;
                }

                // Show loading
                $(this).prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Generating...'
                );

                // Redirect to cards generation page
                window.location.href = "{{ route('siswa.cards.class', ':kelasId') }}".replace(':kelasId',
                    kelasId);
            });

            // Reset modal on close
            $('#cardByClassModal').on('hidden.bs.modal', function() {
                $('#kelasSelect').val('').removeClass('is-invalid');
                $('#kelasSelect').next('.invalid-feedback').text('');
                $('#classInfo').addClass('d-none');
                $('#generateCardsBtn').prop('disabled', true).html(
                    '<i class="ri-id-card-line me-1"></i> Generate Kartu'
                );
            });

            // Handle flash messages dari session
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: {!! json_encode(session('error')) !!}
                });
            @endif

            @if (session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: {!! json_encode(session('warning')) !!}
                });
            @endif
        });
    </script>
@endpush
