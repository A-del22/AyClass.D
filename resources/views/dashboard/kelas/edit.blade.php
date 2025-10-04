@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Edit Kelas" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Edit Kelas</h4>
                        <p class="text-muted mb-0">
                            Ubah data kelas {{ $kelas->nama_kelas }}. Pastikan kombinasi tingkat dan jurusan
                            tidak conflict dengan kelas lain.
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" id="formEditKelas">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tingkat_kelas_id" class="form-label">Tingkat Kelas <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="tingkat_kelas_id" name="tingkat_kelas_id" required>
                                            <option value="">Pilih Tingkat Kelas</option>
                                            @foreach ($tingkatKelas as $tingkat)
                                                <option value="{{ $tingkat->id }}"
                                                    {{ old('tingkat_kelas_id', $kelas->tingkat_kelas_id) == $tingkat->id ? 'selected' : '' }}>
                                                    {{ $tingkat->tingkat }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tingkat_kelas_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jurusan_id" class="form-label">Jurusan <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                                                <option value="">Pilih Jurusan</option>
                                                @foreach ($jurusans as $jurusan)
                                                    <option value="{{ $jurusan->id }}"
                                                        data-kode="{{ $jurusan->kode_jurusan }}"
                                                        data-nama="{{ $jurusan->nama_jurusan }}"
                                                        {{ old('jurusan_id', $kelas->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                                        {{ $jurusan->kode_jurusan }} - {{ $jurusan->nama_jurusan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-outline-primary" id="btnEditJurusan"
                                                data-bs-toggle="modal" data-bs-target="#editJurusanModal"
                                                title="Edit Jurusan yang Dipilih">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        </div>
                                        @error('jurusan_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Pilih jurusan yang ingin diedit, lalu klik tombol
                                            edit</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_kelas_preview" class="form-label">Preview Nama Kelas</label>
                                        <input type="text" class="form-control bg-light" id="nama_kelas_preview" readonly
                                            value="{{ $kelas->nama_kelas }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="wali_kelas" class="form-label">Wali Kelas <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="wali_kelas" name="wali_kelas"
                                            value="{{ old('wali_kelas', $kelas->wali_kelas) }}"
                                            placeholder="Masukkan nama wali kelas" required>
                                        @error('wali_kelas')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Contoh: Drs. Ahmad Subandi, M.Pd</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Alert -->
                            <div class="alert alert-info" id="statusAlert">
                                <small><i class="ri-information-line me-1"></i>
                                    Kombinasi saat ini: <strong>{{ $kelas->nama_kelas }}</strong>
                                </small>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ri-save-line me-1"></i> Update Kelas
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
                            <div class="col-md-4">
                                <div class="p-2">
                                    <i class="ri-graduation-cap-line text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">{{ $kelas->tingkatKelas->tingkat }}</h6>
                                    <p class="text-muted small mb-0">Tingkat Kelas</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2">
                                    <i class="ri-book-line text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">{{ $kelas->jurusan->kode_jurusan }}</h6>
                                    <p class="text-muted small mb-0">{{ $kelas->jurusan->nama_jurusan }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2">
                                    <i class="ri-user-3-line text-warning" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">{{ $kelas->jumlah_siswa }} Siswa</h6>
                                    <p class="text-muted small mb-0">Total Siswa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Jurusan -->
    <div class="modal fade" id="editJurusanModal" tabindex="-1" aria-labelledby="editJurusanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJurusanModalLabel">Edit Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditJurusan">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-warning">
                            <small><i class="ri-alert-line me-1"></i>
                                Perubahan jurusan akan mempengaruhi semua kelas yang menggunakan jurusan ini.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="edit_kode_jurusan" class="form-label">Kode Jurusan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_kode_jurusan" name="kode_jurusan"
                                placeholder="Contoh: IPA, IPS, BAHASA" required>
                            <small class="text-muted">Kode singkat untuk jurusan</small>
                        </div>

                        <div class="mb-3">
                            <label for="edit_nama_jurusan" class="form-label">Nama Jurusan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_jurusan" name="nama_jurusan"
                                placeholder="Nama lengkap jurusan" required>
                            <small class="text-muted">Nama lengkap jurusan</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Update Jurusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
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

            const currentKelasId = "{{ $kelas->id }}";
            let currentEditJurusanId = null;

            function toggleEditJurusanButton() {
                const selectedJurusan = $('#jurusan_id').val();
                $('#btnEditJurusan').prop('disabled', !selectedJurusan);
            }

            toggleEditJurusanButton();

            $('#jurusan_id').change(function() {
                toggleEditJurusanButton();
                updatePreviewAndCheck();
            });

            $('#editJurusanModal').on('show.bs.modal', function() {
                const selectedJurusan = $('#jurusan_id option:selected');
                if (selectedJurusan.val()) {
                    currentEditJurusanId = selectedJurusan.val();
                    $('#edit_kode_jurusan').val(selectedJurusan.data('kode'));
                    $('#edit_nama_jurusan').val(selectedJurusan.data('nama'));

                    $('#editJurusanModalLabel').text(`Edit Jurusan: ${selectedJurusan.data('kode')}`);
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih jurusan terlebih dahulu!'
                    });
                    return false;
                }
            });

            $('#formEditJurusan').on('submit', function(e) {
                e.preventDefault();

                if (!currentEditJurusanId) {
                    Toast.fire({
                        icon: 'error',
                        title: 'ID jurusan tidak ditemukan!'
                    });
                    return;
                }

                const kodeJurusan = $('#edit_kode_jurusan').val().trim().toUpperCase();
                const namaJurusan = $('#edit_nama_jurusan').val().trim();

                if (!kodeJurusan || !namaJurusan) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Semua field harus diisi!'
                    });
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Memperbarui...');

                $.ajax({
                    url: "{{ route('kelas.jurusan.update', ':id') }}".replace(':id',
                        currentEditJurusanId),
                    type: 'PUT',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'kode_jurusan': kodeJurusan,
                        'nama_jurusan': namaJurusan
                    },
                    success: function(response) {
                        if (response.success) {
                            const optionToUpdate = $(
                                `#jurusan_id option[value="${currentEditJurusanId}"]`);
                            optionToUpdate.text(response.data.kode_jurusan + ' - ' + response
                                    .data.nama_jurusan)
                                .attr('data-kode', response.data.kode_jurusan)
                                .attr('data-nama', response.data.nama_jurusan);

                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });

                            $('#editJurusanModal').modal('hide');
                            updatePreviewAndCheck();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Gagal memperbarui jurusan'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui jurusan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                        }
                        Toast.fire({
                            icon: 'error',
                            title: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            $('#editJurusanModal').on('hidden.bs.modal', function() {
                $('#formEditJurusan')[0].reset();
                $('#editJurusanModalLabel').text('Edit Jurusan');
                currentEditJurusanId = null;
            });

            function updatePreviewAndCheck() {
                const tingkatSelect = $('#tingkat_kelas_id');
                const jurusanSelect = $('#jurusan_id');
                const tingkatText = tingkatSelect.find('option:selected').text();
                const jurusanText = jurusanSelect.find('option:selected').text();

                if (tingkatSelect.val() && jurusanSelect.val()) {
                    const tingkat = tingkatText.trim();
                    const jurusan = jurusanText.split(' - ')[0];
                    const newNamaKelas = tingkat + ' ' + jurusan;

                    $('#nama_kelas_preview').val(newNamaKelas);

                    const currentNama = "{{ $kelas->nama_kelas }}";
                    if (newNamaKelas !== currentNama) {
                        $('#statusAlert').removeClass('alert-info').addClass('alert-warning')
                            .html(`<small><i class="ri-alert-line me-1"></i>
                            Nama kelas akan diubah dari <strong>${currentNama}</strong>
                            menjadi <strong>${newNamaKelas}</strong></small>`);
                    } else {
                        $('#statusAlert').removeClass('alert-warning').addClass('alert-info')
                            .html(`<small><i class="ri-information-line me-1"></i>
                            Kombinasi saat ini: <strong>${currentNama}</strong></small>`);
                    }
                } else {
                    $('#nama_kelas_preview').val('');
                }
            }

            $('#tingkat_kelas_id').change(function() {
                const tingkatId = $(this).val();
                const jurusanSelect = $('#jurusan_id');
                const currentJurusanId = "{{ $kelas->jurusan_id }}";

                jurusanSelect.empty().append('<option value="">Pilih Jurusan</option>');
                toggleEditJurusanButton();

                if (tingkatId) {
                    jurusanSelect.prop('disabled', true)
                        .append('<option value="">Memuat jurusan...</option>');

                    $.ajax({
                        url: "{{ route('kelas.available.jurusan') }}",
                        type: 'GET',
                        data: {
                            tingkat_id: tingkatId,
                            current_kelas_id: currentKelasId
                        },
                        success: function(response) {
                            jurusanSelect.empty().append(
                                '<option value="">Pilih Jurusan</option>');

                            if (response.success && response.data.length > 0) {
                                response.data.forEach(function(jurusan) {
                                    const selected = jurusan.id === currentJurusanId ?
                                        'selected' : '';
                                    jurusanSelect.append(`
                                        <option value="${jurusan.id}"
                                                data-kode="${jurusan.kode_jurusan}"
                                                data-nama="${jurusan.nama_jurusan}"
                                                ${selected}>
                                            ${jurusan.kode_jurusan} - ${jurusan.nama_jurusan}
                                        </option>
                                    `);
                                });
                            } else {
                                jurusanSelect.append(
                                    '<option value="" disabled>Tidak ada jurusan tersedia</option>'
                                );
                            }

                            jurusanSelect.prop('disabled', false);
                            toggleEditJurusanButton();
                            updatePreviewAndCheck();
                        },
                        error: function(xhr) {
                            jurusanSelect.empty()
                                .append('<option value="">Error memuat jurusan</option>')
                                .prop('disabled', false);

                            Toast.fire({
                                icon: 'error',
                                title: 'Gagal memuat data jurusan'
                            });
                        }
                    });
                } else {
                    updatePreviewAndCheck();
                }
            });

            $('#formEditKelas').on('submit', function(e) {
                e.preventDefault();

                const tingkatId = $('#tingkat_kelas_id').val();
                const jurusanId = $('#jurusan_id').val();
                const waliKelas = $('#wali_kelas').val().trim();

                if (!tingkatId) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Tingkat kelas harus dipilih!'
                    });
                    $('#tingkat_kelas_id').focus();
                    return;
                }

                if (!jurusanId) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Jurusan harus dipilih!'
                    });
                    $('#jurusan_id').focus();
                    return;
                }

                if (!waliKelas) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Nama wali kelas harus diisi!'
                    });
                    $('#wali_kelas').focus();
                    return;
                }

                const namaKelas = $('#nama_kelas_preview').val();
                const currentNama = "{{ $kelas->nama_kelas }}";
                const isChanged = namaKelas !== currentNama;

                Swal.fire({
                    title: 'Konfirmasi Update Kelas',
                    html: `
                        <div class="text-start">
                            ${isChanged ?
                                `<p><strong>Nama Kelas:</strong> ${currentNama} → ${namaKelas}</p>` :
                                `<p><strong>Nama Kelas:</strong> ${namaKelas}</p>`
                            }
                            <p><strong>Wali Kelas:</strong> ${waliKelas}</p>
                        </div>
                        <small class="text-muted">Pastikan data sudah benar sebelum menyimpan perubahan.</small>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-save-line me-1"></i>Ya, Update!',
                    cancelButtonText: '<i class="ri-close-line me-1"></i>Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#submitBtn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Memperbarui...'
                        );

                        this.submit();
                    }
                });
            });

            if ($('#tingkat_kelas_id').val()) {
                $('#tingkat_kelas_id').trigger('change');
            } else {
                updatePreviewAndCheck();
            }

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

            @if ($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: 'Terdapat kesalahan pada form!'
                });
            @endif
        });
    </script>
@endpush
