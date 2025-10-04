@extends('layouts.app')
@section('title', 'Data Guru')
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
        <x-breadcrumbs title="Data Guru" />

        <!-- Statistics Cards -->
        <div class="row mb-3" id="statisticsCards">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-group-line text-primary" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="totalGuru">0</h3>
                        <p class="text-muted mb-0">Total Guru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-user-add-line text-success" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-1" id="guruBulanIni">0</h3>
                        <p class="text-muted mb-0">Guru Bulan Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">Data Guru</h4>
                            <p class="text-muted mb-0">
                                Daftar semua guru yang terdaftar di sekolah beserta informasi akun dan data pribadi.
                                Data dapat diurutkan, dicari, dan difilter sesuai kebutuhan.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Tambah Guru
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Nama</th>
                                        <th width="30%">Email</th>
                                        <th width="20%">Tanggal Dibuat</th>
                                        <th width="15%">Aksi</th>
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

        // Load statistics function (defined outside document ready)
        function loadStatistics() {
            $.ajax({
                url: "{{ route('guru.statistics') }}",
                type: 'GET',
                success: function(response) {
                    $('#totalGuru').text(response.total_guru);
                    $('#guruBulanIni').text(response.guru_bulan_ini);
                },
                error: function() {
                    alert('Gagal memuat data statistik');
                }
            });
        }

        $(document).ready(function() {
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const table = $('#datatable1').DataTable({
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'Detail Guru: ' + data.nama;
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
                    url: "{{ route('guru.data') }}",
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
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        searchable: true,
                        responsivePriority: 3
                    },
                    {
                        data: 'tanggal_dibuat',
                        name: 'tanggal_dibuat',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 4
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
                    emptyTable: "Tidak ada data guru tersedia",
                    loadingRecords: "Memuat data..."
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                initComplete: function() {
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

            // Load initial statistics
            loadStatistics();

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

        // Delete guru function
        function deleteGuru(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus data guru ini?<br><small class="text-danger">Data yang dihapus tidak dapat dikembalikan.</small>`,
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
                        url: "{{ route('guru.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message || 'Data guru berhasil dihapus'
                                });
                                $('#datatable1').DataTable().ajax.reload();
                                loadStatistics();
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.message || 'Gagal menghapus data'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            let errorMessage = 'Terjadi kesalahan pada server';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 404) {
                                errorMessage = 'Data guru tidak ditemukan.';
                            } else if (xhr.status === 403) {
                                errorMessage = 'Anda tidak memiliki izin untuk menghapus data ini.';
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
        }
    </script>
@endpush
