@extends('layouts.app')
@section('title', 'Kartu Siswa - ' . $kelas->nama_kelas)

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
            margin: 20px auto;
            font-family: 'Arial', sans-serif;
            page-break-inside: avoid;
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

        .print-controls {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
        }

        .print-button {
            padding: 12px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            min-width: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .print-button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .print-button:active {
            transform: translateY(0);
        }

        .print-button.pdf-btn {
            background: #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .print-button.pdf-btn:hover {
            background: #047857;
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }

        .page-header {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .cards-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            margin: 20px 0;
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
        }

        @media (max-width: 767px) and (min-width: 576px) {
            .student-card-landscape {
                max-width: 100%;
                margin: 20px 10px;
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
        }

        @media (max-width: 768px) {
            .print-controls {
                position: relative;
                top: auto;
                right: auto;
                display: flex;
                justify-content: center;
                margin: 20px auto;
                width: fit-content;
            }

            .print-button {
                min-width: auto;
                padding: 12px 20px;
                font-size: 14px;
            }
        }

        @media (max-width: 575px) {
            .student-card-landscape {
                margin: 20px 5px;
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
        }

        @media print {
            .print-controls,
            .page-header {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .cards-container {
                margin: 0;
                gap: 10mm;
            }

            .student-card-landscape {
                box-shadow: none;
                page-break-inside: avoid;
                border: 1px solid #000;
                width: 600px;
                height: 380px;
                margin: 0 auto 10mm auto;
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

            @page {
                margin: 10mm;
                size: A4;
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
    </style>
@endpush

@section('content')
    <div class="print-controls">
        <button class="print-button pdf-btn" onclick="openPrintView()" title="Buka tampilan cetak PDF">
            <i class="ri-file-pdf-line"></i>
            <span>Cetak PDF</span>
        </button>
    </div>

    <div class="page-header">
        <h2 class="mb-2">Kartu Siswa Kelas {{ $kelas->nama_kelas }}</h2>
        <p class="text-muted mb-0">
            {{ $appSettings->nama_sekolah ?? 'SMA NEGERI 1 SURAKARTA' }} -
            Total: {{ $siswaList->count() }} siswa
        </p>
    </div>

    <div class="cards-container">
        @foreach($siswaList as $siswa)
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
        @endforeach
    </div>

    <script>
        function openPrintView() {
            const kelasId = "{{ $kelas->id }}";
            const printUrl = "{{ route('siswa.cards.class.print', ':kelasId') }}".replace(':kelasId', kelasId);
            window.open(printUrl, '_blank', 'width=1200,height=800');
        }
    </script>
@endsection