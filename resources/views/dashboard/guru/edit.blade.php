@extends('layouts.app')
@section('title', 'Edit Guru')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Edit Guru" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Edit Data Guru</h4>
                        <p class="text-muted mb-0">
                            Perbarui data guru dengan informasi yang benar. Kosongkan password jika tidak ingin mengubahnya.
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guru.update', $guru->id) }}" method="POST" id="formEditGuru">
                            @csrf
                            @method('PUT')

                            <!-- Informasi Akun -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="ri-user-settings-line me-2"></i>Informasi Akun
                                    </h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $guru->name) }}"
                                            placeholder="Masukkan nama lengkap guru" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $guru->email) }}"
                                            placeholder="Masukkan email guru" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Email akan digunakan untuk login guru</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Password -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-warning mb-3">
                                        <i class="ri-lock-line me-2"></i>Ubah Password (Opsional)
                                    </h5>
                                    <div class="alert alert-info">
                                        <i class="ri-information-line me-1"></i>
                                        Kosongkan kedua field password jika tidak ingin mengubah password.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" placeholder="Masukkan password baru">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="ri-eye-line" id="togglePasswordIcon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Password minimal 8 karakter</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password
                                            Baru</label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" name="password_confirmation"
                                                placeholder="Konfirmasi password baru">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="togglePasswordConfirmation">
                                                <i class="ri-eye-line" id="togglePasswordConfirmationIcon"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Ulangi password baru yang sama</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('guru.index') }}" class="btn btn-light">
                                            <i class="ri-arrow-left-line me-1"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="ri-save-line me-1"></i> Perbarui Data
                                        </button>
                                    </div>
                                </div>
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
            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const passwordIcon = $('#togglePasswordIcon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    passwordIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    passwordField.attr('type', 'password');
                    passwordIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            $('#togglePasswordConfirmation').on('click', function() {
                const passwordField = $('#password_confirmation');
                const passwordIcon = $('#togglePasswordConfirmationIcon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    passwordIcon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    passwordField.attr('type', 'password');
                    passwordIcon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            // Form submission handling
            $('#formEditGuru').on('submit', function() {
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Memperbarui...'
                );
            });

            // Real-time password confirmation validation
            $('#password_confirmation').on('input', function() {
                const password = $('#password').val();
                const passwordConfirmation = $(this).val();

                // Only validate if both fields have values
                if (password || passwordConfirmation) {
                    if (password !== passwordConfirmation) {
                        $(this).addClass('is-invalid');
                        $(this).next('.input-group').next('.invalid-feedback').remove();
                        $(this).parent().after(
                            '<div class="invalid-feedback">Password konfirmasi tidak sesuai</div>');
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).parent().next('.invalid-feedback').remove();
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).parent().next('.invalid-feedback').remove();
                }
            });

            // Clear password confirmation when password is cleared
            $('#password').on('input', function() {
                if (!$(this).val()) {
                    $('#password_confirmation').val('').removeClass('is-invalid');
                    $('#password_confirmation').parent().next('.invalid-feedback').remove();
                }
            });

            // Auto-format email
            $('#email').on('blur', function() {
                let email = $(this).val().toLowerCase().trim();
                $(this).val(email);
            });

            // Capitalize name
            $('#name').on('blur', function() {
                let name = $(this).val();
                // Capitalize each word
                name = name.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
                $(this).val(name);
            });
        });
    </script>
@endpush
