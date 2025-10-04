@extends('layouts.app')
@section('title', 'QR Code Saya')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">QR Code Saya</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">QR Code</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">QR Code Presensi</h5>
                        <p class="text-muted mb-4">
                            Gunakan QR Code ini untuk melakukan presensi. Tunjukkan kepada guru atau scan pada perangkat
                            yang tersedia.
                        </p>

                        <!-- QR Code Display -->
                        <div class="qr-code-container mb-4">
                            <div class="border border-2 border-primary rounded p-4 d-inline-block">
                                <img src="{{ route('student.qrcode.generate', $siswa->nis) }}"
                                    alt="QR Code {{ $siswa->nis }}" class="img-fluid" style="max-width: 300px;">
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">Nama</td>
                                                <td>{{ $siswa->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">NIS</td>
                                                <td>{{ $siswa->nis }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Kelas</td>
                                                <td>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="{{ route('student.qrcode.download', $siswa->nis) }}" class="btn btn-primary">
                                <i class="ri-download-line me-1"></i>
                                Download QR Code
                            </a>
                            <a href="{{ route('student.qrcode.print', $siswa->nis) }}" class="btn btn-outline-primary"
                                target="_blank">
                                <i class="ri-printer-line me-1"></i>
                                Print QR Code
                            </a>
                            {{-- <button type="button" class="btn btn-outline-success" onclick="refreshQrCode()">
                                <i class="ri-refresh-line me-1"></i>
                                Refresh
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="ri-information-line me-2 text-info"></i>
                            Cara Menggunakan QR Code
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Untuk Presensi Manual:</h6>
                                <ol class="ps-3">
                                    <li>Buka aplikasi scanner QR Code</li>
                                    <li>Arahkan kamera ke QR Code di atas</li>
                                    <li>Tunjukkan hasil scan kepada guru</li>
                                    <li>Guru akan mencatat kehadiran Anda</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Untuk Presensi Otomatis:</h6>
                                <ol class="ps-3">
                                    <li>Cari perangkat scanner di kelas</li>
                                    <li>Tunjukkan QR Code ke scanner</li>
                                    <li>Tunggu bunyi konfirmasi</li>
                                    <li>Presensi otomatis tercatat</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">
                            <i class="ri-lightbulb-line me-2"></i>
                            Tips Penting
                        </h6>
                        <ul class="mb-0">
                            <li>Pastikan QR Code dalam kondisi bersih dan tidak rusak</li>
                            <li>Gunakan pencahayaan yang cukup saat melakukan scan</li>
                            <li>Jaga jarak yang tepat antara kamera dan QR Code</li>
                            <li>Jangan berbagi QR Code dengan orang lain</li>
                            <li>Laporkan ke guru jika ada masalah dengan QR Code</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function refreshQrCode() {
            // Get the QR code image element
            const qrCodeImg = document.querySelector('.qr-code-container img');

            // Add timestamp to force refresh
            const currentSrc = qrCodeImg.src.split('?')[0];
            qrCodeImg.src = currentSrc + '?t=' + new Date().getTime();

            // Show success message
            showToast('QR Code berhasil di-refresh!', 'success');
        }

        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

            // Add to body
            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }

        // Auto refresh QR code every 5 minutes to ensure it's always fresh
        setInterval(function() {
            refreshQrCode();
        }, 300000); // 5 minutes
    </script>
@endpush

@push('styles')
    <style>
        .qr-code-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }

        .qr-code-container .border {
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media print {

            .btn,
            .breadcrumb,
            .page-title-box {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush
