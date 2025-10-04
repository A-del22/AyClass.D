<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Pelajar Kelas {{ $kelas->nama_kelas }}</title>
    {{-- <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet"> --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .page-header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #6c757d;
            font-size: 16px;
        }

        .cards-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .student-card-landscape {
            width: 600px;
            height: 380px;
            background: #ffffff;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            page-break-inside: avoid;
            margin: 0 auto 20px auto;
        }

        .card-header-simple {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }

        .school-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
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

        .id-text {
            font-size: 10px;
            color: #6e707e;
        }

        .card-body-landscape {
            display: flex;
            padding: 20px;
            gap: 20px;
            height: 300px;
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

        .qr-code-section {
            text-align: center;
            margin-top: 10px;
        }

        .qr-label {
            font-size: 8px;
            color: #6e707e;
            margin-bottom: 2px;
        }

        .info-section-landscape {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .student-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
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

        .validity-text {
            font-size: 10px;
            color: #6e707e;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .print-btn {
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .print-btn:hover {
            background: #1d4ed8;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-controls,
            .page-header {
                display: none !important;
            }

            .cards-container {
                margin: 0;
                gap: 10mm;
            }

            .student-card-landscape {
                box-shadow: none !important;
                page-break-inside: avoid;
                page-break-after: always;
                border: 2px solid #000 !important;
                width: 600px !important;
                height: 380px !important;
                margin: 0 auto 10mm auto !important;
                background: white !important;
            }

            .student-card-landscape:last-child {
                page-break-after: auto;
            }

            .card-header-simple {
                background: #f8f9fc !important;
                border-bottom: 2px solid #000 !important;
            }

            .card-body-landscape {
                display: flex !important;
                height: 300px !important;
            }

            .student-details {
                grid-template-columns: 1fr 1fr !important;
            }

            /* Ensure all content is visible */
            .detail-value,
            .detail-label,
            .school-name-simple,
            .card-type-simple,
            .id-text {
                color: #000 !important;
            }

            @page {
                margin: 10mm;
                size: A4;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <button class="print-btn" onclick="window.print()">
            <i class="ri-printer-line"></i> Cetak Semua
        </button>
        <button class="print-btn" onclick="window.close()" style="background: #6c757d;">
            <i class="ri-close-line"></i> Tutup
        </button>
    </div>

    <div class="page-header">
        <h1>Kartu Pelajar Kelas {{ $kelas->nama_kelas }}</h1>
        <p>{{ $appSettings->nama_sekolah ?? 'SMA NEGERI 1 SURAKARTA' }} - Total: {{ $siswaList->count() }} siswa</p>
    </div>

    <div class="cards-container">
        @foreach ($siswaList as $siswa)
            <div class="student-card-landscape">
                <!-- Header -->
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
                    <div class="id-text">ID: {{ $siswa->nis }}</div>
                </div>

                <!-- Body -->
                <div class="card-body-landscape">
                    <!-- Photo Section -->
                    <div class="photo-section-landscape">
                        @if ($siswa->foto == 'avatar.png')
                            <img src="{{ asset('avatar.png') }}" alt="Foto {{ $siswa->user->name }}"
                                class="student-photo-landscape">
                        @elseif($siswa->foto)
                            <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto {{ $siswa->user->name }}"
                                class="student-photo-landscape">
                        @else
                            <div class="student-photo-landscape"
                                style="display: flex; align-items: center; justify-content: center; background: #f8f9fa; border: 1px solid #e3e6f0;">
                                <i class="ri-user-3-line" style="font-size: 3rem; color: #6c757d;"></i>
                            </div>
                        @endif

                        <!-- QR Code -->
                        <div class="qr-code-section">
                            <div style="font-size: 8px; color: #6e707e; margin-bottom: 2px;">QR Code</div>
                            <div
                                style="padding: 2px; background: white; border: 1px solid #e3e6f0; display: inline-block;">
                                <img src="{{ route('siswa.qrcode', $siswa->id) }}" alt="QR Code"
                                    style="width: 80px; height: 80px;">
                            </div>
                        </div>
                    </div>

                    <!-- Information Section -->
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

                        <!-- Validitas -->
                        <div class="validity-section">
                            <div class="validity-text">
                                <div>Berlaku selama</div>
                                <div>menjadi siswa aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- <script>
        // Auto print when page loads (optional - can be removed if not desired)
        window.addEventListener('load', function() {
            // Remove auto-print to let user control when to print
            console.log('Page loaded - ready to print');
        });

        // Print event handling
        window.addEventListener('beforeprint', function() {
            console.log('Print dialog opened');
        });

        window.addEventListener('afterprint', function() {
            console.log('Print dialog closed');
        });
    </script> --}}
</body>

</html>
