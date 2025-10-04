<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Pelajar - {{ $siswa->user->name }}</title>
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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

        .barcode-section-landscape {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }


        .validity-text {
            font-size: 10px;
            color: #6e707e;
            text-align: right;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                display: block !important;
                margin: 0 !important;
                min-height: auto !important;
            }

            .student-card-landscape {
                box-shadow: none !important;
                page-break-inside: avoid;
                border: 2px solid #000 !important;
                width: 600px !important;
                height: 380px !important;
                margin: 0 auto !important;
                background: white !important;
            }

            .card-header-simple {
                background: #f8f9fc !important;
                border-bottom: 2px solid #000 !important;
            }

            .card-body-landscape {
                display: flex !important;
                height: 300px !important;
            }

            .no-print {
                display: none !important;
            }

            /* Ensure all content is visible */
            .detail-value,
            .detail-label,
            .school-name-simple,
            .card-type-simple {
                color: #000 !important;
            }

        }
    </style>
</head>

<body>
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
                    <div class="student-photo-landscape d-flex align-items-center justify-content-center bg-light">
                        <i class="ri-user-3-line text-muted" style="font-size: 3rem;"></i>
                    </div>
                @endif

                <!-- QR Code -->
                <div class="qr-code-section">
                    <div style="font-size: 8px; color: #6e707e; margin-bottom: 2px;">QR Code</div>
                    <div style="padding: 2px; background: white; border: 1px solid #e3e6f0; display: inline-block;">
                        <img src="{{ route('student.qrcode.generate', $siswa->nis) }}" alt="QR Code"
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
                    <div class="validity-text"
                        style="text-align: center; padding-top: 15px; border-top: 1px solid #e3e6f0;">
                        <div>Berlaku selama</div>
                        <div>menjadi siswa aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>
