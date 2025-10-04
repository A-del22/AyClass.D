@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Tambah Siswa" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Tambah Siswa Baru</h4>
                        <p class="text-muted mb-0">
                            Isi semua data siswa dengan lengkap dan benar. Sistem akan otomatis membuat akun user untuk
                            siswa.
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('siswa.store') }}" method="POST" id="formTambahSiswa" enctype="multipart/form-data">
                            @csrf

                            <!-- Informasi Akun -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="ri-user-settings-line me-2"></i>Informasi Akun
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Masukkan nama lengkap siswa" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="Masukkan email siswa" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Email akan digunakan untuk login siswa (password default:
                                            123)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Siswa -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-success mb-3">
                                        <i class="ri-graduation-cap-line me-2"></i>Informasi Siswa
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nis" class="form-label">NIS (Nomor Induk Siswa) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nis') is-invalid @enderror"
                                            id="nis" name="nis" value="{{ old('nis') }}"
                                            placeholder="Masukkan NIS siswa" required>
                                        @error('nis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kelas_id" class="form-label">Kelas <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id"
                                            name="kelas_id" required>
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}"
                                                    {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                                    {{ $kelas->nama_kelas }} - {{ $kelas->wali_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kelas_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Pilih kelas tempat siswa akan ditempatkan</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Siswa -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-image-line me-2"></i>Foto Siswa
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="foto" class="form-label">Foto Siswa</label>
                                        <input type="file" class="form-control @error('foto') is-invalid @enderror"
                                            id="foto" name="foto" accept="image/*">
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF (Maks. 2MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Preview Foto</label>
                                        <div class="text-center">
                                            <img id="fotoPreview" src="{{ asset('avatar.png') }}"
                                                alt="Preview Foto" class="img-thumbnail"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Pribadi -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-info mb-3">
                                        <i class="ri-user-line me-2"></i>Informasi Pribadi
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                            id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                            required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                                Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_telepon" class="form-label">Nomor Telepon <span
                                                class="text-danger">*</span></label>
                                        <input type="tel"
                                            class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon"
                                            name="no_telepon" value="{{ old('no_telepon') }}"
                                            placeholder="Contoh: 081234567890" required>
                                        @error('no_telepon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                            id="tanggal_masuk" name="tanggal_masuk"
                                            value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                                        @error('tanggal_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat Lengkap <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                                            placeholder="Masukkan alamat lengkap siswa" required>{{ old('alamat') }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Tambahan -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="ri-information-line me-1"></i>Informasi Penting
                                        </h6>
                                        <ul class="mb-0">
                                            <li>Password default untuk akun siswa adalah <strong>123</strong></li>
                                            <li>Siswa dapat mengubah password setelah login pertama kali</li>
                                            <li>Email yang dimasukkan harus unik dan belum terdaftar</li>
                                            <li>NIS harus unik untuk setiap siswa</li>
                                            <li>Kelas harus dipilih sebelum menyimpan data siswa</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ri-save-line me-1"></i> Simpan Data Siswa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toast notification setup
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

            // Auto-generate email from name
            $('#name').on('input', function() {
                const name = $(this).val().toLowerCase().replace(/\s+/g, '');
                if (name && !$('#email').val()) {
                    const randomNum = Math.floor(Math.random() * 1000);
                    $('#email').val(name + randomNum + '@student.sch.id');
                }
            });

            // Auto-calculate age when birth date changes
            $('#tanggal_lahir').on('change', function() {
                const birthDate = new Date($(this).val());
                const today = new Date();
                const age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                if (age > 0) {
                    $(this).next('.form-text').remove();
                    $(this).after(`<small class="form-text text-muted">Umur: ${age} tahun</small>`);
                }
            });

            // Foto preview functionality
            $('#foto').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#fotoPreview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#fotoPreview').attr('src', '{{ asset('avatar.png') }}');
                }
            });

            // Form validation dan submit
            $('#formTambahSiswa').on('submit', function(e) {
                e.preventDefault();

                // Client-side validation
                const requiredFields = ['name', 'email', 'nis', 'kelas_id', 'tanggal_lahir', 'jenis_kelamin', 'alamat',
                    'no_telepon', 'tanggal_masuk'
                ];
                let isValid = true;

                requiredFields.forEach(field => {
                    const value = $(`#${field}`).val().trim();
                    if (!value) {
                        isValid = false;
                        $(`#${field}`).addClass('is-invalid');
                        if (!$(`#${field}`).next('.invalid-feedback').length) {
                            $(`#${field}`).after(
                                '<div class="invalid-feedback">Field ini harus diisi</div>');
                        }
                    } else {
                        $(`#${field}`).removeClass('is-invalid');
                        $(`#${field}`).next('.invalid-feedback').remove();
                    }
                });

                // Email validation
                const email = $('#email').val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email)) {
                    isValid = false;
                    $('#email').addClass('is-invalid');
                    $('#email').next('.invalid-feedback').remove();
                    $('#email').after('<div class="invalid-feedback">Format email tidak valid</div>');
                }

                // Phone number validation
                const phone = $('#no_telepon').val();
                const phoneRegex = /^[0-9+\-\s]+$/;
                if (phone && !phoneRegex.test(phone)) {
                    isValid = false;
                    $('#no_telepon').addClass('is-invalid');
                    $('#no_telepon').next('.invalid-feedback').remove();
                    $('#no_telepon').after(
                        '<div class="invalid-feedback">Nomor telepon hanya boleh berisi angka, +, -, dan spasi</div>'
                    );
                }

                // Birth date validation
                const birthDate = new Date($('#tanggal_lahir').val());
                const today = new Date();
                if (birthDate >= today) {
                    isValid = false;
                    $('#tanggal_lahir').addClass('is-invalid');
                    $('#tanggal_lahir').next('.invalid-feedback').remove();
                    $('#tanggal_lahir').after(
                        '<div class="invalid-feedback">Tanggal lahir harus sebelum hari ini</div>');
                }

                if (!isValid) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Mohon periksa kembali data yang diisi!'
                    });
                    return;
                }

                // Confirmation before submit
                const nama = $('#name').val();
                const nis = $('#nis').val();
                const kelas = $('#kelas_id option:selected').text();

                Swal.fire({
                    title: 'Konfirmasi Tambah Siswa',
                    html: `
                        <div class="text-start">
                            <p><strong>Nama:</strong> ${nama}</p>
                            <p><strong>NIS:</strong> ${nis}</p>
                            <p><strong>Email:</strong> ${$('#email').val()}</p>
                            <p><strong>Kelas:</strong> ${kelas !== 'Pilih Kelas (Opsional)' ? kelas : 'Belum ditentukan'}</p>
                        </div>
                        <small class="text-muted">Pastikan data sudah benar sebelum menyimpan.</small>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-save-line me-1"></i>Ya, Simpan!',
                    cancelButtonText: '<i class="ri-close-line me-1"></i>Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        $('#submitBtn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Menyimpan...'
                        );

                        // Submit form
                        this.submit();
                    }
                });
            });

            // Remove invalid class on input
            $('.form-control, .form-select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });

            // Handle flash messages
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

            // Handle validation errors
            @if ($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: 'Terdapat kesalahan pada form!'
                });
            @endif
        });
    </script>
@endpush
