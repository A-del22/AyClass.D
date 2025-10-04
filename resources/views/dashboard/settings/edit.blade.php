@extends('layouts.app')
@section('title', 'Edit Pengaturan Aplikasi')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Edit Pengaturan Aplikasi" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Edit Pengaturan Aplikasi</h4>
                        <p class="text-muted mb-0">
                            Perbarui informasi dan pengaturan aplikasi sekolah
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST" id="formEditSettings"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Logo Preview -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="ri-image-line me-2"></i>Logo Sekolah
                                    </h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="border rounded p-3 bg-light mb-3">
                                            <img id="logoPreview" src="{{ asset('logo.png') }}" alt="Logo Sekolah"
                                                class="img-fluid" style="max-height: 200px;">
                                        </div>
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Upload Logo Baru</label>
                                            <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                                id="logo" name="logo" accept="image/png,image/jpg,image/jpeg">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Format: PNG, JPG, JPEG. Maksimal 2MB</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Fields -->
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Aplikasi <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $setting->name ?? '') }}"
                                            placeholder="Masukkan nama aplikasi" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Deskripsi <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3" placeholder="Masukkan deskripsi aplikasi" required>{{ old('description', $setting->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="url" class="form-label">URL Aplikasi <span
                                                class="text-danger">*</span></label>
                                        <input type="url" class="form-control @error('url') is-invalid @enderror"
                                            id="url" name="url" value="{{ old('url', $setting->url ?? '') }}"
                                            placeholder="https://example.com" required>
                                        @error('url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- School Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="ri-school-line me-2"></i>Informasi Sekolah
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_sekolah" class="form-label">Nama Sekolah <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('nama_sekolah') is-invalid @enderror"
                                            id="nama_sekolah" name="nama_sekolah"
                                            value="{{ old('nama_sekolah', $setting->nama_sekolah ?? '') }}"
                                            placeholder="Masukkan nama sekolah" required>
                                        @error('nama_sekolah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alamat_sekolah" class="form-label">Alamat Sekolah <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('alamat_sekolah') is-invalid @enderror"
                                            id="alamat_sekolah" name="alamat_sekolah"
                                            value="{{ old('alamat_sekolah', $setting->alamat_sekolah ?? '') }}"
                                            placeholder="Masukkan alamat sekolah" required>
                                        @error('alamat_sekolah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                                            <i class="ri-close-line me-1"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="ri-save-line me-1"></i> Simpan Pengaturan
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

        // Logo preview
        $('#logo').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                if (file.size > 2048000) { // 2MB
                    Toast.fire({
                        icon: 'error',
                        title: 'Ukuran file terlalu besar! Maksimal 2MB'
                    });
                    $(this).val('');
                    return;
                }

                // Validate file type
                if (!['image/png', 'image/jpg', 'image/jpeg'].includes(file.type)) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Format file tidak valid! Gunakan PNG, JPG, atau JPEG'
                    });
                    $(this).val('');
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoPreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        $('#formEditSettings').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan pengaturan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Simpan!',
                cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    this.submit();
                }
            });
        });

        // Handle flash messages
        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: {!! json_encode(session('error')) !!}
            });
        @endif
    </script>
@endpush
