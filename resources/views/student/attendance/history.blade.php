@extends('layouts.app')
@section('title', 'Riwayat Presensi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Presensi Saya</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Presensi Saya</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.attendance.history') }}" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <!-- Filter Type -->
                            <div class="col-md-3">
                                <label for="type" class="form-label">Jenis Filter</label>
                                <select class="form-select" id="type" name="type" onchange="handleTypeChange()">
                                    <option value="daily" {{ $type == 'daily' ? 'selected' : '' }}>Harian</option>
                                    <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="semester" {{ $type == 'semester' ? 'selected' : '' }}>Semester</option>
                                </select>
                            </div>

                            <!-- Monthly Date Input -->
                            <div class="col-md-3" id="monthly-input" style="display: {{ $type == 'monthly' ? 'block' : 'none' }};">
                                <label for="monthly_date" class="form-label">Bulan</label>
                                <input type="month" class="form-control" id="monthly_date" name="date"
                                       value="{{ $type == 'monthly' ? ($date ?? $displayDate ?? date('Y-m')) : '' }}">
                            </div>

                            <!-- Semester Year Input -->
                            <div class="col-md-2" id="semester-year" style="display: {{ $type == 'semester' ? 'block' : 'none' }};">
                                <label for="year" class="form-label">Tahun</label>
                                <select class="form-select" id="year" name="year">
                                    @for($y = 2020; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Semester Select -->
                            <div class="col-md-2" id="semester-select" style="display: {{ $type == 'semester' ? 'block' : 'none' }};">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester">
                                    <option value="1" {{ $semester == 1 ? 'selected' : '' }}>Semester 1</option>
                                    <option value="2" {{ $semester == 2 ? 'selected' : '' }}>Semester 2</option>
                                </select>
                            </div>

                            <!-- Filter Button -->
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-search-line"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily View -->
    @if($type == 'daily')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Presensi Hari Ini</h5>
                        @if($attendanceData)
                            <div class="text-center">
                                @php
                                    $statusConfig = [
                                        'hadir' => ['class' => 'bg-success', 'text' => 'Hadir'],
                                        'izin' => ['class' => 'bg-info', 'text' => 'Izin'],
                                        'sakit' => ['class' => 'bg-secondary', 'text' => 'Sakit'],
                                    ];
                                    $status = $statusConfig[$attendanceData->status] ?? ['class' => 'bg-secondary', 'text' => ucfirst($attendanceData->status)];
                                @endphp

                                <div class="mb-3">
                                    <span class="badge {{ $status['class'] }} fs-6 px-3 py-2">{{ $status['text'] }}</span>
                                </div>

                                <div class="row">
                                    @if($attendanceData->waktu_masuk)
                                    <div class="col-md-4">
                                        <p><strong>Waktu Masuk:</strong><br>{{ \Carbon\Carbon::parse($attendanceData->waktu_masuk)->format('H:i:s') }}</p>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <p><strong>Method:</strong><br>
                                            <span class="badge {{ $attendanceData->method == 'qr_scan' ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $attendanceData->method == 'qr_scan' ? 'QR Scan' : 'Manual' }}
                                            </span>
                                        </p>
                                    </div>
                                    @if($attendanceData->keterangan)
                                    <div class="col-md-4">
                                        <p><strong>Keterangan:</strong><br>{{ $attendanceData->keterangan }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ri-calendar-close-line font-48 text-muted mb-3"></i>
                                <h6 class="text-muted">Belum ada presensi hari ini</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Monthly/Semester Statistics -->
    @if(($type == 'monthly' || $type == 'semester') && $stats)
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="my-2">{{ $stats['total_hari_sekolah'] }}</h3>
                        <p class="text-muted mb-0">Hari Sekolah</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="my-2 text-success">{{ $stats['total_hadir'] }}</h3>
                        <p class="text-muted mb-0">Hadir</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="my-2 text-info">{{ $stats['total_izin'] }}</h3>
                        <p class="text-muted mb-0">Izin</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="my-2 text-secondary">{{ $stats['total_sakit'] }}</h3>
                        <p class="text-muted mb-0">Sakit</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h3 class="my-2 text-{{ $stats['persentase_kehadiran'] >= 75 ? 'success' : 'danger' }}">
                            {{ $stats['persentase_kehadiran'] }}%
                        </h3>
                        <p class="text-muted mb-0">Persentase Kehadiran</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Monthly/Semester Table -->
    @if(($type == 'monthly' || $type == 'semester') && $attendanceData && count($attendanceData) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Detail Presensi</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Status</th>
                                        <th>Waktu Masuk</th>
                                        <th>Method</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceData as $index => $attendance)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $attendance->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $attendance->tanggal->translatedFormat('l') }}</td>
                                        <td>
                                            @php
                                                $statusConfig = [
                                                    'hadir' => ['class' => 'bg-success', 'text' => 'Hadir'],
                                                    'izin' => ['class' => 'bg-info', 'text' => 'Izin'],
                                                    'sakit' => ['class' => 'bg-secondary', 'text' => 'Sakit'],
                                                ];
                                                $status = $statusConfig[$attendance->status] ?? ['class' => 'bg-secondary', 'text' => ucfirst($attendance->status)];
                                            @endphp
                                            <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                        </td>
                                        <td>{{ $attendance->waktu_masuk ? \Carbon\Carbon::parse($attendance->waktu_masuk)->format('H:i:s') : '-' }}</td>
                                        <td>
                                            <span class="badge {{ $attendance->method == 'qr_scan' ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $attendance->method == 'qr_scan' ? 'QR Scan' : 'Manual' }}
                                            </span>
                                        </td>
                                        <td>{{ $attendance->keterangan ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(($type == 'monthly' || $type == 'semester') && (!$attendanceData || count($attendanceData) == 0))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <i class="ri-calendar-close-line font-48 text-muted mb-3"></i>
                        <h6 class="text-muted">Tidak ada data presensi</h6>
                        <p class="text-muted">Pada periode yang dipilih</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function handleTypeChange() {
    const type = document.getElementById('type').value;

    // Hide all inputs
    document.getElementById('monthly-input').style.display = 'none';
    document.getElementById('semester-year').style.display = 'none';
    document.getElementById('semester-select').style.display = 'none';

    // Show relevant inputs
    if (type === 'monthly') {
        document.getElementById('monthly-input').style.display = 'block';
        const monthlyInput = document.getElementById('monthly_date');
        if (!monthlyInput.value) {
            const now = new Date();
            monthlyInput.value = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
        }
    } else if (type === 'semester') {
        document.getElementById('semester-year').style.display = 'block';
        document.getElementById('semester-select').style.display = 'block';
    }
}

// Auto submit when date changes
document.addEventListener('DOMContentLoaded', function() {
    const monthlyInput = document.getElementById('monthly_date');
    const yearSelect = document.getElementById('year');
    const semesterSelect = document.getElementById('semester');

    if (monthlyInput) {
        monthlyInput.addEventListener('change', function() {
            if (document.getElementById('type').value === 'monthly') {
                document.getElementById('filterForm').submit();
            }
        });
    }

    if (yearSelect) {
        yearSelect.addEventListener('change', function() {
            if (document.getElementById('type').value === 'semester') {
                document.getElementById('filterForm').submit();
            }
        });
    }

    if (semesterSelect) {
        semesterSelect.addEventListener('change', function() {
            if (document.getElementById('type').value === 'semester') {
                document.getElementById('filterForm').submit();
            }
        });
    }
});
</script>
@endpush