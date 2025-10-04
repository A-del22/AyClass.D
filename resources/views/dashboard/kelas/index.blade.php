@extends('layouts.app')
@section('title', 'Data Kelas')
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
        <x-breadcrumbs title="Data Kelas" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">Data Kelas</h4>
                            <p class="text-muted mb-0">
                                Daftar semua kelas yang tersedia di sekolah beserta wali kelas masing-masing.
                                Data dapat diurutkan, dicari, dan difilter sesuai kebutuhan.
                            </p>
                        </div>
                        <a href="{{ route('kelas.add') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Kelola Kelas
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Kelas</th>
                                        <th width="10%">Jumlah Siswa</th>
                                        <th width="30%">Wali Kelas</th>
                                        <th width="20%">Update Pada</th>
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
                                return 'Detail Kelas: ' + data.kelas;
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
                    url: "{{ route('kelas.data') }}",
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
                        data: 'nama_kelas_link',
                        name: 'nama_kelas',
                        orderable: false,
                        searchable: true,
                        className: 'fw-medium',
                        responsivePriority: 2
                    },
                    {
                        data: 'jumlah_siswa',
                        name: 'jumlah_siswa',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 3
                    },
                    {
                        data: 'wali_kelas',
                        name: 'wali_kelas',
                        orderable: false,
                        searchable: true,
                        responsivePriority: 4
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: true,
                        searchable: false,
                        responsivePriority: 5,
                        render: function(data, type, row) {
                            if (data) {
                                var date = new Date(data);
                                return '<span class="text-muted small">' + date.toLocaleDateString(
                                    'id-ID', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) + '</span>';
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '120px',
                        className: 'text-center',
                        responsivePriority: 3
                    }
                ],
                orderCellsTop: true,
                order: [
                    [3, 'desc']
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
                    emptyTable: "Tidak ada data kelas tersedia",
                    loadingRecords: "Memuat data..."
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                initComplete: function() {
                    // Event handler untuk tombol edit
                    $('#datatable1').on('click', '.edit-btn', function() {
                        const id = $(this).data('id');
                        window.location.href = "{{ route('kelas.edit', ':id') }}".replace(
                            ':id', id);
                    });

                    // Event handler untuk tombol delete
                    $('#datatable1').on('click', '.delete-btn', function() {
                        const id = $(this).data('id');
                        const kelasName = $(this).closest('tr').find('td:eq(1)').text();

                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `Apakah Anda yakin ingin menghapus kelas <strong>${kelasName}</strong>?<br><small class="text-danger">Data yang dihapus tidak dapat dikembalikan.</small>`,
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
                                    url: "{{ route('kelas.destroy', ':id') }}"
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
                                                    'Data kelas berhasil dihapus'
                                            });
                                            table.ajax.reload();
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
                                                'Data kelas tidak ditemukan.';
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

            // Handle flash messages dari session
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif
        });
    </script>
@endpush
