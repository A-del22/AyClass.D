@extends('layouts.app')
@section('title', 'Presensi Manual')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .file-upload-wrapper {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
        }

        .file-upload-wrapper:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }

        .file-upload-wrapper.drag-over {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
        }

        .file-upload-input {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            cursor: pointer;
            margin: 0;
        }

        .file-upload-icon {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 6px;
            display: none;
        }

        .file-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-remove {
            background: #dc3545;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            color: white;
            cursor: pointer;
        }

        .siswa-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
        }

        /* Force status section and select to be visible */
        #status-section,
        #status-kehadiran {
            display: block !important;
        }

        /* Custom Select2 styling */
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 12px;
            padding-top: 6px;
            color: #495057;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 10px;
        }

        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Presensi Manual" />

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mb-0">Presensi Manual</h4>
                        <p class="text-muted mb-0">Input Presensi siswa secara manual</p>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ri-check-circle-line me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="ri-close-circle-line me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('attendance.store-manual') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Siswa Selection -->
                            <div class="mb-3">
                                <label for="siswa_id" class="form-label">Pilih Siswa</label>
                                @if ($siswas->count() > 0)
                                    <select name="siswa_id" id="siswa_id"
                                        class="form-select @error('siswa_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach ($siswas as $siswa)
                                            <option value="{{ $siswa->id }}" data-nama="{{ $siswa->user->name }}"
                                                data-nis="{{ $siswa->nis }}"
                                                data-kelas="{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}"
                                                {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                                {{ $siswa->user->name }} - {{ $siswa->nis }}
                                                ({{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('siswa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Siswa Info Display -->
                                    <div id="siswa-info" class="siswa-info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Nama:</strong> <span id="info-nama">-</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>NIS:</strong> <span id="info-nis">-</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Kelas:</strong> <span id="info-kelas">-</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="ri-information-line me-2"></i>
                                        Semua siswa di kelas ini sudah melakukan Presensi hari ini.
                                    </div>
                                @endif
                            </div>

                            <!-- Status Selection -->
                            <div class="mb-3" id="status-section">
                                <label for="status-kehadiran" class="form-label">Status Kehadiran</label>
                                <select name="status" id="status-kehadiran"
                                    class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="terlambat" {{ old('status') == 'terlambat' ? 'selected' : '' }}>
                                        Terlambat</option>
                                    <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <strong>Hadir:</strong> Siswa hadir di kelas<br>
                                    <strong>Izin:</strong> Siswa Tidak Hadir dengan keterangan izin<br>
                                    <strong>Sakit:</strong> Siswa Tidak Hadir karena sakit
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3" id="keterangan-section">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="3"
                                    class="form-control @error('keterangan') is-invalid @enderror"
                                    placeholder="Berikan keterangan tambahan jika diperlukan">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Upload -->
                            <div class="mb-3" id="file-section">
                                <label class="form-label">Surat Izin/Keterangan Dokter (Opsional)</label>
                                <div class="file-upload-wrapper" id="fileUploadWrapper">
                                    <input type="file" name="surat_izin" id="surat_izin"
                                        class="file-upload-input @error('surat_izin') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <label for="surat_izin" class="file-upload-label">
                                        <div class="file-upload-icon">
                                            <i class="ri-cloud-upload-line"></i>
                                        </div>
                                        <div>
                                            <strong>Klik untuk upload file</strong> atau drag & drop di sini<br>
                                            <small class="text-muted">Maksimal 2MB - Format: PDF, JPG, JPEG, PNG</small>
                                        </div>
                                    </label>
                                </div>
                                @error('surat_izin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <div class="file-preview" id="filePreview">
                                    <div class="file-info">
                                        <div>
                                            <i class="ri-file-line me-2"></i>
                                            <span id="fileName"></span>
                                            <span id="fileSize" class="text-muted"></span>
                                        </div>
                                        <button type="button" class="file-remove" id="fileRemove">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2" id="buttons-section">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Simpan Presensi
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="ri-refresh-line me-1"></i>Reset Form
                                </button>
                                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            const hasStudents = {{ $siswas->count() > 0 ? 'true' : 'false' }};

            // Ensure all form sections are visible and remove any inline styles
            $('#status-section, #keterangan-section, #file-section, #buttons-section').show().removeAttr('style');
            $('#status-kehadiran').show().removeAttr('style');

            // Initialize Select2 for siswa selection
            $('#siswa_id').select2({
                placeholder: '-- Pilih Siswa --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Tidak ada siswa yang ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Status change handler - toggle required fields
            $('#status-kehadiran').on('change', function() {
                const status = $(this).val();
                const keteranganField = $('#keterangan');
                const keteranganLabel = $('label[for="keterangan"]');
                const suratIzinField = $('#surat_izin');
                const fileLabel = $('#file-section').find('label.form-label');

                if (status === 'izin' || status === 'sakit') {
                    // Make keterangan required
                    keteranganField.prop('required', true);
                    keteranganLabel.html('Keterangan <span class="text-danger">*</span>');

                    // Show file upload section
                    $('#file-section').show();

                    // Make surat izin required for 'izin' and 'sakit' status
                    if (status === 'izin' || status === 'sakit') {
                        suratIzinField.prop('required', true);
                        fileLabel.html('Surat Izin/Keterangan Dokter <span class="text-danger">*</span>');
                    } else {
                        suratIzinField.prop('required', false);
                        fileLabel.html('Surat Izin/Keterangan Dokter (Opsional)');
                    }
                } else {
                    // Make keterangan optional
                    keteranganField.prop('required', false);
                    keteranganLabel.html('Keterangan');

                    // Make surat izin optional
                    suratIzinField.prop('required', false);
                    fileLabel.html('Surat Izin/Keterangan Dokter (Opsional)');

                    // Hide file upload section for hadir status
                    if (status === 'hadir') {
                        $('#file-section').hide();
                    } else {
                        $('#file-section').show();
                    }
                }
            });

            // Initialize form functionality regardless of student count
            // Siswa selection change handler
            $('#siswa_id').on('change', function() {
                const selectedOption = $(this).find(':selected');
                if (selectedOption.val()) {
                    $('#info-nama').text(selectedOption.data('nama'));
                    $('#info-nis').text(selectedOption.data('nis'));
                    $('#info-kelas').text(selectedOption.data('kelas'));
                    $('#siswa-info').show();
                } else {
                    $('#siswa-info').hide();
                }
            });

            // File upload handlers
            const fileInput = $('#surat_izin');
            const fileWrapper = $('#fileUploadWrapper');
            const filePreview = $('#filePreview');

            fileInput.on('change', function() {
                handleFileSelect(this.files[0]);
            });

            fileWrapper.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drag-over');
            });

            fileWrapper.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
            });

            fileWrapper.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drag-over');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    fileInput[0].files = files;
                    handleFileSelect(files[0]);
                }
            });

            $('#fileRemove').on('click', function() {
                fileInput.val('');
                filePreview.hide();
            });

            function handleFileSelect(file) {
                if (file) {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    $('#fileName').text(fileName);
                    $('#fileSize').text('(' + fileSize + ')');
                    filePreview.show();
                }
            }

            // Form reset handler
            $('button[type="reset"]').on('click', function() {
                $('#siswa_id').val('').trigger('change');
                $('#siswa-info').hide();
                $('#status-kehadiran').val('').trigger('change');
                $('#keterangan').val('').prop('required', false);
                $('label[for="keterangan"]').html('Keterangan');
                $('#surat_izin').prop('required', false);
                $('#file-section').find('label.form-label').html('Surat Izin/Keterangan Dokter (Opsional)');
                $('#file-section').show();
                filePreview.hide();
                fileInput.val('');
            });

            // Trigger change event if there's an old value
            if ($('#siswa_id').val()) {
                $('#siswa_id').trigger('change');
            }
            if ($('#status-kehadiran').val()) {
                $('#status-kehadiran').trigger('change');
            }

            // Form validation enhancement
            $('form').on('submit', function(e) {
                // Check if student is selected
                if (!$('#siswa_id').val()) {
                    e.preventDefault();
                    $('#siswa_id').addClass('is-invalid');
                    if (!$('#siswa_id').next('.invalid-feedback').length) {
                        $('#siswa_id').after(
                            '<div class="invalid-feedback">Pilih siswa terlebih dahulu</div>');
                    }
                    $('#siswa_id').focus();
                    return false;
                }

                // Check if status is selected
                if (!$('#status-kehadiran').val()) {
                    e.preventDefault();
                    $('#status-kehadiran').addClass('is-invalid');
                    if (!$('#status-kehadiran').next('.invalid-feedback').length) {
                        $('#status-kehadiran').after(
                            '<div class="invalid-feedback">Pilih status kehadiran</div>');
                    }
                    $('#status-kehadiran').focus();
                    return false;
                }

                // Check if keterangan is required and filled
                const status = $('#status-kehadiran').val();
                if ((status === 'izin' || status === 'sakit') && !$(
                        '#keterangan').val().trim()) {
                    e.preventDefault();
                    $('#keterangan').addClass('is-invalid');
                    if (!$('#keterangan').next('.invalid-feedback').length) {
                        $('#keterangan').after(
                            '<div class="invalid-feedback">Keterangan wajib diisi untuk status ini</div>'
                        );
                    }
                    $('#keterangan').focus();
                    return false;
                }

                // Check if surat izin is required and uploaded
                if ((status === 'izin' || status === 'sakit') && !$('#surat_izin').val()) {
                    e.preventDefault();
                    $('#surat_izin').addClass('is-invalid');
                    if (!$('#surat_izin').next('.invalid-feedback').length) {
                        $('#surat_izin').after(
                            '<div class="invalid-feedback d-block">Surat izin/keterangan dokter wajib diupload untuk status ini</div>'
                        );
                    }
                    return false;
                }
            });

            // Remove validation errors on change
            $('#siswa_id, #status-kehadiran, #keterangan, #surat_izin').on('change input', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });
        });
    </script>
@endpush
