<?php

namespace Database\Seeders;

use App\Models\AppSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppSettings::create([
            'name' => 'AyClass.QR',
            'description' => 'AyClass adalah aplikasi absensi berbasis QR Code yang memudahkan pengguna untuk mencatat kehadiran secara cepat dan efisien.',
            'url' => 'https://ayclass.com',
            'nama_sekolah' => 'SMK Negeri 1 Contoh',
            'alamat_sekolah' => 'Jl. Contoh Alamat No.123, Kota Contoh, Negara Contoh',
        ]);
    }
}
