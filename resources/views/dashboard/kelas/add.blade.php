@extends('layouts.app')
@section('title', 'Kelola Data Kelas')
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Kelola Data Kelas" />

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="managementTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tingkat-tab" data-bs-toggle="tab"
                                    data-bs-target="#tingkat-pane" type="button" role="tab">
                                    <i class="ri-graduation-cap-line me-1"></i>Tingkat Kelas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="jurusan-tab" data-bs-toggle="tab"
                                    data-bs-target="#jurusan-pane" type="button" role="tab">
                                    <i class="ri-book-line me-1"></i>Jurusan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kelas-tab" data-bs-toggle="tab" data-bs-target="#kelas-pane"
                                    type="button" role="tab">
                                    <i class="ri-school-line me-1"></i>Kelas
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="managementTabsContent">
                            <div class="tab-pane fade show active" id="tingkat-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5 class="mb-3">Tambah Tingkat Kelas</h5>
                                        <form id="formTingkatKelas">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="tingkat" class="form-label">Tingkat <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="tingkat"
                                                            name="tingkat" placeholder="Contoh: X, XI, XII" required>
                                                        <small class="text-muted">Masukkan tingkat dalam format
                                                            romawi</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-add-line me-1"></i>Tambah Tingkat
                                            </button>
                                        </form>

                                        <div class="mt-4">
                                            <h6>Tingkat Kelas yang Tersedia:</h6>
                                            <div id="tingkatList">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @if (isset($tingkatKelas) && $tingkatKelas->count() > 0)
                                                        @foreach ($tingkatKelas as $tingkat)
                                                            <span class="badge bg-primary-subtle text-primary px-3 py-2 position-relative d-inline-flex align-items-center" data-tingkat-id="{{ $tingkat->id }}">
                                                                {{ $tingkat->tingkat }}
                                                                <button type="button" class="btn btn-sm delete-tingkat-btn ms-2"
                                                                        data-tingkat-id="{{ $tingkat->id }}"
                                                                        data-tingkat="{{ $tingkat->tingkat }}"
                                                                        style="padding: 0; width: 18px; height: 18px; line-height: 1; border-radius: 50%; background: #dc3545; color: white; border: none;"
                                                                        title="Hapus tingkat {{ $tingkat->tingkat }}">
                                                                    <i class="ri-close-line" style="font-size: 12px;"></i>
                                                                </button>
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">Belum ada tingkat kelas</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <i class="ri-information-line text-info" style="font-size: 3rem;"></i>
                                                <h6 class="mt-2">Tips Tingkat Kelas</h6>
                                                <p class="text-muted small mb-0">
                                                    Gunakan format romawi: X (kelas 10), XI (kelas 11), XII (kelas 12).
                                                    Tingkat kelas yang sudah ada tidak dapat diduplikasi.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="jurusan-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5 class="mb-3">Tambah Jurusan</h5>
                                        <form id="formJurusan">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="kode_jurusan" class="form-label">Kode Jurusan <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="kode_jurusan"
                                                            name="kode_jurusan" placeholder="Contoh: IPA, IPS, BAHASA"
                                                            required>
                                                        <small class="text-muted">Kode singkat untuk jurusan</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="nama_jurusan" class="form-label">Nama Jurusan <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="nama_jurusan"
                                                            name="nama_jurusan" placeholder="Nama lengkap jurusan"
                                                            required>
                                                        <small class="text-muted">Nama lengkap jurusan</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success">
                                                <i class="ri-add-line me-1"></i>Tambah Jurusan
                                            </button>
                                        </form>

                                        <div class="mt-4">
                                            <h6>Jurusan yang Tersedia:</h6>
                                            <div id="jurusanList">
                                                @if (isset($jurusans) && $jurusans->count() > 0)
                                                    @foreach ($jurusans as $jurusan)
                                                        <div class="card card-body border-start border-success border-3 mb-2"
                                                            data-jurusan-id="{{ $jurusan->id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1 jurusan-kode">
                                                                        {{ $jurusan->kode_jurusan }}</h6>
                                                                    <p class="text-muted small mb-0 jurusan-nama">
                                                                        {{ $jurusan->nama_jurusan }}
                                                                    </p>
                                                                </div>
                                                                <div class="btn-group" role="group">
                                                                    <button
                                                                        class="btn btn-sm btn-outline-primary edit-jurusan-btn"
                                                                        data-jurusan-id="{{ $jurusan->id }}"
                                                                        data-kode="{{ $jurusan->kode_jurusan }}"
                                                                        data-nama="{{ $jurusan->nama_jurusan }}"
                                                                        type="button" title="Edit Jurusan">
                                                                        <i class="ri-edit-line"></i>
                                                                    </button>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-danger delete-jurusan-btn"
                                                                        data-jurusan-id="{{ $jurusan->id }}"
                                                                        data-kode="{{ $jurusan->kode_jurusan }}"
                                                                        data-nama="{{ $jurusan->nama_jurusan }}"
                                                                        type="button" title="Hapus Jurusan">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-muted">Belum ada jurusan</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <i class="ri-information-line text-success" style="font-size: 3rem;"></i>
                                                <h6 class="mt-2">Tips Jurusan</h6>
                                                <p class="text-muted small mb-0">
                                                    Kode jurusan sebaiknya singkat dan mudah diingat.
                                                    Nama jurusan harus lengkap dan deskriptif.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="kelas-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h5 class="mb-3">Tambah Kelas</h5>
                                        <div id="kelasFormContainer">
                                            {{-- Akan muncul jika tingkat dan jurusan sudah ada --}}
                                        </div>

                                        <div class="mt-4" id="existingClassesInfo" style="display: none;">
                                            <h6>Kelas yang Sudah Ada:</h6>
                                            <div class="row" id="existingClassesList">
                                                @if (isset($existingKelas) && $existingKelas->count() > 0)
                                                    @foreach ($existingKelas as $kelas)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="card card-body bg-light border-start border-info border-3"
                                                                 data-kelas-id="{{ $kelas->id }}">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-1 kelas-nama">{{ $kelas->nama_kelas }}</h6>
                                                                        <p class="text-muted small mb-0 kelas-wali">{{ $kelas->wali_kelas }}</p>
                                                                    </div>
                                                                    <div class="btn-group" role="group">
                                                                        <button class="btn btn-sm btn-outline-primary edit-kelas-btn"
                                                                                data-kelas-id="{{ $kelas->id }}"
                                                                                data-tingkat="{{ $kelas->tingkatKelas->tingkat }}"
                                                                                data-jurusan="{{ $kelas->jurusan->kode_jurusan }}"
                                                                                data-wali="{{ $kelas->wali_kelas }}"
                                                                                data-nama="{{ $kelas->nama_kelas }}"
                                                                                type="button"
                                                                                title="Edit Kelas">
                                                                            <i class="ri-edit-line"></i>
                                                                        </button>
                                                                        <button class="btn btn-sm btn-outline-danger delete-kelas-btn"
                                                                                data-kelas-id="{{ $kelas->id }}"
                                                                                data-nama="{{ $kelas->nama_kelas }}"
                                                                                data-wali="{{ $kelas->wali_kelas }}"
                                                                                type="button"
                                                                                title="Hapus Kelas">
                                                                            <i class="ri-delete-bin-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <i class="ri-information-line text-warning" style="font-size: 3rem;"></i>
                                                <h6 class="mt-2">Persyaratan</h6>
                                                <p class="text-muted small mb-0">
                                                    Untuk membuat kelas, pastikan sudah ada minimal 1 tingkat kelas dan 1
                                                    jurusan.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Data Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                        <input type="hidden" id="edit_jurusan_id">
                        <div class="mb-3">
                            <label for="edit_kode_jurusan" class="form-label">Kode Jurusan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_kode_jurusan" name="kode_jurusan"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama_jurusan" class="form-label">Nama Jurusan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_jurusan" name="nama_jurusan"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editKelasModal" tabindex="-1" aria-labelledby="editKelasModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKelasModalLabel">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditKelas">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="edit_kelas_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tingkat_kelas_id" class="form-label">Tingkat Kelas</label>
                                    <select class="form-select" id="edit_tingkat_kelas_id" name="tingkat_kelas_id" disabled>
                                        <option value="">Tingkat tidak dapat diubah</option>
                                    </select>
                                    <small class="text-muted">Tingkat kelas tidak dapat diubah saat edit</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_jurusan_id" class="form-label">Jurusan</label>
                                    <select class="form-select" id="edit_jurusan_id" name="jurusan_id" disabled>
                                        <option value="">Jurusan tidak dapat diubah</option>
                                    </select>
                                    <small class="text-muted">Jurusan tidak dapat diubah saat edit</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nama_kelas_preview" class="form-label">Preview Nama Kelas</label>
                                    <input type="text" class="form-control bg-light" id="edit_nama_kelas_preview" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_wali_kelas" class="form-label">Wali Kelas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_wali_kelas" name="wali_kelas" required>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info" id="editCombinationAlert" style="display: none;">
                            <small><i class="ri-information-line me-1"></i>Kombinasi ini dapat digunakan.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Simpan Perubahan
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

            function checkRequirements() {
                const hasTingkat = $('#tingkatList .badge').length > 0;
                const hasJurusan = $('#jurusanList .card').length > 0;

                if (hasTingkat && hasJurusan) {
                    showKelasForm();
                } else {
                    showRequirementsMessage();
                }
            }

            function showRequirementsMessage() {
                $('#kelasFormContainer').html(`
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">Persyaratan Belum Terpenuhi</h6>
                        <p class="mb-0">Untuk membuat kelas, Anda perlu:</p>
                        <ul class="mb-0 mt-2">
                            <li>Minimal 1 Tingkat Kelas (Tab pertama)</li>
                            <li>Minimal 1 Jurusan (Tab kedua)</li>
                        </ul>
                    </div>
                `);
            }

            function showKelasForm() {
                $('#kelasFormContainer').html(`
                    <form id="formKelas">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tingkat_kelas_id" class="form-label">Tingkat Kelas <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tingkat_kelas_id" name="tingkat_kelas_id" required>
                                        <option value="">Pilih Tingkat Kelas</option>
                                    </select>
                                    <small class="text-muted">Hanya menampilkan tingkat yang masih bisa dikombinasikan</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jurusan_id" name="jurusan_id" required disabled>
                                        <option value="">Pilih tingkat kelas terlebih dahulu</option>
                                    </select>
                                    <small class="text-muted">Otomatis terfilter berdasarkan tingkat yang dipilih</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_kelas_preview" class="form-label">Preview Nama Kelas</label>
                                    <input type="text" class="form-control bg-light" id="nama_kelas_preview" readonly
                                           placeholder="Akan otomatis terisi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="wali_kelas" class="form-label">Wali Kelas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="wali_kelas" name="wali_kelas"
                                           placeholder="Nama wali kelas" required>
                                    <small class="text-muted">Contoh: Drs. Ahmad Subandi, M.Pd</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" id="combinationAlert" style="display: none;">
                            <small><i class="ri-information-line me-1"></i>Kombinasi ini belum ada dan dapat ditambahkan.</small>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="ri-school-line me-1"></i>Tambah Kelas
                        </button>
                    </form>
                `);

                $('#existingClassesInfo').show();
                loadSelectOptions();
            }

            function loadSelectOptions() {
                const existingCombinations = [
                    @if (isset($existingKelas))
                        @foreach ($existingKelas as $kelas)
                            {
                                tingkat: '{{ $kelas->tingkatKelas->tingkat }}',
                                jurusan: '{{ $kelas->jurusan->kode_jurusan }}'
                            },
                        @endforeach
                    @endif
                ];

                // Available jurusan from server-side data
                const availableJurusan = [
                    @if (isset($jurusans))
                        @foreach ($jurusans as $jurusan)
                            {
                                kode: '{{ $jurusan->kode_jurusan }}',
                                nama: '{{ $jurusan->nama_jurusan }}'
                            },
                        @endforeach
                    @endif
                ];

                // Available tingkat from server-side data
                const availableTingkat = [
                    @if (isset($tingkatKelas))
                        @foreach ($tingkatKelas as $tingkat)
                            '{{ $tingkat->tingkat }}',
                        @endforeach
                    @endif
                ];


                $('#tingkat_kelas_id').empty().append('<option value="">Pilih Tingkat Kelas</option>');
                availableTingkat.forEach(function(tingkat) {
                    $('#tingkat_kelas_id').append(`<option value="${tingkat}">${tingkat}</option>`);
                });

                // Initially, jurusan dropdown is disabled until tingkat is selected
                $('#jurusan_id').empty().append('<option value="">Pilih tingkat kelas terlebih dahulu</option>').prop('disabled', true);

                $('#tingkat_kelas_id').change(function() {
                    const selectedTingkat = $(this).val();
                    const jurusanSelect = $('#jurusan_id');

                    jurusanSelect.empty().append('<option value="">Pilih Jurusan</option>');
                    $('#nama_kelas_preview').val('');

                    if (selectedTingkat) {
                        availableJurusan.forEach(function(jurusan) {
                            const combinationExists = existingCombinations.some(combo =>
                                combo.tingkat === selectedTingkat && combo.jurusan === jurusan.kode
                            );

                            if (!combinationExists) {
                                jurusanSelect.append(
                                    `<option value="${jurusan.kode}">${jurusan.kode} - ${jurusan.nama}</option>`);
                            }
                        });

                        jurusanSelect.prop('disabled', false);
                        if (jurusanSelect.find('option').length === 1) {
                            jurusanSelect.append(
                                '<option value="" disabled>Semua jurusan sudah ada untuk tingkat ini</option>'
                            );
                        }
                    } else {
                        jurusanSelect.prop('disabled', true);
                    }
                });

                $('#jurusan_id').change(function() {
                    const tingkat = $('#tingkat_kelas_id').val();
                    const jurusan = $(this).val();

                    if (tingkat && jurusan) {
                        $('#nama_kelas_preview').val(tingkat + ' ' + jurusan);

                        $('#combinationAlert').show().html(`
                            <small><i class="ri-check-line me-1 text-success"></i>
                            Kombinasi <strong>${tingkat} ${jurusan}</strong> belum ada dan dapat ditambahkan.
                            </small>
                        `).removeClass('alert-info alert-warning').addClass('alert-success');

                    } else {
                        $('#nama_kelas_preview').val('');
                        $('#combinationAlert').hide();
                    }
                });
            }

            $(document).on('submit', '#formTingkatKelas', function(e) {
                e.preventDefault();
                const tingkat = $('#tingkat').val().trim().toUpperCase();

                if (!tingkat) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Tingkat harus diisi!'
                    });
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                $.ajax({
                    url: "{{ route('kelas.store.tingkat') }}",
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'tingkat': tingkat
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#tingkatList .d-flex').append(`
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 position-relative d-inline-flex align-items-center" data-tingkat-id="${response.data.id}">
                                    ${response.data.tingkat}
                                    <button type="button" class="btn btn-sm delete-tingkat-btn ms-2"
                                            data-tingkat-id="${response.data.id}"
                                            data-tingkat="${response.data.tingkat}"
                                            style="padding: 0; width: 18px; height: 18px; line-height: 1; border-radius: 50%; background: #dc3545; color: white; border: none;"
                                            title="Hapus tingkat ${response.data.tingkat}">
                                        <i class="ri-close-line" style="font-size: 12px;"></i>
                                    </button>
                                </span>
                            `);

                            $('#tingkat').val('');
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            checkRequirements();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan';
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

            $(document).on('submit', '#formJurusan', function(e) {
                e.preventDefault();
                const kode = $('#kode_jurusan').val().trim().toUpperCase();
                const nama = $('#nama_jurusan').val().trim();

                if (!kode || !nama) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Kode dan nama jurusan harus diisi!'
                    });
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                $.ajax({
                    url: "{{ route('kelas.store.jurusan') }}",
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'kode_jurusan': kode,
                        'nama_jurusan': nama
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#jurusanList').append(`
                                <div class="card card-body border-start border-success border-3 mb-2"
                                     data-jurusan-id="${response.data.id}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 jurusan-kode">${response.data.kode_jurusan}</h6>
                                            <p class="text-muted small mb-0 jurusan-nama">${response.data.nama_jurusan}</p>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary edit-jurusan-btn"
                                                    data-jurusan-id="${response.data.id}"
                                                    data-kode="${response.data.kode_jurusan}"
                                                    data-nama="${response.data.nama_jurusan}"
                                                    type="button"
                                                    title="Edit Jurusan">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-jurusan-btn"
                                                    data-jurusan-id="${response.data.id}"
                                                    data-kode="${response.data.kode_jurusan}"
                                                    data-nama="${response.data.nama_jurusan}"
                                                    type="button"
                                                    title="Hapus Jurusan">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `);

                            $('#kode_jurusan, #nama_jurusan').val('');
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            checkRequirements();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan';
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

            $(document).on('click', '.edit-jurusan-btn', function() {
                const jurusanId = $(this).data('jurusan-id');
                const kode = $(this).data('kode');
                const nama = $(this).data('nama');

                $('#edit_jurusan_id').val(jurusanId);
                $('#edit_kode_jurusan').val(kode);
                $('#edit_nama_jurusan').val(nama);

                $('#editJurusanModal').modal('show');
            });

            $(document).on('submit', '#formEditJurusan', function(e) {
                e.preventDefault();

                const jurusanId = $('#edit_jurusan_id').val();
                const kode = $('#edit_kode_jurusan').val().trim().toUpperCase();
                const nama = $('#edit_nama_jurusan').val().trim();

                if (!kode || !nama) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Kode dan nama jurusan harus diisi!'
                    });
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                $.ajax({
                    url: `{{ route('kelas.jurusan.update', ':id') }}`.replace(':id', jurusanId),
                    type: 'PUT',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'kode_jurusan': kode,
                        'nama_jurusan': nama
                    },
                    success: function(response) {
                        if (response.success) {
                            const jurusanCard = $(`[data-jurusan-id="${jurusanId}"]`);
                            jurusanCard.find('.jurusan-kode').text(response.data.kode_jurusan);
                            jurusanCard.find('.jurusan-nama').text(response.data.nama_jurusan);

                            jurusanCard.find('.edit-jurusan-btn')
                                .data('kode', response.data.kode_jurusan)
                                .data('nama', response.data.nama_jurusan);
                            jurusanCard.find('.delete-jurusan-btn')
                                .data('kode', response.data.kode_jurusan)
                                .data('nama', response.data.nama_jurusan);

                            $('#editJurusanModal').modal('hide');
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            checkRequirements();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui';
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

            // Handle delete tingkat kelas
            $(document).on('click', '.delete-tingkat-btn', function() {
                const tingkatId = $(this).data('tingkat-id');
                const tingkat = $(this).data('tingkat');

                Swal.fire({
                    title: 'Konfirmasi Hapus Tingkat Kelas',
                    html: `
                        <div class="text-start">
                            <p>Apakah Anda yakin ingin menghapus tingkat kelas <strong>${tingkat}</strong>?</p>
                            <div class="alert alert-warning">
                                <small><i class="ri-information-line"></i> Tingkat kelas yang masih digunakan oleh kelas tidak dapat dihapus.</small>
                            </div>
                        </div>
                    `,
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
                            title: 'Menghapus...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `{{ route('kelas.tingkat.destroy', ':id') }}`.replace(':id', tingkatId),
                            type: 'DELETE',
                            data: {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.close();
                                if (response.success) {
                                    $(`[data-tingkat-id="${tingkatId}"]`).fadeOut(300, function() {
                                        $(this).remove();

                                        if ($('#tingkatList .badge').length === 0) {
                                            $('#tingkatList').html('<div class="d-flex flex-wrap gap-2"><span class="text-muted">Belum ada tingkat kelas</span></div>');
                                        }

                                        checkRequirements();
                                    });

                                    Toast.fire({
                                        icon: 'success',
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                let errorMessage = 'Terjadi kesalahan saat menghapus';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.status === 422) {
                                    errorMessage = xhr.responseJSON.message || 'Tingkat kelas masih digunakan';
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

            $(document).on('click', '.delete-jurusan-btn', function() {
                const jurusanId = $(this).data('jurusan-id');
                const kode = $(this).data('kode');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Konfirmasi Hapus Jurusan',
                    html: `
                        <div class="text-start">
                            <p>Apakah Anda yakin ingin menghapus jurusan ini?</p>
                            <div class="alert alert-warning">
                                <strong>Kode:</strong> ${kode}<br>
                                <strong>Nama:</strong> ${nama}
                            </div>
                            <small class="text-muted">
                                <i class="ri-information-line"></i>
                                Jurusan yang masih digunakan oleh kelas tidak dapat dihapus.
                            </small>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteBtn = $(this);
                        const originalHtml = deleteBtn.html();
                        deleteBtn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm"></span>');

                        $.ajax({
                            url: `{{ route('kelas.jurusan.destroy', ':id') }}`.replace(
                                ':id', jurusanId),
                            type: 'DELETE',
                            data: {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`[data-jurusan-id="${jurusanId}"]`).fadeOut(300,
                                        function() {
                                            $(this).remove();

                                            if ($('#jurusanList .card').length ===
                                                0) {
                                                $('#jurusanList').html(
                                                    '<div class="text-muted">Belum ada jurusan</div>'
                                                );
                                            }

                                            checkRequirements();
                                        });

                                    Toast.fire({
                                        icon: 'success',
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: response.message
                                    });
                                    deleteBtn.prop('disabled', false).html(
                                        originalHtml);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menghapus';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Toast.fire({
                                    icon: 'error',
                                    title: errorMessage
                                });
                                deleteBtn.prop('disabled', false).html(originalHtml);
                            }
                        });
                    }
                });
            });

            $(document).on('submit', '#formKelas', function(e) {
                e.preventDefault();

                const tingkat = $('#tingkat_kelas_id').val();
                const jurusan = $('#jurusan_id').val();
                const waliKelas = $('#wali_kelas').val().trim();

                if (!tingkat || !jurusan || !waliKelas) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Semua field harus diisi!'
                    });
                    return;
                }

                const namaKelas = tingkat + ' ' + jurusan;

                Swal.fire({
                    title: 'Konfirmasi Tambah Kelas',
                    html: `
                        <div class="text-start">
                            <p><strong>Kelas:</strong> ${namaKelas}</p>
                            <p><strong>Wali Kelas:</strong> ${waliKelas}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tambahkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const submitBtn = $('#formKelas').find('button[type="submit"]');
                        const originalText = submitBtn.html();
                        submitBtn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...'
                        );

                        $.ajax({
                            url: "{{ route('kelas.store.kelas') }}",
                            type: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'tingkat_kelas_id': tingkat,
                                'jurusan_id': jurusan,
                                'wali_kelas': waliKelas
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#existingClassesList').append(`
                                        <div class="col-md-6 mb-2">
                                            <div class="card card-body bg-light border-start border-info border-3"
                                                 data-kelas-id="${response.data.id}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1 kelas-nama">${response.data.nama_kelas}</h6>
                                                        <p class="text-muted small mb-0 kelas-wali">${response.data.wali_kelas}</p>
                                                    </div>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-primary edit-kelas-btn"
                                                                data-kelas-id="${response.data.id}"
                                                                data-tingkat="${response.data.tingkat_kelas.tingkat}"
                                                                data-jurusan="${response.data.jurusan.kode_jurusan}"
                                                                data-wali="${response.data.wali_kelas}"
                                                                data-nama="${response.data.nama_kelas}"
                                                                type="button"
                                                                title="Edit Kelas">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger delete-kelas-btn"
                                                                data-kelas-id="${response.data.id}"
                                                                data-nama="${response.data.nama_kelas}"
                                                                data-wali="${response.data.wali_kelas}"
                                                                type="button"
                                                                title="Hapus Kelas">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);

                                    Toast.fire({
                                        icon: 'success',
                                        title: response.message
                                    });
                                    $('#formKelas')[0].reset();
                                    $('#nama_kelas_preview').val('');
                                    $('#combinationAlert').hide();
                                    checkRequirements();
                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menyimpan';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON
                                    .errors) {
                                    errorMessage = Object.values(xhr.responseJSON
                                        .errors)[0][0];
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
                    }
                });
            });

            // Store data temporarily for modal population
            let currentEditData = {};

            $(document).on('click', '.edit-kelas-btn', function() {
                const kelasId = $(this).data('kelas-id');
                const tingkat = $(this).data('tingkat');
                const jurusan = $(this).data('jurusan');
                const wali = $(this).data('wali');
                const nama = $(this).data('nama');

                // Store data for use when modal is shown
                currentEditData = {kelasId, tingkat, jurusan, wali, nama};

                // Set basic values
                $('#edit_kelas_id').val(kelasId);
                $('#edit_wali_kelas').val(wali);
                $('#edit_nama_kelas_preview').val(nama);

                // Show modal
                $('#editKelasModal').modal('show');
            });

            // Populate modal when it's fully shown
            $('#editKelasModal').on('shown.bs.modal', function() {
                const {kelasId, tingkat, jurusan, wali, nama} = currentEditData;

                // Set current tingkat as display only
                $('#edit_tingkat_kelas_id').empty().append(`<option value="${tingkat}" selected>${tingkat} - (Tidak dapat diubah)</option>`);

                // Set current jurusan as display only
                $('#edit_jurusan_id').empty().append(`<option value="${jurusan}" selected>${jurusan} - (Tidak dapat diubah)</option>`);

                // Update alert
                $('#editCombinationAlert').show().html(`
                    <small><i class="ri-information-line me-1"></i>Kombinasi saat ini: <strong>${nama}</strong></small>
                `);
            });



            $(document).on('submit', '#formEditKelas', function(e) {
                e.preventDefault();

                const kelasId = $('#edit_kelas_id').val();
                const {tingkat, jurusan} = currentEditData;
                const waliKelas = $('#edit_wali_kelas').val().trim();

                if (!waliKelas) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Wali Kelas harus diisi!'
                    });
                    return;
                }

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

                $.ajax({
                    url: `{{ route('kelas.update', ':id') }}`.replace(':id', kelasId),
                    type: 'PUT',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'tingkat_kelas_id': tingkat,
                        'jurusan_id': jurusan,
                        'wali_kelas': waliKelas
                    },
                    success: function(response) {
                        if (response.success) {
                            const kelasCard = $(`[data-kelas-id="${kelasId}"]`);
                            kelasCard.find('.kelas-nama').text(response.data.nama_kelas);
                            kelasCard.find('.kelas-wali').text(response.data.wali_kelas);

                            kelasCard.find('.edit-kelas-btn')
                                .data('tingkat', response.data.tingkat_kelas.tingkat)
                                .data('jurusan', response.data.jurusan.kode_jurusan)
                                .data('wali', response.data.wali_kelas)
                                .data('nama', response.data.nama_kelas);
                            kelasCard.find('.delete-kelas-btn')
                                .data('nama', response.data.nama_kelas)
                                .data('wali', response.data.wali_kelas);

                            $('#editKelasModal').modal('hide');
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            checkRequirements();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui kelas';
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

            $(document).on('click', '.delete-kelas-btn', function() {
                const kelasId = $(this).data('kelas-id');
                const nama = $(this).data('nama');
                const wali = $(this).data('wali');

                Swal.fire({
                    title: 'Konfirmasi Hapus Kelas',
                    html: `
                        <div class="text-start">
                            <p>Apakah Anda yakin ingin menghapus kelas ini?</p>
                            <div class="alert alert-warning">
                                <strong>Kelas:</strong> ${nama}<br>
                                <strong>Wali Kelas:</strong> ${wali}
                            </div>
                            <small class="text-muted">
                                <i class="ri-information-line"></i>
                                Kelas yang masih memiliki siswa tidak dapat dihapus.
                            </small>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteBtn = $(this);
                        const originalHtml = deleteBtn.html();
                        deleteBtn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm"></span>');

                        $.ajax({
                            url: `{{ route('kelas.destroy', ':id') }}`.replace(':id', kelasId),
                            type: 'DELETE',
                            data: {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`[data-kelas-id="${kelasId}"]`).closest('.col-md-6').fadeOut(300,
                                        function() {
                                            $(this).remove();

                                            if ($('#existingClassesList .col-md-6').length === 0) {
                                                $('#existingClassesInfo').hide();
                                            }

                                            checkRequirements();
                                        });

                                    Toast.fire({
                                        icon: 'success',
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: response.message
                                    });
                                    deleteBtn.prop('disabled', false).html(originalHtml);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menghapus kelas';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Toast.fire({
                                    icon: 'error',
                                    title: errorMessage
                                });
                                deleteBtn.prop('disabled', false).html(originalHtml);
                            }
                        });
                    }
                });
            });

            checkRequirements();

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
