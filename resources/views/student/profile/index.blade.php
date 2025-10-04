@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Profil Saya</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profil</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <!-- Profile Card -->
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-primary rounded-circle text-white font-20">
                                {{ strtoupper(substr($siswa->user->name, 0, 2)) }}
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $siswa->user->name }}</h4>
                        <p class="text-muted">{{ $siswa->nis }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-primary">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                            @if ($siswa->kelas && $siswa->kelas->jurusan)
                                <span class="badge bg-info">{{ $siswa->kelas->jurusan->nama_jurusan }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Aksi Cepat</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.qrcode') }}" class="btn btn-primary">
                                <i class="ri-qr-code-line me-1"></i>
                                Lihat QR Code
                            </a>
                            <a href="{{ route('student.attendance.history') }}" class="btn btn-outline-primary">
                                <i class="ri-history-line me-1"></i>
                                Riwayat Presensi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Pribadi</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Lengkap</label>
                                    <p class="form-control-plaintext">{{ $siswa->user->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">NIS</label>
                                    <p class="form-control-plaintext">{{ $siswa->nis }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <p class="form-control-plaintext">{{ $siswa->user->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jenis Kelamin</label>
                                    <p class="form-control-plaintext">
                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Lahir</label>
                                    <p class="form-control-plaintext">
                                        {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat</label>
                                    <p class="form-control-plaintext">{{ $siswa->alamat ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Akademik</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kelas</label>
                                    <p class="form-control-plaintext">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tingkat</label>
                                    <p class="form-control-plaintext">{{ $siswa->kelas->tingkatKelas->tingkat ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jurusan</label>
                                    <p class="form-control-plaintext">{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <span class="badge bg-success">Aktif</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Masuk</label>
                                    <p class="form-control-plaintext">{{ $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('d F Y') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Kontak</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">No. Telepon</label>
                                    <p class="form-control-plaintext">{{ $siswa->no_telepon ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
