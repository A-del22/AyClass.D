@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Edit Siswa" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Edit Data Siswa</h4>
                        <p class="text-muted mb-0">
                            Edit data siswa {{ $siswa->user->name }}. Pastikan semua informasi sudah benar sebelum menyimpan
                            perubahan.
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" id="formEditSiswa"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

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
                                            id="name" name="name" value="{{ old('name', $siswa->user->name) }}"
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
                                            id="email" name="email" value="{{ old('email', $siswa->user->email) }}"
                                            placeholder="Masukkan email siswa" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Email digunakan untuk login siswa</small>
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
                                            id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}"
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
                                                    {{ old('kelas_id', $siswa->kelas_id) == $kelas->id ? 'selected' : '' }}>
                                                    {{ $kelas->nama_kelas }} - {{ $kelas->wali_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kelas_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Kelas saat ini:
                                            {{ $siswa->kelas ? $siswa->kelas->nama_kelas : 'Belum ada kelas' }}</small>
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
                                            <img id="fotoPreview"
                                                src="{{ $siswa->foto && $siswa->foto != 'avatar.png' ? asset('storage/' . $siswa->foto) : asset('avatar.png') }}"
                                                alt="Preview Foto" class="img-thumbnail"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                        </div>
                                        <small class="text-muted text-center d-block mt-2">
                                            Foto saat ini: {{ $siswa->foto }}
                                        </small>
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
                                            id="tanggal_lahir" name="tanggal_lahir"
                                            value="{{ old('tanggal_lahir', $siswa->tanggal_lahir->format('Y-m-d')) }}"
                                            required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Umur saat ini: {{ $siswa->umur }} tahun</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L"
                                                {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>
                                                Laki-laki</option>
                                            <option value="P"
                                                {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>
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
                                            name="no_telepon" value="{{ old('no_telepon', $siswa->no_telepon) }}"
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
                                            value="{{ old('tanggal_masuk', $siswa->tanggal_masuk->format('Y-m-d')) }}"
                                            required>
                                        @error('tanggal_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Lama bersekolah: {{ $siswa->lama_bersekolah }}
                                            tahun</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat Lengkap <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                                            placeholder="Masukkan alamat lengkap siswa" required>{{ old('alamat', $siswa->alamat) }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Tambahan -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading">
                                            <i class="ri-alert-line me-1"></i>Informasi Penting
                                        </h6>
                                        <ul class="mb-0">
                                            <li>Perubahan email akan mempengaruhi akun login siswa</li>
                                            <li>Perubahan NIS harus dipastikan tidak conflict dengan siswa lain</li>
                                            <li>Password siswa tidak akan berubah kecuali direset manual</li>
                                            <li>Perubahan kelas akan mempengaruhi data akademik siswa</li>
                                            <li>Kelas harus dipilih dan tidak boleh kosong</li>
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
                                    <i class="ri-save-line me-1"></i> Update Data Siswa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Current Data Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Data Saat Ini</h6>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-2">
                                    <i class="ri-user-3-line text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">{{ $siswa->user->name }}</h6>
                                    <p class="text-muted small mb-0">Nama Siswa</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-2">
                                    <i class="ri-profile-line text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">{{ $siswa->nis }}</h6>
                                    <p class="text-muted small mb-0">NIS</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-2">
                                    <i class="ri-school-line text-info" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">
                                        {{ $siswa->kelas ? $siswa->kelas->nama_kelas : 'Belum ada kelas' }}</h6>
                                    <p class="text-muted small mb-0">Kelas</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-2">
                                    @if ($siswa->jenis_kelamin === 'L')
                                        <i class="ri-user-line text-primary" style="font-size: 2rem;"></i>
                                    @else
                                        <i class="ri-user-2-line text-danger" style="font-size: 2rem;"></i>
                                    @endif
                                    <h6 class="mt-2 mb-1">{{ $siswa->jenis_kelamin_lengkap }}</h6>
                                    <p class="text-muted small mb-0">{{ $siswa->umur }} tahun</p>
                                </div>
                            </div>
                        </div>
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

            // Auto-calculate age when birth date changes
            $('#tanggal_lahir').on('change', function() {
                const birthDate = new Date($(this).val());
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                if (age > 0) {
                    $(this).siblings('.text-muted').text(`Umur akan menjadi: ${age} tahun`);
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
                }
            });

            // Form validation dan submit
            $('#formEditSiswa').on('submit', function(e) {
                e.preventDefault();

                // Client-side validation
                const requiredFields = ['name', 'email', 'nis', 'kelas_id', 'tanggal_lahir',
                    'jenis_kelamin', 'alamat',
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
                const currentNama = "{{ $siswa->user->name }}";

                Swal.fire({
                    title: 'Konfirmasi Update Data Siswa',
                    html: `
                        <div class="text-start">
                            ${nama !== currentNama ?
                                `<p><strong>Nama:</strong> ${currentNama} → ${nama}</p>` :
                                `<p><strong>Nama:</strong> ${nama}</p>`
                            }
                            <p><strong>NIS:</strong> ${nis}</p>
                            <p><strong>Email:</strong> ${$('#email').val()}</p>
                            <p><strong>Kelas:</strong> ${kelas !== 'Pilih Kelas (Opsional)' ? kelas : 'Belum ditentukan'}</p>
                        </div>
                        <small class="text-muted">Pastikan perubahan data sudah benar sebelum menyimpan.</small>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-save-line me-1"></i>Ya, Update!',
                    cancelButtonText: '<i class="ri-close-line me-1"></i>Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        $('#submitBtn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Memperbarui...'
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
