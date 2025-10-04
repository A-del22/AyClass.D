<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TingkatKelas;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    private array $usedNames = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedTingkatKelas();
        $this->seedJurusan();
        $this->seedKelas();
        $this->seedSiswa();
    }

    private function seedTingkatKelas(): void
    {
        $tingkatKelas = [
            ['tingkat' => 'X'],
            ['tingkat' => 'XI'],
            ['tingkat' => 'XII'],
        ];

        foreach ($tingkatKelas as $tingkat) {
            TingkatKelas::create($tingkat);
        }

        $this->command->info('✓ TingkatKelas seeded (3 records)');
    }

    private function seedJurusan(): void
    {
        $jurusans = [
            [
                'kode_jurusan' => 'IPA',
                'nama_jurusan' => 'Ilmu Pengetahuan Alam'
            ],
            [
                'kode_jurusan' => 'IPS',
                'nama_jurusan' => 'Ilmu Pengetahuan Sosial'
            ],
            [
                'kode_jurusan' => 'BAHASA',
                'nama_jurusan' => 'Bahasa dan Budaya'
            ]
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::create($jurusan);
        }

        $this->command->info('✓ Jurusan seeded (3 records)');
    }

    private function seedKelas(): void
    {
        $tingkatKelas = TingkatKelas::all();
        $jurusans = Jurusan::all();

        $waliKelas = [
            'Dra. Siti Aminah, M.Pd',
            'Ahmad Subandi, S.Pd',
            'Dr. Endang Suryani, M.Si',
            'Budi Santoso, S.Pd, M.Pd',
            'Tri Wahyuni, S.S, M.Pd',
            'Drs. Bambang Priyo, M.Pd',
            'Nina Sari, S.Pd',
            'Agus Riyanto, S.Si, M.Pd',
            'Dewi Sartika, S.Pd',
            'Hendra Gunawan, M.Pd'
        ];

        $waliIndex = 0;

        foreach ($tingkatKelas as $tingkat) {
            foreach ($jurusans as $jurusan) {
                // Skip XII BAHASA untuk variasi
                if ($tingkat->tingkat === 'XII' && $jurusan->kode_jurusan === 'BAHASA') {
                    continue;
                }

                $namaKelas = $tingkat->tingkat . ' ' . $jurusan->kode_jurusan;

                Kelas::create([
                    'tingkat_kelas_id' => $tingkat->id,
                    'jurusan_id' => $jurusan->id,
                    'nama_kelas' => $namaKelas,
                    'wali_kelas' => $waliKelas[$waliIndex % count($waliKelas)],
                ]);

                $waliIndex++;
            }
        }

        $jumlahKelas = Kelas::count();
        $this->command->info("✓ Kelas seeded ({$jumlahKelas} records)");
    }

    private function seedSiswa(): void
    {
        $kelasList = Kelas::with(['tingkatKelas', 'jurusan'])->get();
        $totalSiswa = 0;

        foreach ($kelasList as $kelas) {
            // Generate 25-35 siswa per kelas
            $jumlahSiswa = rand(25, 35);

            for ($i = 1; $i <= $jumlahSiswa; $i++) {
                $totalSiswa++;

                // Generate data
                $nis = $this->generateNIS($kelas->tingkatKelas->tingkat, $totalSiswa);
                $namaSiswa = $this->generateUniqueNamaSiswa();
                $jenisKelamin = rand(0, 1) ? 'L' : 'P';
                $alamat = $this->generateAlamat();
                $noTelepon = $this->generateNoTelepon();

                // Buat user siswa
                $user = User::create([
                    'name' => $namaSiswa,
                    'email' => $this->generateEmail($nis),
                    'password' => Hash::make('123'),
                    'role' => 'siswa',
                    'email_verified_at' => now()
                ]);

                // Buat detail siswa
                Siswa::create([
                    'user_id' => $user->id,
                    'kelas_id' => $kelas->id,
                    'nis' => $nis,
                    'tanggal_lahir' => $this->generateTanggalLahir($kelas->tingkatKelas->tingkat),
                    'jenis_kelamin' => $jenisKelamin,
                    'alamat' => $alamat,
                    'no_telepon' => $noTelepon,
                    'foto' => 'avatar.png', // Default foto
                    'tanggal_masuk' => $this->generateTanggalMasuk($kelas->tingkatKelas->tingkat),
                ]);
            }
        }

        $this->command->info("✓ Total Siswa seeded ({$totalSiswa} records)");
    }

    // Helper Methods

    private function generateNIS($tingkat, $urutan): string
    {
        $tahun = match ($tingkat) {
            'X' => '24',   // Masuk tahun 2024
            'XI' => '23',  // Masuk tahun 2023
            'XII' => '22', // Masuk tahun 2022
        };

        return $tahun . str_pad($urutan, 6, '0', STR_PAD_LEFT);
    }

    private function generateEmail($nis): string
    {
        return 'siswa' . $nis . '@student.sch.id';
    }

    private function generateUniqueNamaSiswa(): string
    {
        $namaDepan = [
            // Nama laki-laki
            'Ahmad', 'Andi', 'Arif', 'Bayu', 'Budi', 'Dani', 'Eko', 'Fajar',
            'Galih', 'Hendra', 'Indra', 'Joko', 'Kurnia', 'Lutfi', 'Made', 'Nando',
            'Omar', 'Putra', 'Rizki', 'Sandi', 'Taufik', 'Umar', 'Wahyu', 'Yoga',
            'Zakaria', 'Aditya', 'Bagus', 'Cahya', 'Dimas', 'Reza', 'Faisal', 'Irfan',
            'Kevin', 'Lucky', 'Maulana', 'Nathan', 'Oscar', 'Pandu', 'Qomarudin', 'Rafi',
            'Satria', 'Teguh', 'Udin', 'Vito', 'Wawan', 'Xavier', 'Yusuf', 'Zidan',
            'Alfian', 'Bryan', 'Candra', 'Doni', 'Erik', 'Firman', 'Ghani', 'Habibi',
            // Nama perempuan
            'Ayu', 'Bella', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hani',
            'Indah', 'Kartika', 'Lestari', 'Maya', 'Novi', 'Okta', 'Putri', 'Rina',
            'Sari', 'Tika', 'Umi', 'Vina', 'Wulan', 'Yani', 'Zahra', 'Anggun',
            'Bunga', 'Diah', 'Elsa', 'Firda', 'Gina', 'Hilda', 'Intan', 'Jasmine',
            'Kania', 'Linda', 'Mira', 'Nabila', 'Olivia', 'Putri', 'Qori', 'Rara',
            'Sinta', 'Tasya', 'Ulfa', 'Vira', 'Widya', 'Yuni', 'Zara', 'Amanda',
            'Bernadette', 'Clarissa', 'Diana', 'Elsa', 'Fadilla', 'Ghea', 'Helena'
        ];

        $namaTengah = [
            '', '', '', '', '', // Lebih banyak tanpa nama tengah
            'Dwi', 'Tri', 'Ade', 'Nur', 'Sri', 'Agung', 'Bagas', 'Candra',
            'Dinda', 'Elia', 'Farah', 'Gilang', 'Hana', 'Iqbal', 'Jaya', 'Kirana',
            'Lintang', 'Mulia', 'Nusa', 'Putra', 'Rizky', 'Surya', 'Tama', 'Wira'
        ];

        $namaBelakang = [
            'Pratama', 'Saputra', 'Wijaya', 'Santoso', 'Kusuma', 'Mahendra', 'Nugraha', 'Permata',
            'Sari', 'Putri', 'Wati', 'Maharani', 'Rahayu', 'Lestari', 'Handayani', 'Pertiwi',
            'Utama', 'Wardani', 'Setiawan', 'Purnama', 'Cahaya', 'Sejati', 'Utomo', 'Wardoyo',
            'Susanto', 'Hartanto', 'Nurjana', 'Setiadi', 'Pranoto', 'Widodo', 'Sasmita', 'Wibowo',
            'Kurniawan', 'Hidayat', 'Rahman', 'Hakim', 'Ramadhan', 'Firmansyah', 'Syahputra', 'Maulana',
            'Adiputra', 'Budiman', 'Cahyadi', 'Darmawan', 'Erlangga', 'Firdaus', 'Gunawan', 'Hermawan',
            'Irawan', 'Junaedi', 'Kartika', 'Lukman', 'Mulyadi', 'Nainggolan', 'Octavia', 'Prasetyo',
            'Qomarudin', 'Ramadani', 'Salim', 'Tanjung', 'Umami', 'Veronica', 'Wulandari', 'Yuliana',
            'Zulkarnain', 'Ananda', 'Bintang', 'Cakra', 'Dewa', 'Efendi', 'Fahmi', 'Ghozali',
            'Harahap', 'Iskandar', 'Jatmiko', 'Karim', 'Lazuardi', 'Mansur', 'Nugroho', 'Oktavian'
        ];

        $maxAttempts = 100;
        $attempts = 0;

        do {
            $depan = $namaDepan[array_rand($namaDepan)];
            $tengah = $namaTengah[array_rand($namaTengah)];
            $belakang = $namaBelakang[array_rand($namaBelakang)];

            if ($tengah) {
                $namaLengkap = $depan . ' ' . $tengah . ' ' . $belakang;
            } else {
                $namaLengkap = $depan . ' ' . $belakang;
            }

            $attempts++;

            // Jika sudah mencoba terlalu banyak, tambahkan nomor urut
            if ($attempts >= $maxAttempts) {
                $counter = 1;
                $originalName = $namaLengkap;
                while (in_array($namaLengkap, $this->usedNames)) {
                    $namaLengkap = $originalName . ' ' . $counter;
                    $counter++;
                }
                break;
            }
        } while (in_array($namaLengkap, $this->usedNames));

        $this->usedNames[] = $namaLengkap;

        return $namaLengkap;
    }

    private function generateAlamat(): string
    {
        $jalan = [
            'Jl. Merdeka',
            'Jl. Sudirman',
            'Jl. Diponegoro',
            'Jl. Ahmad Yani',
            'Jl. Gajah Mada',
            'Jl. Malioboro',
            'Jl. Thamrin',
            'Jl. Kebon Jeruk',
            'Jl. Cendrawasih',
            'Jl. Mawar',
            'Jl. Melati',
            'Jl. Anggrek',
            'Jl. Dahlia',
            'Jl. Kenanga',
            'Jl. Flamboyan',
            'Jl. Bougenville',
            'Jl. Kamboja',
            'Jl. Teratai',
            'Jl. Cemara',
            'Jl. Bambu'
        ];

        $kelurahan = [
            'Pakualaman',
            'Tegalrejo',
            'Jetis',
            'Danurejan',
            'Gondokusuman',
            'Mergangsan',
            'Umbulharjo',
            'Kotagede',
            'Banguntapan',
            'Sewon',
            'Kasihan',
            'Depok',
            'Mlati',
            'Sleman',
            'Ngaglik',
            'Kalasan',
            'Berbah',
            'Prambanan',
            'Cangkringan',
            'Turi'
        ];

        $kecamatan = [
            'Kraton',
            'Gondomanan',
            'Ngampilan',
            'Wirobrajan',
            'Gedongtengen',
            'Jetis',
            'Tegalrejo',
            'Jebres',
            'Laweyan',
            'Serengan',
            'Pasar Kliwon',
            'Banjarsari',
            'Bantul',
            'Sewon',
            'Kasihan',
            'Mlati',
            'Depok',
            'Berbah',
            'Prambanan',
            'Kalasan'
        ];

        $nomor = rand(1, 150);
        $rt = str_pad(rand(1, 15), 2, '0', STR_PAD_LEFT);
        $rw = str_pad(rand(1, 8), 2, '0', STR_PAD_LEFT);

        return $jalan[array_rand($jalan)] . ' No.' . $nomor .
            ', RT ' . $rt . '/RW ' . $rw .
            ', ' . $kelurahan[array_rand($kelurahan)] .
            ', ' . $kecamatan[array_rand($kecamatan)] .
            ', Yogyakarta';
    }

    private function generateNoTelepon(): string
    {
        $providers = ['0812', '0813', '0821', '0822', '0851', '0852', '0856', '0857', '0858'];
        $provider = $providers[array_rand($providers)];
        $number = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

        return $provider . $number;
    }

    private function generateTanggalLahir($tingkat): string
    {
        $baseYear = match ($tingkat) {
            'X' => 2008,   // Kelas 10, lahir sekitar 2008 (umur 16-17)
            'XI' => 2007,  // Kelas 11, lahir sekitar 2007 (umur 17-18)
            'XII' => 2006, // Kelas 12, lahir sekitar 2006 (umur 18-19)
        };

        // Variasi ±1 tahun untuk realisme
        $year = $baseYear + rand(-1, 1);
        $month = rand(1, 12);
        $day = rand(1, 28);

        return Carbon::create($year, $month, $day)->format('Y-m-d');
    }

    private function generateTanggalMasuk($tingkat): string
    {
        $tahunMasuk = match ($tingkat) {
            'X' => 2024,   // Kelas 10, masuk 2024
            'XI' => 2023,  // Kelas 11, masuk 2023
            'XII' => 2022, // Kelas 12, masuk 2022
        };

        // Tanggal masuk di awal tahun ajaran (Juli)
        return Carbon::create($tahunMasuk, 7, rand(10, 20))->format('Y-m-d');
    }
}
