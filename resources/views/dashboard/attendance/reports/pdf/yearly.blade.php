<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Tahunan</title>
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
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .summary-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .percentage {
            font-weight: bold;
        }
        .percentage.good {
            color: #28a745;
        }
        .percentage.bad {
            color: #dc3545;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI TAHUNAN</h1>
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
            <div class="info-label">Periode:</div>
            <div class="info-value">Tahun {{ $year }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kelas:</div>
            <div class="info-value">{{ $kelasName }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Siswa:</div>
            <div class="info-value">{{ $yearlyStats['total_siswa'] }} orang</div>
        </div>
        <div class="info-row">
            <div class="info-label">Hari Sekolah:</div>
            <div class="info-value">{{ $yearlyStats['total_hari_sekolah'] }} hari</div>
        </div>
        <div class="info-row">
            <div class="info-label">Dicetak pada:</div>
            <div class="info-value">{{ now()->format('d F Y H:i:s') }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" style="color: #28a745;">{{ $yearlyStats['total_hadir'] + $yearlyStats['total_terlambat'] }}</div>
            <div class="stat-label">Total Kehadiran</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #17a2b8;">{{ $yearlyStats['total_izin'] }}</div>
            <div class="stat-label">Total Izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6c757d;">{{ $yearlyStats['total_sakit'] }}</div>
            <div class="stat-label">Total Sakit</div>
        </div>
        <div class="stat-card">
            <div class="stat-number percentage {{ $yearlyStats['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                {{ $yearlyStats['persentase_kehadiran'] }}%
            </div>
            <div class="stat-label">Persentase Kehadiran</div>
        </div>
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Jenis Absensi</th>
                <th>Jumlah</th>
                <th>Persentase dari Total Absen</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hadir</td>
                <td>{{ $yearlyStats['total_hadir'] + $yearlyStats['total_terlambat'] }}</td>
                <td>
                    @php
                        $hadirPercentage = $yearlyStats['total_absen'] > 0 ? round((($yearlyStats['total_hadir'] + $yearlyStats['total_terlambat']) / $yearlyStats['total_absen']) * 100, 2) : 0;
                    @endphp
                    <span class="percentage {{ $hadirPercentage >= 75 ? 'good' : 'bad' }}">{{ $hadirPercentage }}%</span>
                </td>
            </tr>
            <tr>
                <td>Izin</td>
                <td>{{ $yearlyStats['total_izin'] }}</td>
                <td>
                    @php
                        $izinPercentage = $yearlyStats['total_absen'] > 0 ? round(($yearlyStats['total_izin'] / $yearlyStats['total_absen']) * 100, 2) : 0;
                    @endphp
                    {{ $izinPercentage }}%
                </td>
            </tr>
            <tr>
                <td>Sakit</td>
                <td>{{ $yearlyStats['total_sakit'] }}</td>
                <td>
                    @php
                        $sakitPercentage = $yearlyStats['total_absen'] > 0 ? round(($yearlyStats['total_sakit'] / $yearlyStats['total_absen']) * 100, 2) : 0;
                    @endphp
                    {{ $sakitPercentage }}%
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef;">
                <td>Total</td>
                <td>{{ $yearlyStats['total_absen'] }}</td>
                <td>100%</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h3 style="margin-top: 0;">Ringkasan Tahun {{ $year }}</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Periode laporan: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}</li>
            <li>Total hari sekolah efektif: {{ $yearlyStats['total_hari_sekolah'] }} hari</li>
            <li>Rata-rata kehadiran per hari: {{ $yearlyStats['total_hari_sekolah'] > 0 ? round(($yearlyStats['total_hadir'] + $yearlyStats['total_terlambat']) / $yearlyStats['total_hari_sekolah'], 1) : 0 }} siswa</li>
            <li>Persentase kehadiran keseluruhan:
                <span class="percentage {{ $yearlyStats['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                    {{ $yearlyStats['persentase_kehadiran'] }}%
                </span>
            </li>
        </ul>
    </div>

    @if($attendanceData->count() > 0)
    <div style="page-break-before: always; margin-top: 30px;">
        <h3 style="margin-bottom: 15px;">Detail Data Absensi Siswa</h3>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 12%;">NIS</th>
                    <th style="width: 15%;">Kelas</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 10%;">Waktu</th>
                    <th style="width: 14%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $index => $attendance)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $attendance->siswa->user->name ?? '-' }}</td>
                    <td>{{ $attendance->siswa->nis ?? '-' }}</td>
                    <td>{{ $attendance->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</td>
                    <td style="text-align: center;">{{ $attendance->tanggal ? $attendance->tanggal->format('d M Y') : '-' }}</td>
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
    </div>
    @else
    <div style="text-align: center; padding: 30px; color: #666; margin-top: 30px;">
        <p>Tidak ada data absensi pada periode ini.</p>
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
