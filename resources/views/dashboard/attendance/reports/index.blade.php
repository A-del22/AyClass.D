@extends('layouts.app')

@section('title', 'Rekap & Laporan Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Presensi</a></li>
                        <li class="breadcrumb-item active">Rekap & Laporan</li>
                    </ol>
                </div>
                <h4 class="page-title">Rekap & Laporan Presensi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Pilih Jenis Laporan</h5>

                    <div class="row">
                        <!-- Laporan Harian -->
                        <div class="col-lg-6 col-xl-3 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-primary rounded-circle">
                                            <i class="ri-calendar-line font-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mt-3">Laporan Harian</h5>
                                    <p class="text-muted">Rekap presensi per hari dengan detail siswa dan kelas</p>
                                    <a href="{{ route('attendance.reports.daily') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-eye-line me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan Bulanan -->
                        <div class="col-lg-6 col-xl-3 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-success rounded-circle">
                                            <i class="ri-calendar-2-line font-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mt-3">Laporan Bulanan</h5>
                                    <p class="text-muted">Rekap presensi per bulan dengan statistik kehadiran</p>
                                    <a href="{{ route('attendance.reports.monthly') }}" class="btn btn-success btn-sm">
                                        <i class="ri-eye-line me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan Tahunan -->
                        <div class="col-lg-6 col-xl-3 mb-3">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-danger rounded-circle">
                                            <i class="ri-calendar-event-line font-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mt-3">Laporan Tahunan</h5>
                                    <p class="text-muted">Rekap presensi per tahun dengan statistik bulanan</p>
                                    <a href="{{ route('attendance.reports.yearly') }}" class="btn btn-danger btn-sm">
                                        <i class="ri-eye-line me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan per Siswa -->
                        <div class="col-lg-6 col-xl-3 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-info rounded-circle">
                                            <i class="ri-user-line font-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mt-3">Laporan per Siswa</h5>
                                    <p class="text-muted">Riwayat kehadiran individual siswa dengan detail</p>
                                    <a href="{{ route('attendance.reports.student') }}" class="btn btn-info btn-sm">
                                        <i class="ri-eye-line me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan per Kelas -->
                        <div class="col-lg-6 col-xl-3 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-warning rounded-circle">
                                            <i class="ri-group-line font-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mt-3">Laporan per Kelas</h5>
                                    <p class="text-muted">Statistik kehadiran per kelas dengan persentase</p>
                                    <a href="{{ route('attendance.reports.class') }}" class="btn btn-warning btn-sm">
                                        <i class="ri-eye-line me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Informasi Laporan:</h6>
                                <ul class="mb-0">
                                    <li><strong>Laporan Harian:</strong> Menampilkan data presensi per tanggal tertentu dengan filter kelas</li>
                                    <li><strong>Laporan Bulanan:</strong> Menampilkan statistik kehadiran dalam satu bulan dengan grafik harian</li>
                                    <li><strong>Laporan Tahunan:</strong> Menampilkan statistik kehadiran dalam satu tahun dengan grafik bulanan</li>
                                    <li><strong>Laporan per Siswa:</strong> Menampilkan riwayat kehadiran siswa dalam rentang tanggal tertentu</li>
                                    <li><strong>Laporan per Kelas:</strong> Menampilkan perbandingan kehadiran semua siswa dalam satu kelas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection