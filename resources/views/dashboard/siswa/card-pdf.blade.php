<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Pelajar - {{ $siswa->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            width: 600px;
            height: 380px;
            margin: 0;
            padding: 0;
        }

        .student-card-landscape {
            width: 600px;
            height: 380px;
            background: #ffffff !important;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
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
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #e3e6f0;
            margin-bottom: 10px;
        }

        .qr-code-section {
            text-align: center;
            margin-top: 10px;
        }


        .info-section-landscape {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .student-details {
            display: block;
            margin-bottom: 20px;
        }

        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .detail-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 11px;
            color: #6e707e !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .detail-value {
            font-size: 13px;
            color: #2c3e50 !important;
            font-weight: 600;
            word-wrap: break-word;
            line-height: 1.3;
        }

        .barcode-section-landscape {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
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
    </style>
</head>

<body>
    <div class="student-card-landscape">
        <div class="card-header-simple">
            <div class="school-info">
                <div class="school-logo-simple">
                    <img src="{{ public_path('logo.png') }}" alt="Logo Sekolah"
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

        <div class="card-body-landscape">
            <div class="photo-section-landscape">
                @if ($siswa->foto == 'avatar.png')
                    <img src="{{ public_path('avatar.png') }}" alt="Foto {{ $siswa->user->name }}"
                        class="student-photo-landscape">
                @elseif($siswa->foto)
                    <img src="{{ public_path('storage/' . $siswa->foto) }}" alt="Foto {{ $siswa->user->name }}"
                        class="student-photo-landscape">
                @else
                    <div class="student-photo-landscape"
                        style="display: flex; align-items: center; justify-content: center; background: #f8f9fa; color: #6c757d; font-size: 3rem;">
                        👤
                    </div>
                @endif

                <div class="qr-code-section">
                    <div style="font-size: 8px; color: #6e707e; margin-bottom: 2px;">QR Code</div>
                    <div style="padding: 2px; background: white; border: 1px solid #e3e6f0; display: inline-block;">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($siswa->nis, 'QRCODE', 4, 4) }}"
                            alt="QR Code" style="width: 80px; height: 80px;">
                    </div>
                </div>
            </div>

            <div class="info-section-landscape">
                <div class="student-details">
                    <div class="detail-row">
                        <div class="detail-col">
                            <div class="detail-item">
                                <div class="detail-label">NAMA LENGKAP</div>
                                <div class="detail-value">{{ $siswa->user->name }}</div>
                            </div>
                        </div>
                        <div class="detail-col">
                            <div class="detail-item">
                                <div class="detail-label">NIS</div>
                                <div class="detail-value">{{ $siswa->nis }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-col">
                            <div class="detail-item">
                                <div class="detail-label">KELAS</div>
                                <div class="detail-value">
                                    {{ $siswa->kelas ? $siswa->kelas->nama_kelas : 'Belum ada kelas' }}
                                </div>
                            </div>
                        </div>
                        <div class="detail-col">
                            <div class="detail-item">
                                <div class="detail-label">JENIS KELAMIN</div>
                                <div class="detail-value">
                                    {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-col">
                            <div class="detail-item">
                                <div class="detail-label">TANGGAL LAHIR</div>
                                <div class="detail-value">{{ $siswa->tanggal_lahir->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        <div class="detail-col">
                            <div class="detail-item">
                                <div class="detail-label">TAHUN MASUK</div>
                                <div class="detail-value">{{ $siswa->tanggal_masuk->format('Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="validity-section">
                    <div
                        style="text-align: center; padding-top: 15px; border-top: 1px solid #e3e6f0; font-size: 10px; color: #6e707e;">
                        <div>Berlaku selama</div>
                        <div>menjadi siswa aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
