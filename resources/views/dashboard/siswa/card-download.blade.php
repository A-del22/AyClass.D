<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Download Kartu Pelajar - {{ $siswa->user->name }}</title>
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .download-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        /* Tanpa card wrapper - content only */
        .student-card-landscape {
            width: 600px;
            height: 380px;
            background: #ffffff;
            position: relative;
            font-family: 'Arial', sans-serif;
            border: 1px solid #ddd;
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

        .student-photo-bg {
            width: 120px;
            height: 150px;
            margin-bottom: 10px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            background-color: transparent;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }

        /* Khusus untuk placeholder icon */
        .student-photo-placeholder {
            width: 120px;
            height: 150px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .validity-text {
            font-size: 10px;
            color: #6e707e;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }

        .download-controls {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            margin: 0 5px;
        }

        #downloadStatus {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="download-container">
        <h4 class="text-center">Download Kartu Pelajar</h4>

        <div id="cardToCapture" class="student-card-landscape">
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
                <div class="text-end">
                    <div style="font-size: 10px; color: #6e707e;">ID: {{ $siswa->nis }}</div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body-landscape">
                <!-- Foto -->
                <div class="photo-section-landscape">
                    @if ($siswa->foto == 'avatar.png')
                        <div class="student-photo-bg" style="background-image: url('{{ asset('avatar.png') }}');"></div>
                    @elseif($siswa->foto)
                        <div class="student-photo-bg"
                            style="background-image: url('{{ asset('storage/' . $siswa->foto) }}');"></div>
                    @else
                        <div class="student-photo-placeholder">
                            <i class="ri-user-3-line text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <!-- QR Code -->
                    <div class="qr-code-section">
                        <div style="font-size: 8px; color: #6e707e; margin-bottom: 2px;">QR Code</div>
                        <div class="d-inline-block" style="padding: 2px; background: white; border: 1px solid #e3e6f0;">
                            <img src="{{ route('siswa.qrcode', $siswa->id) }}" alt="QR Code"
                                style="width: 80px; height: 80px;">
                        </div>
                    </div>
                </div>

                <!-- Informasi -->
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

        <div class="download-controls">
            <button id="downloadBtn" class="btn btn-success">
                <i class="ri-download-line me-1"></i>Download sebagai Gambar
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="ri-close-line me-1"></i>Tutup
            </button>
            <div id="downloadStatus"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.getElementById('downloadBtn').addEventListener('click', function() {
            const button = this;
            const status = document.getElementById('downloadStatus');

            button.disabled = true;
            button.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Memproses...';
            status.innerHTML = '<div class="text-info">Sedang memproses kartu...</div>';

            // Wait for images to load first
            const images = document.querySelectorAll('#cardToCapture img');
            let imagesLoaded = 0;
            const totalImages = images.length;

            function checkImagesLoaded() {
                imagesLoaded++;
                if (imagesLoaded >= totalImages) {
                    captureCard();
                }
            }

            if (totalImages > 0) {
                images.forEach(img => {
                    if (img.complete) {
                        checkImagesLoaded();
                    } else {
                        img.onload = checkImagesLoaded;
                        img.onerror = checkImagesLoaded;
                    }
                });
            } else {
                captureCard();
            }

            function captureCard() {
                // Alternative approach: capture without forcing scale
                html2canvas(document.getElementById('cardToCapture'), {
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    scale: 3,
                    width: 600,
                    height: 380,
                    dpi: 300,
                    imageTimeout: 0,
                    onclone: function(clonedDoc) {
                        // Ensure crisp rendering
                        clonedDoc.body.style.imageRendering = 'crisp-edges';
                        clonedDoc.body.style.imageRendering = '-webkit-optimize-contrast';

                        const photoBgs = clonedDoc.querySelectorAll('.student-photo-bg');
                        photoBgs.forEach(bg => {
                            bg.style.imageRendering = 'crisp-edges';
                            bg.style.imageRendering = '-webkit-optimize-contrast';
                        });
                    }
                }).then(function(canvas) {
                    // Create download link
                    const link = document.createElement('a');
                    link.download = 'kartu-pelajar-{{ $siswa->nis }}.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();

                    // Reset button
                    button.disabled = false;
                    button.innerHTML = '<i class="ri-download-line me-1"></i>Download sebagai Gambar';
                    status.innerHTML = '<div class="text-success">Kartu berhasil didownload!</div>';

                    // Auto close after 2 seconds
                    setTimeout(() => {
                        window.close();
                    }, 2000);
                }).catch(function(error) {
                    button.disabled = false;
                    button.innerHTML = '<i class="ri-download-line me-1"></i>Download sebagai Gambar';
                    status.innerHTML =
                        '<div class="text-danger">Terjadi kesalahan saat memproses kartu.</div>';
                });
            }
        });

        // Auto trigger download after page loads
        window.addEventListener('load', function() {
            // Wait longer for all images to fully render
            setTimeout(() => {
                document.getElementById('downloadBtn').click();
            }, 2000);
        });
    </script>
</body>

</html>
