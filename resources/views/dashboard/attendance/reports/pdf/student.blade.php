<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .student-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 120px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .attendance-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .attendance-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .status-hadir { background-color: #28a745; }
        .status-terlambat { background-color: #ffc107; color: #333; }
        .status-tidak-hadir { background-color: #dc3545; }
        .status-izin { background-color: #17a2b8; }
        .status-sakit { background-color: #6c757d; }
        .percentage {
            font-weight: bold;
        }
        .percentage.good {
            color: #28a745;
        }
        .percentage.bad {
            color: #dc3545;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI SISWA</h1>
        <h2>{{ $appSetting->name ?? config('app.name', 'AYCLASS') }}</h2>
        @if($appSetting && $appSetting->nama_sekolah)
        <p style="margin: 5px 0; font-size: 12px;">{{ $appSetting->nama_sekolah }}</p>
        @endif
        @if($appSetting && $appSetting->alamat_sekolah)
        <p style="margin: 5px 0; font-size: 11px; color: #666;">{{ $appSetting->alamat_sekolah }}</p>
        @endif
    </div>

    <div class="student-info">
        <h3 style="margin-top: 0; margin-bottom: 15px;">Informasi Siswa</h3>
        <div class="info-grid">
            <div>
                <div class="info-row">
                    <div class="info-label">Nama:</div>
                    <div class="info-value">{{ $siswa->user->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">NIS:</div>
                    <div class="info-value">{{ $siswa->nis }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kelas:</div>
                    <div class="info-value">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</div>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <div class="info-label">Periode:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Hari Sekolah:</div>
                    <div class="info-value">{{ $statistics['total_hari_sekolah'] }} hari</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dicetak pada:</div>
                    <div class="info-value">{{ now()->format('d F Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" style="color: #28a745;">{{ $statistics['total_hadir'] + $statistics['total_terlambat'] }}</div>
            <div class="stat-label">Hadir</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #17a2b8;">{{ $statistics['total_izin'] }}</div>
            <div class="stat-label">Izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6c757d;">{{ $statistics['total_sakit'] }}</div>
            <div class="stat-label">Sakit</div>
        </div>
        <div class="stat-card">
            <div class="stat-number percentage {{ $statistics['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                {{ $statistics['persentase_kehadiran'] }}%
            </div>
            <div class="stat-label">Persentase Kehadiran</div>
        </div>
    </div>

    @if($attendanceData->count() > 0)
    <h3>Detail Absensi</h3>
    <table class="attendance-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Hari</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 10%;">Waktu</th>
                <th style="width: 40%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $index => $attendance)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $attendance->tanggal->format('d/m/Y') }}</td>
                <td>{{ $attendance->tanggal->format('l') }}</td>
                <td style="text-align: center;">
                    @php
                        $statusClass = match($attendance->status) {
                            'hadir' => 'status-hadir',
                            'terlambat' => 'status-terlambat',
                            'izin' => 'status-izin',
                            'sakit' => 'status-sakit',
                            default => 'status-sakit'
                        };
                        $statusText = match($attendance->status) {
                            'hadir' => 'Hadir',
                            'terlambat' => 'Terlambat',
                            'izin' => 'Izin',
                            'sakit' => 'Sakit',
                            default => ucfirst($attendance->status)
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td style="text-align: center;">{{ $attendance->waktu_masuk ? $attendance->waktu_masuk->format('H:i') : '-' }}</td>
                <td>{{ $attendance->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 30px; color: #666;">
        <p>Tidak ada data absensi pada periode ini.</p>
    </div>
    @endif

    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h3 style="margin-top: 0;">Ringkasan Kehadiran</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Total hari sekolah: {{ $statistics['total_hari_sekolah'] }} hari</li>
            <li>Total kehadiran: {{ $statistics['total_hadir'] + $statistics['total_terlambat'] }} hari</li>
            <li>Total ketidakhadiran: {{ $statistics['total_izin'] + $statistics['total_sakit'] }} hari</li>
            <li>Persentase kehadiran:
                <span class="percentage {{ $statistics['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                    {{ $statistics['persentase_kehadiran'] }}%
                </span>
                @if($statistics['persentase_kehadiran'] >= 90)
                    (Sangat Baik)
                @elseif($statistics['persentase_kehadiran'] >= 75)
                    (Baik)
                @elseif($statistics['persentase_kehadiran'] >= 60)
                    (Cukup)
                @else
                    (Kurang)
                @endif
            </li>
        </ul>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>