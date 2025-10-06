@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard Siswa</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h5 class="mb-1">Selamat datang, {{ $siswa->user->name }}!</h5>
                            <p class="text-muted mb-0">
                                NIS: {{ $siswa->nis }} | Kelas: {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                            </p>
                        </div>
                        <div class="col-sm-4 text-end">
                            <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Status -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status Hari Ini</h5>
                    @if ($todayAttendance)
                        <div class="row">
                            <div class="col-md-6">
                                @php
                                    $badgeClass = match ($todayAttendance->status) {
                                        'hadir' => 'bg-success',
                                        'izin' => 'bg-info',
                                        'sakit' => 'bg-secondary',
                                        default => 'bg-secondary',
                                    };

                                    $statusText = match ($todayAttendance->status) {
                                        'hadir' => 'Hadir',
                                        'izin' => 'Izin',
                                        'sakit' => 'Sakit',
                                        default => ucfirst($todayAttendance->status),
                                    };
                                @endphp
                                <p><strong>Status:</strong> <span class="badge {{ $badgeClass }}">{{ $statusText }}</span></p>
                                @if ($todayAttendance->waktu_masuk)
                                    <p><strong>Waktu Masuk:</strong> {{ $todayAttendance->waktu_masuk->format('H:i:s') }}</p>
                                @endif
                                @if ($todayAttendance->keterangan)
                                    <p><strong>Keterangan:</strong> {{ $todayAttendance->keterangan }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>Method:</strong>
                                    <span class="badge {{ $todayAttendance->method === 'qr_scan' ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $todayAttendance->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="ri-calendar-close-line font-48 text-muted mb-3"></i>
                            <h6 class="text-muted">Belum Presensi hari ini</h6>
                            <p class="text-muted">Silakan lakukan presensi melalui QR Code atau hubungi guru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="my-2">{{ $monthlyStats['total_hari_sekolah'] }}</h3>
                    <p class="text-muted mb-0">Hari Sekolah</p>
                    <small class="text-muted">Bulan ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="my-2 text-success">{{ $monthlyStats['total_hadir'] }}</h3>
                    <p class="text-muted mb-0">Hadir</p>
                    <small class="text-success">
                        {{ $monthlyStats['total_hari_sekolah'] > 0 ? round(($monthlyStats['total_hadir'] / $monthlyStats['total_hari_sekolah']) * 100, 1) : 0 }}%
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="my-2 text-info">{{ $monthlyStats['total_izin'] + $monthlyStats['total_sakit'] }}</h3>
                    <p class="text-muted mb-0">Izin & Sakit</p>
                    <small class="text-info">
                        {{ $monthlyStats['total_hari_sekolah'] > 0 ? round((($monthlyStats['total_izin'] + $monthlyStats['total_sakit']) / $monthlyStats['total_hari_sekolah']) * 100, 1) : 0 }}%
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="my-2 text-{{ $monthlyStats['persentase_kehadiran'] >= 75 ? 'success' : 'danger' }}">
                        {{ $monthlyStats['persentase_kehadiran'] }}%
                    </h3>
                    <p class="text-muted mb-0">Persentase</p>
                    <small class="text-nowrap">
                        {{ $monthlyStats['persentase_kehadiran'] >= 75 ? 'Baik' : 'Perlu Perhatian' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance & Quick Actions -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Riwayat Presensi (7 Hari Terakhir)</h5>
                        <a href="{{ route('student.attendance.history') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Status</th>
                                    <th>Waktu Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendance as $attendance)
                                    <tr>
                                        <td>{{ $attendance->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $attendance->tanggal->format('l') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match ($attendance->status) {
                                                    'hadir' => 'bg-success',
                                                    'izin' => 'bg-info',
                                                    'sakit' => 'bg-secondary',
                                                    default => 'bg-secondary',
                                                };

                                                $statusText = match ($attendance->status) {
                                                    'hadir' => 'Hadir',
                                                    'izin' => 'Izin',
                                                    'sakit' => 'Sakit',
                                                    default => ucfirst($attendance->status),
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>{{ $attendance->waktu_masuk ? $attendance->waktu_masuk->format('H:i:s') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data presensi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Menu Cepat</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.profile') }}" class="btn btn-outline-primary">
                            <i class="ri-user-line me-1"></i> Lihat Profil
                        </a>
                        <a href="{{ route('student.attendance.history') }}" class="btn btn-outline-info">
                            <i class="ri-history-line me-1"></i> Riwayat Presensi
                        </a>
                        <a href="{{ route('student.qrcode') }}" class="btn btn-outline-success">
                            <i class="ri-qr-code-line me-1"></i> QR Code Saya
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Siswa</h5>
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Nama:</strong></td>
                                <td>{{ $siswa->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>NIS:</strong></td>
                                <td>{{ $siswa->nis }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kelas:</strong></td>
                                <td>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jurusan:</strong></td>
                                <td>{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-success">Aktif</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection