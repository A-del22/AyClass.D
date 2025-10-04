@extends('layouts.app')
@section('title', 'Detail Siswa')
@push('styles')
    <style>
        .student-card-landscape {
            width: 100%;
            max-width: 600px;
            height: auto;
            min-height: 380px;
            background: #ffffff;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            margin: 0 auto;
            font-family: 'Arial', sans-serif;
        }

        .card-header-simple {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .school-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 200px;
        }

        .school-logo-simple {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .school-text {
            color: #5a5c69;
            flex: 1;
        }

        .school-name-simple {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }

        .card-type-simple {
            font-size: 11px;
            color: #6e707e;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body-landscape {
            display: flex;
            padding: 20px;
            gap: 20px;
            min-height: 300px;
        }

        .photo-section-landscape {
            flex: 0 0 140px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .student-photo-landscape {
            width: 120px;
            height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .info-section-landscape {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }

        .student-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 11px;
            color: #6e707e;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 13px;
            color: #2c3e50;
            font-weight: 600;
            word-wrap: break-word;
        }

        .barcode-section-landscape {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .barcode-container-landscape {
            background: white;
            padding: 5px;
            border: 1px solid #e3e6f0;
            border-radius: 4px;
        }

        .nis-text {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin-top: 3px;
        }

        .validity-text {
            font-size: 10px;
            color: #6e707e;
            text-align: right;
        }

        .qr-code-section {
            text-align: center;
            margin-top: 10px;
        }

        .qr-code-section img {
            display: block !important;
            image-rendering: pixelated !important;
            image-rendering: crisp-edges !important;
        }

        .detail-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }

        .detail-card .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .info-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #4e73df;
        }

        .info-item .label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .info-item .value {
            font-size: 1rem;
            color: #212529;
            font-weight: 500;
            word-wrap: break-word;
        }

        @media (min-width: 1200px) {
            .student-card-landscape {
                width: 600px;
                height: 380px;
            }

            .card-body-landscape {
                height: calc(100% - 80px);
            }

            .student-details {
                grid-template-columns: 1fr 1fr;
            }

            .info-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 1199px) and (min-width: 992px) {
            .student-card-landscape {
                max-width: 550px;
            }

            .student-details {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 991px) and (min-width: 768px) {
            .student-card-landscape {
                max-width: 500px;
            }

            .card-body-landscape {
                padding: 15px;
                gap: 15px;
            }

            .photo-section-landscape {
                flex: 0 0 120px;
            }

            .student-photo-landscape {
                width: 100px;
                height: 130px;
            }

            .student-details {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .detail-value {
                font-size: 12px;
            }

            .info-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            }
        }

        @media (max-width: 767px) and (min-width: 576px) {
            .student-card-landscape {
                max-width: 100%;
                margin: 0 10px;
            }

            .card-header-simple {
                padding: 12px 15px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .school-info {
                width: 100%;
                min-width: auto;
            }

            .school-name-simple {
                font-size: 13px;
            }

            .card-body-landscape {
                flex-direction: column;
                align-items: center;
                padding: 15px;
                gap: 20px;
            }

            .photo-section-landscape {
                flex: none;
                width: 100%;
                align-items: center;
            }

            .info-section-landscape {
                width: 100%;
            }

            .student-details {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
                text-align: left;
            }

            .barcode-section-landscape {
                flex-direction: column;
                align-items: center;
                gap: 15px;
                text-align: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .student-card-landscape {
                margin: 0 5px;
                border-radius: 8px;
            }

            .card-header-simple {
                padding: 10px 12px;
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .school-logo-simple {
                width: 35px;
                height: 35px;
            }

            .school-name-simple {
                font-size: 12px;
            }

            .card-type-simple {
                font-size: 10px;
            }

            .card-body-landscape {
                flex-direction: column;
                align-items: center;
                padding: 12px;
                gap: 15px;
            }

            .student-photo-landscape {
                width: 90px;
                height: 120px;
            }

            .student-details {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .detail-label {
                font-size: 10px;
            }

            .detail-value {
                font-size: 12px;
            }

            .barcode-section-landscape {
                flex-direction: column;
                align-items: center;
                gap: 10px;
                padding-top: 10px;
            }

            .nis-text {
                font-size: 10px;
            }

            .validity-text {
                font-size: 9px;
                text-align: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .info-item {
                padding: 0.75rem;
            }

            .info-item .label {
                font-size: 0.8rem;
            }

            .info-item .value {
                font-size: 0.9rem;
            }

            .card-footer .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            .card-footer .d-flex {
                flex-direction: column;
                gap: 10px;
            }

            .card-footer .d-flex>div {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 375px) {
            .student-card-landscape {
                margin: 0 2px;
            }

            .card-body-landscape {
                padding: 10px;
            }

            .student-photo-landscape {
                width: 80px;
                height: 110px;
            }

            .detail-value {
                font-size: 11px;
            }

            .card-footer .btn {
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .student-card-landscape {
                box-shadow: none;
                page-break-inside: avoid;
                border: 1px solid #000;
                width: 600px;
                height: 380px;
                margin: 0;
            }

            .card-body-landscape {
                flex-direction: row;
                height: calc(100% - 80px);
            }

            .student-details {
                grid-template-columns: 1fr 1fr;
            }

            .barcode-section-landscape {
                flex-direction: row;
            }
        }

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .student-photo-landscape {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        .student-card-landscape {
            background: #ffffff !important;
            border-color: #e3e6f0 !important;
            color: #2c3e50 !important;
        }

        .card-header-simple {
            background: #f8f9fc !important;
            border-bottom-color: #e3e6f0 !important;
        }

        .school-text {
            color: #5a5c69 !important;
        }

        .detail-label {
            color: #6e707e !important;
        }

        .detail-value {
            color: #2c3e50 !important;
        }

        .info-item {
            background: #f8f9fa !important;
            color: #212529 !important;
        }

        .info-item .label {
            color: #6c757d !important;
        }

        .info-item .value {
            color: #212529 !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Detail Siswa" />

        <div class="row">
            <div class="col-12">
                <div class="card detail-card mb-4">
                    <div class="card-header text-center">
                        <h5 class="mb-0">
                            <i class="ri-id-card-line me-2"></i>Kartu Pelajar
                        </h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center"
                        style="min-height: 420px; padding: 15px;">
                        <div class="student-card-landscape">
                            <div class="card-header-simple">
                                <div class="school-info">
                                    <div class="school-logo-simple">
                                        <img src="{{ asset('logo.png') }}" alt="Logo Sekolah"
                                            style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;">
                                    </div>
                                    <div class="school-text">
                                        <h6 class="school-name-simple">
                                            {{ $appSettings->nama_sekolah ?? 'SMA NEGERI 1 SURAKARTA' }}
                                        </h6>
                                        <p class="card-type-simple">Kartu Pelajar</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size: 10px; color: #6e707e;">ID: {{ $siswa->nis }}</div>
                                </div>
                            </div>

                            <div class="card-body-landscape">
                                <div class="photo-section-landscape">
                                    @if ($siswa->foto == 'avatar.png')
                                        <img src="{{ asset('avatar.png') }}" alt="Foto {{ $siswa->user->name }}"
                                            class="student-photo-landscape">
                                    @elseif($siswa->foto)
                                        <img src="{{ asset('storage/' . $siswa->foto) }}"
                                            alt="Foto {{ $siswa->user->name }}" class="student-photo-landscape">
                                    @else
                                        <div
                                            class="student-photo-landscape d-flex align-items-center justify-content-center">
                                            <i class="ri-user-3-line text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif

                                    <div class="qr-code-section">
                                        <div style="font-size: 8px; color: #6e707e; margin-bottom: 2px;">QR Code</div>
                                        <div class="d-inline-block"
                                            style="padding: 2px; background: white; border: 1px solid #e3e6f0;">
                                            <img src="{{ route('siswa.qrcode', $siswa->id) }}" alt="QR Code"
                                                style="width: 80px; height: 80px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="info-section-landscape">
                                    <div class="student-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Nama Lengkap</div>
                                            <div class="detail-value">{{ $siswa->user->name }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">NIS</div>
                                            <div class="detail-value">{{ $siswa->nis }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Kelas</div>
                                            <div class="detail-value">
                                                {{ $siswa->kelas ? $siswa->kelas->nama_kelas : 'Belum ada kelas' }}
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Jenis Kelamin</div>
                                            <div class="detail-value">
                                                {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Tanggal Lahir</div>
                                            <div class="detail-value">{{ $siswa->tanggal_lahir->format('d/m/Y') }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Tahun Masuk</div>
                                            <div class="detail-value">{{ $siswa->tanggal_masuk->format('Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="validity-section">
                                        <div class="validity-text"
                                            style="text-align: center; padding-top: 15px; border-top: 1px solid #e3e6f0;">
                                            <div>Berlaku selama</div>
                                            <div>menjadi siswa aktif</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center no-print">
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button class="btn btn-primary btn-sm" onclick="printCard()">
                                <i class="ri-printer-line me-1"></i>Cetak Kartu
                            </button>
                            <button class="btn btn-success btn-sm" onclick="downloadCard()">
                                <i class="ri-download-line me-1"></i>Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card detail-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ri-user-line me-2"></i>Informasi Lengkap Siswa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="ri-user-3-line me-2"></i>Data Pribadi
                            </h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="label">Nama Lengkap</div>
                                    <div class="value">{{ $siswa->user->name }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="label">NIS</div>
                                    <div class="value">{{ $siswa->nis }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="label">Email</div>
                                    <div class="value">{{ $siswa->user->email }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="label">Jenis Kelamin</div>
                                    <div class="value">
                                        <span class="badge {{ $siswa->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-info' }}">
                                            {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="label">Tanggal Lahir</div>
                                    <div class="value">{{ $siswa->tanggal_lahir->format('d F Y') }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="label">Umur</div>
                                    <div class="value">{{ $siswa->umur }} tahun</div>
                                </div>
                                <div class="info-item">
                                    <div class="label">Nomor Telepon</div>
                                    <div class="value">{{ $siswa->no_telepon }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="label">Tanggal Masuk</div>
                                    <div class="value">{{ $siswa->tanggal_masuk->format('d F Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="ri-school-line me-2"></i>Data Sekolah
                            </h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="label">Kelas</div>
                                    <div class="value">
                                        @if ($siswa->kelas)
                                            <span class="badge bg-success">{{ $siswa->kelas->nama_kelas }}</span>
                                        @else
                                            <span class="badge bg-warning">Belum ada kelas</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($siswa->kelas)
                                    <div class="info-item">
                                        <div class="label">Wali Kelas</div>
                                        <div class="value">{{ $siswa->kelas->wali_kelas }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Tingkat</div>
                                        <div class="value">{{ $siswa->kelas->tingkatKelas->tingkat }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Jurusan</div>
                                        <div class="value">{{ $siswa->kelas->jurusan->nama_jurusan }}</div>
                                    </div>
                                @endif
                                <div class="info-item">
                                    <div class="label">Lama Bersekolah</div>
                                    <div class="value">{{ $siswa->lama_bersekolah }} tahun</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-info mb-3">
                                <i class="ri-map-pin-line me-2"></i>Alamat
                            </h6>
                            <div class="info-item">
                                <div class="label">Alamat Lengkap</div>
                                <div class="value">{{ $siswa->alamat }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer no-print">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-primary">
                                    <i class="ri-edit-line me-1"></i> Edit Data
                                </a>
                                {{-- <button class="btn btn-success" onclick="window.print()">
                                    <i class="ri-printer-line me-1"></i> Print Halaman
                                </button> --}}
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
        function printCard() {
            window.open('{{ route('siswa.print-card', $siswa->id) }}', '_blank');
        }

        function downloadCard() {
            window.open('{{ route('siswa.download-card', $siswa->id) }}', '_blank', 'width=800,height=600');
        }

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
