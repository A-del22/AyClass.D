@extends('layouts.app')
@section('title', 'Ubah Password')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Ubah Password" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Ubah Password</h4>
                        <p class="text-muted mb-0">
                            Ubah password akun Anda untuk meningkatkan keamanan
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('student.update-password') }}" method="POST" id="formChangePassword">
                            @csrf
                            @method('PUT')

                            <!-- Info Alert -->
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="ri-information-line me-1"></i>
                                <strong>Tips Keamanan:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                                    <li>Minimal 8 karakter</li>
                                    <li>Jangan gunakan password yang mudah ditebak</li>
                                    <li>Jangan gunakan password yang sama dengan akun lain</li>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    Password Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password"
                                        placeholder="Masukkan password saat ini" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                        <i class="ri-eye-line" id="toggleCurrentPasswordIcon"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                        id="new_password" name="new_password" placeholder="Masukkan password baru" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="ri-eye-line" id="toggleNewPasswordIcon"></i>
                                    </button>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Password minimal 8 karakter</small>

                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="passwordStrength" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="passwordStrengthText"></small>
                                </div>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">
                                    Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                        id="new_password_confirmation" name="new_password_confirmation"
                                        placeholder="Ulangi password baru" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="ri-eye-line" id="toggleConfirmPasswordIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Ulangi password baru yang sama</small>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                                    <i class="ri-close-line me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ri-lock-password-line me-1"></i> Ubah Password
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

        // Toggle password visibility
        $('#toggleCurrentPassword').on('click', function() {
            const input = $('#current_password');
            const icon = $('#toggleCurrentPasswordIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
            }
        });

        $('#toggleNewPassword').on('click', function() {
            const input = $('#new_password');
            const icon = $('#toggleNewPasswordIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
            }
        });

        $('#toggleConfirmPassword').on('click', function() {
            const input = $('#new_password_confirmation');
            const icon = $('#toggleConfirmPasswordIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
            }
        });

        // Password strength checker
        $('#new_password').on('input', function() {
            const password = $(this).val();
            const strengthBar = $('#passwordStrength');
            const strengthText = $('#passwordStrengthText');

            if (password.length === 0) {
                strengthBar.css('width', '0%').removeClass().addClass('progress-bar');
                strengthText.text('');
                return;
            }

            let strength = 0;
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            strength = Object.values(checks).filter(Boolean).length;

            const colors = ['bg-danger', 'bg-danger', 'bg-warning', 'bg-info', 'bg-success', 'bg-success'];
            const texts = ['Sangat Lemah', 'Lemah', 'Cukup', 'Baik', 'Kuat', 'Sangat Kuat'];
            const widths = [20, 40, 60, 80, 100, 100];

            strengthBar.css('width', widths[strength] + '%')
                .removeClass()
                .addClass('progress-bar ' + colors[strength]);
            strengthText.text(texts[strength]);
        });

        // Form submission
        $('#formChangePassword').on('submit', function(e) {
            e.preventDefault();

            const newPassword = $('#new_password').val();
            const confirmPassword = $('#new_password_confirmation').val();

            if (newPassword !== confirmPassword) {
                Toast.fire({
                    icon: 'error',
                    title: 'Konfirmasi password tidak cocok'
                });
                return;
            }

            if (newPassword.length < 8) {
                Toast.fire({
                    icon: 'error',
                    title: 'Password minimal 8 karakter'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengubah password?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Ubah!',
                cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    this.submit();
                }
            });
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
    </script>
@endpush
