<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Harian</title>
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
        .info-section {
            margin-bottom: 20px;
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
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
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
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI HARIAN</h1>
        <h2>{{ $appSetting->name ?? config('app.name', 'AYCLASS') }}</h2>
        @if($appSetting && $appSetting->nama_sekolah)
        <p style="margin: 5px 0; font-size: 12px;">{{ $appSetting->nama_sekolah }}</p>
        @endif
        @if($appSetting && $appSetting->alamat_sekolah)
        <p style="margin: 5px 0; font-size: 11px; color: #666;">{{ $appSetting->alamat_sekolah }}</p>
        @endif
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $date->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kelas:</div>
            <div class="info-value">{{ $kelasName }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Siswa:</div>
            <div class="info-value">{{ $totalSiswa }} orang</div>
        </div>
        <div class="info-row">
            <div class="info-label">Dicetak pada:</div>
            <div class="info-value">{{ now()->format('d F Y H:i:s') }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" style="color: #28a745;">{{ $attendanceStats['total_hadir'] + $attendanceStats['total_terlambat'] }}</div>
            <div class="stat-label">Hadir</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #17a2b8;">{{ $attendanceStats['total_izin'] }}</div>
            <div class="stat-label">Izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6c757d;">{{ $attendanceStats['total_sakit'] }}</div>
            <div class="stat-label">Sakit</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #868e96;">{{ $attendanceStats['belum_absen'] }}</div>
            <div class="stat-label">Belum Absen</div>
        </div>
    </div>

    @if($attendanceData->count() > 0)
    <table class="attendance-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 20%;">Kelas</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 10%;">Waktu</th>
                <th style="width: 10%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $index => $attendance)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $attendance->siswa->user->name ?? '-' }}</td>
                <td>{{ $attendance->siswa->nis ?? '-' }}</td>
                <td>{{ $attendance->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</td>
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
        <p>Tidak ada data absensi pada tanggal ini.</p>
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>