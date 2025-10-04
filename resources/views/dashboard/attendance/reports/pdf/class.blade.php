<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Kelas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
        .class-info {
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
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .student-table th,
        .student-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        .student-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
        }
        .student-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .student-table td:first-child,
        .student-table td:nth-child(2),
        .student-table td:nth-child(3) {
            text-align: left;
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
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI KELAS</h1>
        <h2>{{ $appSetting->name ?? config('app.name', 'AYCLASS') }}</h2>
        @if($appSetting && $appSetting->nama_sekolah)
        <p style="margin: 5px 0; font-size: 12px;">{{ $appSetting->nama_sekolah }}</p>
        @endif
        @if($appSetting && $appSetting->alamat_sekolah)
        <p style="margin: 5px 0; font-size: 11px; color: #666;">{{ $appSetting->alamat_sekolah }}</p>
        @endif
    </div>

    <div class="class-info">
        <h3 style="margin-top: 0; margin-bottom: 15px;">Informasi Kelas</h3>
        <div class="info-grid">
            <div>
                <div class="info-row">
                    <div class="info-label">Kelas:</div>
                    <div class="info-value">{{ $kelas->nama_kelas }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tingkat:</div>
                    <div class="info-value">{{ $kelas->tingkatKelas->nama_tingkat ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jurusan:</div>
                    <div class="info-value">{{ $kelas->jurusan->nama_jurusan ?? '-' }}</div>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <div class="info-label">Periode:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Siswa:</div>
                    <div class="info-value">{{ $classStats['total_siswa'] }} orang</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Hari Sekolah:</div>
                    <div class="info-value">{{ $classStats['total_hari_sekolah'] }} hari</div>
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
            <div class="stat-number" style="color: #28a745;">{{ ($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0) }}</div>
            <div class="stat-label">Total Kehadiran</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #17a2b8;">{{ $classStats['total_izin'] }}</div>
            <div class="stat-label">Total Izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6c757d;">{{ $classStats['total_sakit'] }}</div>
            <div class="stat-label">Total Sakit</div>
        </div>
        <div class="stat-card">
            <div class="stat-number percentage {{ $classStats['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                {{ $classStats['persentase_kehadiran'] }}%
            </div>
            <div class="stat-label">Persentase Kehadiran Kelas</div>
        </div>
    </div>

    @if($studentData->count() > 0)
    <h3>Detail Absensi Per Siswa</h3>
    <table class="student-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Nama Siswa</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 10%;">Hadir</th>
                <th style="width: 10%;">Izin</th>
                <th style="width: 10%;">Sakit</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 10%;">Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentData as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student['nama'] }}</td>
                <td>{{ $student['nis'] }}</td>
                <td>{{ $student['hadir'] }}</td>
                <td>{{ $student['izin'] }}</td>
                <td>{{ $student['sakit'] }}</td>
                <td>{{ $student['total_absen'] }}</td>
                <td>
                    <span class="percentage {{ $student['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                        {{ $student['persentase_kehadiran'] }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #e9ecef;">
                <td colspan="3">TOTAL</td>
                <td>{{ ($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0) }}</td>
                <td>{{ $classStats['total_izin'] }}</td>
                <td>{{ $classStats['total_sakit'] }}</td>
                <td>{{ ($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0) + $classStats['total_izin'] + $classStats['total_sakit'] }}</td>
                <td>
                    <span class="percentage {{ $classStats['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                        {{ $classStats['persentase_kehadiran'] }}%
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align: center; padding: 30px; color: #666;">
        <p>Tidak ada data siswa dalam kelas ini.</p>
    </div>
    @endif

    <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h3 style="margin-top: 0;">Analisis Kelas</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4 style="margin-bottom: 10px;">Statistik Umum:</h4>
                <ul style="margin: 5px 0; padding-left: 20px; font-size: 11px;">
                    <li>Total siswa: {{ $classStats['total_siswa'] }} orang</li>
                    <li>Hari sekolah efektif: {{ $classStats['total_hari_sekolah'] }} hari</li>
                    <li>Rata-rata kehadiran per hari: {{ $classStats['total_hari_sekolah'] > 0 ? round((($classStats['total_hadir'] ?? 0) + ($classStats['total_terlambat'] ?? 0)) / $classStats['total_hari_sekolah'], 1) : 0 }} siswa</li>
                    <li>Persentase kehadiran kelas:
                        <span class="percentage {{ $classStats['persentase_kehadiran'] >= 75 ? 'good' : 'bad' }}">
                            {{ $classStats['persentase_kehadiran'] }}%
                        </span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 10px;">Kategori Kehadiran:</h4>
                <ul style="margin: 5px 0; padding-left: 20px; font-size: 11px;">
                    @php
                        $excellentStudents = $studentData->where('persentase_kehadiran', '>=', 90)->count();
                        $goodStudents = $studentData->where('persentase_kehadiran', '>=', 75)->where('persentase_kehadiran', '<', 90)->count();
                        $fairStudents = $studentData->where('persentase_kehadiran', '>=', 60)->where('persentase_kehadiran', '<', 75)->count();
                        $poorStudents = $studentData->where('persentase_kehadiran', '<', 60)->count();
                    @endphp
                    <li>Sangat Baik (≥90%): {{ $excellentStudents }} siswa</li>
                    <li>Baik (75-89%): {{ $goodStudents }} siswa</li>
                    <li>Cukup (60-74%): {{ $fairStudents }} siswa</li>
                    <li>Kurang (<60%): {{ $poorStudents }} siswa</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>