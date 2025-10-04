@extends('layouts.app')
@section('title', 'Scan QR Code Presensi')

@push('styles')
    <style>
        .scan-container {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .qr-reader {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            border: 2px solid #28a745;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            min-height: 300px;
        }

        .qr-reader video {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Style HTML5 QR Code built-in controls */
        #qr-reader__dashboard_section {
            padding: 10px;
            border-top: 1px solid #dee2e6;
            background: #f8f9fa;
        }

        #qr-reader__dashboard_section button {
            background: #17a2b8;
            border: none;
            border-radius: 6px;
            color: white;
            padding: 0.5rem 1rem;
            font-weight: 500;
            margin: 0.25rem;
            cursor: pointer;
        }

        #qr-reader__dashboard_section button:hover {
            background: #138496;
        }

        #qr-reader__dashboard_section select {
            background: white;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0.5rem;
            margin: 0.25rem;
        }

        .scan-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 180px;
            height: 180px;
            border: 2px solid #28a745;
            border-radius: 8px;
            pointer-events: none;
        }

        .scan-status {
            text-align: center;
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 6px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .scan-instructions {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .result-card {
            background: #fff;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }

        .result-success {
            border-left: 4px solid #28a745;
            background: #d4edda;
        }

        .result-error {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
        }

        .btn-restart {
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            margin-top: 1rem;
        }

        .btn-restart:hover {
            background-color: #0056b3;
            color: white;
        }

        .camera-controls {
            text-align: center;
            margin-top: 1rem;
            gap: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .camera-btn {
            background-color: #17a2b8;
            border: none;
            border-radius: 6px;
            color: white;
            padding: 0.5rem 1rem;
            font-weight: 500;
            margin: 0.25rem;
        }

        .camera-btn:hover {
            background-color: #138496;
            color: white;
        }

        .camera-btn:disabled {
            background-color: #6c757d;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .loading-spinner {
            text-align: center;
            margin: 1rem 0;
        }

        .spinner-border {
            color: #28a745;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 0.5rem;
            }

            .scan-container {
                padding: 1rem;
                margin-bottom: 0.5rem;
            }

            .scan-instructions {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .qr-reader {
                max-width: 100%;
                min-height: 250px;
            }

            .scan-overlay {
                width: 150px;
                height: 150px;
            }

            .camera-controls {
                flex-direction: column;
                align-items: center;
            }

            .camera-btn {
                width: 200px;
                margin: 0.25rem 0;
            }

            .result-card {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .scan-instructions h5 {
                font-size: 1.1rem;
            }

            .scan-instructions p {
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .qr-reader {
                min-height: 200px;
            }

            .scan-overlay {
                width: 120px;
                height: 120px;
            }

            .camera-btn {
                width: 100%;
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <x-breadcrumbs title="Scan QR Code Presensi" />

        <!-- Class Info -->
        @if (isset($kelas))
            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="ri-team-line text-primary me-2"></i>{{ $kelas->nama_kelas }}
                                    </h5>
                                    <p class="text-muted mb-0">Scan QR Code untuk presensi siswa</p>
                                </div>
                                <div>
                                    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
                                        <i class="ri-arrow-left-line me-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <!-- Instructions -->
                <div class="scan-instructions">
                    <h5 class="mb-3"><i class="ri-qr-scan-line me-2"></i>Petunjuk Penggunaan</h5>
                    <div class="text-start">
                        <p class="mb-2">• Pastikan kamera dapat mengakses QR Code dengan jelas</p>
                        <p class="mb-2">• Arahkan kamera ke QR Code pada kartu siswa</p>
                        <p class="mb-2">• Gunakan kontrol kamera yang tersedia untuk memilih kamera atau mengatur zoom</p>
                        <p class="mb-0">• Tunggu hingga sistem memproses data presensi</p>
                    </div>
                </div>

                <!-- Scanner Container -->
                <div class="scan-container">
                    <div class="text-center mb-3">
                        <h6 class="text-muted mb-0">Arahkan Kamera ke QR Code</h6>
                    </div>

                    <!-- QR Reader -->
                    <div id="qr-reader" class="qr-reader">
                        <div class="scan-overlay"></div>
                    </div>

                    <!-- Scanner Controls -->
                    <div class="camera-controls">
                        <button id="start-scan" class="btn camera-btn">
                            <i class="ri-play-line me-1"></i>Mulai Scan
                        </button>
                        <button id="stop-scan" class="btn camera-btn" disabled>
                            <i class="ri-stop-line me-1"></i>Stop Scan
                        </button>
                    </div>

                    <!-- Scan Status -->
                    <div class="scan-status">
                        <div id="scan-message">Klik "Mulai Scan" untuk memulai</div>
                        <div id="loading" class="loading-spinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">Memproses data...</p>
                        </div>
                    </div>
                </div>

                <!-- Result Card -->
                <div id="result-card" class="result-card">
                    <div id="result-content"></div>
                    <div class="text-center">
                        <button id="scan-again" class="btn btn-restart" style="display: none;">
                            <i class="ri-refresh-line me-1"></i>Scan Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            let html5QrcodeScanner = null;
            let isScanning = false;
            let autoScanTimeout = null;

            function onScanSuccess(decodedText, decodedResult) {
                if (isScanning) {
                    // Pause scanning temporarily
                    isScanning = false;

                    $('#loading').show();
                    $('#scan-message').text('Memproses data presensi...');

                    // Process the scanned QR code
                    $.ajax({
                        url: '{{ route('attendance.process-qr') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nis: decodedText,
                            kelas_id: '{{ $kelas->id }}'
                        },
                        success: function(response) {
                            $('#loading').hide();
                            if (response.success) {
                                showResult(true, response.message, response.data, true);
                                // Auto continue scanning after 2 seconds for successful scans
                                autoScanTimeout = setTimeout(() => {
                                    $('#result-card').fadeOut();
                                    isScanning = true;
                                    $('#scan-message').text(
                                        'Scanning... Arahkan kamera ke QR Code');
                                }, 2000);
                            } else {
                                showResult(false, response.message, response.data, false);
                                // For errors, also auto continue after 3 seconds
                                autoScanTimeout = setTimeout(() => {
                                    $('#result-card').fadeOut();
                                    isScanning = true;
                                    $('#scan-message').text(
                                        'Scanning... Arahkan kamera ke QR Code');
                                }, 3000);
                            }
                        },
                        error: function(xhr) {
                            $('#loading').hide();
                            let message = 'Terjadi kesalahan saat memproses QR Code';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showResult(false, message, null, false);
                            // Auto continue after error
                            autoScanTimeout = setTimeout(() => {
                                $('#result-card').fadeOut();
                                isScanning = true;
                                $('#scan-message').text(
                                'Scanning... Arahkan kamera ke QR Code');
                            }, 3000);
                        }
                    });
                }
            }

            function onScanFailure(error) {
                // Handle scan failures
            }

            function showResult(success, message, data = null, autoHide = false) {
                const resultCard = $('#result-card');
                const resultContent = $('#result-content');

                resultCard.removeClass('result-success result-error');
                resultCard.addClass(success ? 'result-success' : 'result-error');

                let icon = success ? 'ri-check-circle-line text-success' : 'ri-close-circle-line text-danger';
                let html = '<div class="text-center">';
                html += '<i class="' + icon + '" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>';
                html += '<h6>' + message + '</h6>';

                if (data) {
                    if (success) {
                        html += '<div class="mt-2 text-start">';
                        html += '<div class="row">';
                        html += '<div class="col-6"><small><strong>Nama:</strong></small></div>';
                        html += '<div class="col-6"><small>' + data.nama + '</small></div>';
                        html += '<div class="col-6"><small><strong>NIS:</strong></small></div>';
                        html += '<div class="col-6"><small>' + data.nis + '</small></div>';
                        html += '<div class="col-6"><small><strong>Kelas:</strong></small></div>';
                        html += '<div class="col-6"><small>' + data.kelas + '</small></div>';
                        html += '<div class="col-6"><small><strong>Status:</strong></small></div>';
                        html += '<div class="col-6"><small>Hadir</small></div>';
                        html += '<div class="col-6"><small><strong>Waktu:</strong></small></div>';
                        html += '<div class="col-6"><small>' + data.waktu + '</small></div>';
                        html += '</div>';
                        html += '</div>';
                    } else if (data && data.status) {
                        html += '<div class="mt-2">';
                        html += '<small><strong>Status sebelumnya:</strong> ' + data.status + '</small>';
                        if (data.waktu) {
                            html += '<br><small><strong>Waktu masuk:</strong> ' + data.waktu + '</small>';
                        }
                        html += '</div>';
                    }
                }

                if (autoHide) {
                    html +=
                    '<div class="mt-2"><small class="text-muted">Melanjutkan scan otomatis...</small></div>';
                }

                html += '</div>';

                resultContent.html(html);
                resultCard.fadeIn();

                // Hide scan again button for auto-hide results
                if (autoHide) {
                    $('#scan-again').hide();
                } else {
                    $('#scan-again').show();
                }
            }


            function startScanning() {
                // Clear any existing timeout
                if (autoScanTimeout) {
                    clearTimeout(autoScanTimeout);
                    autoScanTimeout = null;
                }

                if (!html5QrcodeScanner) {
                    const config = {
                        fps: 10,
                        qrbox: {
                            width: 180,
                            height: 180
                        },
                        aspectRatio: 1.0,
                        facingMode: "environment", // Prefer back camera
                        showTorchButtonIfSupported: true, // Show flashlight if available
                        showZoomSliderIfSupported: true, // Show zoom if available
                        defaultZoomValueIfSupported: 2 // Default zoom level
                    };

                    html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", config, false);
                }

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                isScanning = true;

                $('#start-scan').prop('disabled', true);
                $('#stop-scan').prop('disabled', false);
                $('#scan-message').text('Scanning... Arahkan kamera ke QR Code');
                $('#result-card').hide();
            }

            $('#start-scan').click(function() {
                startScanning();
            });

            $('#stop-scan').click(function() {
                if (html5QrcodeScanner) {
                    // Clear any auto scan timeout
                    if (autoScanTimeout) {
                        clearTimeout(autoScanTimeout);
                        autoScanTimeout = null;
                    }

                    html5QrcodeScanner.clear().then(() => {
                        isScanning = false;
                        $('#start-scan').prop('disabled', false);
                        $(this).prop('disabled', true);
                        $('#scan-message').text(
                            'Scan dihentikan. Klik "Mulai Scan" untuk memulai lagi');
                    });
                }
            });

            $('#scan-again').click(function() {
                $('#result-card').fadeOut();
                startScanning();
            });

        });
    </script>
@endpush
