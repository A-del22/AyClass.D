@extends('layouts.app')
@section('title', 'Siswa Kelas ' . $kelas->nama_kelas)
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

        .class-info-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .class-stat {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0;
        }

        .class-stat h3 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .class-stat p {
            font-size: 1rem;
            color: #6c757d;
            margin: 0;
        }

        .class-details {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .class-details h5 {
            margin-bottom: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .class-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e9ecef;
        }

        .class-detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .class-detail-label {
            font-weight: 500;
            color: #6c757d;
        }

        .class-detail-value {
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Siswa Kelas {{ $kelas->nama_kelas }}" />

        <!-- Class Information Card -->
        <div class="class-info-card card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="class-stat">
                            <h3 id="totalSiswa">0</h3>
                            <p>Total Siswa</p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="class-details">
                            <h5><i class="ri-school-line me-2"></i>Informasi Kelas</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="class-detail-item">
                                        <span class="class-detail-label">Nama Kelas</span>
                                        <span class="class-detail-value">{{ $kelas->nama_kelas }}</span>
                                    </div>
                                    @if ($kelas->tingkatKelas)
                                        <div class="class-detail-item">
                                            <span class="class-detail-label">Tingkat</span>
                                            <span class="class-detail-value">{{ $kelas->tingkatKelas->tingkat }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="class-detail-item">
                                        <span class="class-detail-label">Wali Kelas</span>
                                        <span class="class-detail-value">{{ $kelas->wali_kelas ?? '-' }}</span>
                                    </div>
                                    @if ($kelas->jurusan)
                                        <div class="class-detail-item">
                                            <span class="class-detail-label">Jurusan</span>
                                            <span class="class-detail-value">{{ $kelas->jurusan->nama_jurusan }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="header-title">Daftar Siswa Kelas {{ $kelas->nama_kelas }}</h4>
                            <p class="text-muted mb-0">
                                Semua siswa yang terdaftar di kelas {{ $kelas->nama_kelas }}.
                                Data dapat diurutkan, dicari, dan difilter sesuai kebutuhan.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                            <a href="{{ route('siswa.cards.class', $kelas->id) }}" class="btn btn-info">
                                <i class="ri-id-card-line me-1"></i> Kartu Siswa
                            </a>
                            <button type="button" class="btn btn-danger" id="deleteAllBtn">
                                <i class="ri-delete-bin-line me-1"></i> Hapus Semua Siswa
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama</th>
                                        <th width="15%">NIS</th>
                                        <th width="10%">L/P</th>
                                        <th width="10%">Umur</th>
                                        <th width="20%">No. Telepon</th>
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
                pageLength: 25,
                ajax: {
                    url: "{{ route('siswa.by-class.data', $kelas->id) }}",
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
                        data: 'jenis_kelamin',
                        name: 'jenis_kelamin',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 5
                    },
                    {
                        data: 'umur',
                        name: 'umur',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 6
                    },
                    {
                        data: 'no_telepon',
                        name: 'no_telepon',
                        orderable: false,
                        searchable: true,
                        responsivePriority: 7
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '120px',
                        className: 'text-center',
                        responsivePriority: 4
                    }
                ],
                orderCellsTop: true,
                order: [
                    [2, 'asc'] // Order by NIS
                ],
                searchDelay: 400,
                stateSave: false,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: "Memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada siswa di kelas ini",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 siswa",
                    infoFiltered: "(disaring dari _MAX_ total siswa)",
                    search: "Cari:",
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'></i>",
                        next: "<i class='ri-arrow-right-s-line'></i>",
                        first: "Pertama",
                        last: "Terakhir"
                    },
                    emptyTable: "Tidak ada siswa di kelas {{ $kelas->nama_kelas }}",
                    loadingRecords: "Memuat data..."
                },
                drawCallback: function(settings) {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");

                    // Update total count
                    $('#totalSiswa').text(settings.json.recordsTotal || 0);
                },
                initComplete: function() {
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
                            html: 'Apakah Anda yakin ingin menghapus siswa <strong>' +
                                siswaName +
                                '</strong>?<br><small class="text-danger">Data yang dihapus tidak dapat dikembalikan dan akan menghapus akun user.</small>',
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
                }
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

            // Handle delete all button
            $('#deleteAllBtn').on('click', function() {
                const kelasName = '{{ $kelas->nama_kelas }}';
                const totalSiswa = $('#totalSiswa').text();

                Swal.fire({
                    title: 'Konfirmasi Hapus Semua Siswa',
                    html: '<div class="text-start">' +
                        '<p>Apakah Anda yakin ingin menghapus <strong>SEMUA ' + totalSiswa + ' siswa</strong> dari kelas <strong>' + kelasName + '</strong>?</p>' +
                        '<div class="alert alert-danger mt-3 mb-0">' +
                        '<i class="ri-alert-line me-2"></i>' +
                        '<strong>PERINGATAN:</strong>' +
                        '<ul class="mb-0 mt-2">' +
                        '<li>Data yang dihapus tidak dapat dikembalikan</li>' +
                        '<li>Semua akun user siswa akan dihapus</li>' +
                        '<li>Semua data absensi siswa akan ikut terhapus</li>' +
                        '</ul>' +
                        '</div>' +
                        '</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Ya, Hapus Semua!',
                    cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-wide'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Konfirmasi kedua
                        Swal.fire({
                            title: 'Konfirmasi Terakhir',
                            html: '<p class="text-danger fw-bold">Apakah Anda BENAR-BENAR yakin?</p>' +
                                '<p>Ketik <strong>"HAPUS SEMUA"</strong> untuk melanjutkan:</p>' +
                                '<input type="text" id="confirmText" class="form-control mt-2" placeholder="Ketik HAPUS SEMUA">',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Hapus',
                            cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
                            reverseButtons: true,
                            preConfirm: () => {
                                const confirmText = document.getElementById('confirmText').value;
                                if (confirmText !== 'HAPUS SEMUA') {
                                    Swal.showValidationMessage('Teks konfirmasi tidak sesuai!');
                                    return false;
                                }
                                return true;
                            }
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                Swal.fire({
                                    title: 'Menghapus semua siswa...',
                                    html: 'Mohon tunggu, proses ini mungkin memakan waktu',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: "{{ route('siswa.by-class.delete-all', $kelas->id) }}",
                                    type: 'DELETE',
                                    data: {
                                        "_token": "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        Swal.close();
                                        if (response.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                html: response.message || 'Semua siswa berhasil dihapus',
                                                timer: 3000,
                                                timerProgressBar: true
                                            }).then(() => {
                                                // Reload table
                                                table.ajax.reload();
                                            });
                                        } else {
                                            Toast.fire({
                                                icon: 'error',
                                                title: response.message || 'Gagal menghapus siswa'
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.close();
                                        let errorMessage = 'Terjadi kesalahan pada server';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage = xhr.responseJSON.message;
                                        } else if (xhr.status === 404) {
                                            errorMessage = 'Tidak ada siswa di kelas ini.';
                                        } else if (xhr.status === 403) {
                                            errorMessage = 'Anda tidak memiliki izin untuk menghapus data ini.';
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
                });
            });
        });
    </script>

    <style>
        .swal-wide {
            width: 600px !important;
        }
    </style>
@endpush
