<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $successCount = 0;
    private $failedCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            \Log::info('Processing row: ', $row);

            DB::beginTransaction();

            // Find kelas by nama_kelas
            $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();
            if (!$kelas) {
                \Log::error('Kelas not found: ' . $row['kelas']);
                $this->failedCount++;
                DB::rollBack();
                return null;
            }

            \Log::info('Kelas found: ' . $kelas->nama_kelas);

            // Create user account
            $user = User::create([
                'name' => trim((string) $row['nama']),
                'email' => trim((string) $row['email']),
                'password' => Hash::make('123'), // Default password
                'role' => 'siswa',
                'email_verified_at' => now()
            ]);

            // Parse dates - handle both Excel serial number and d/m/Y format
            $tanggalLahir = null;
            $tanggalMasuk = null;

            try {
                $tanggalLahir = $this->parseDate($row['tanggal_lahir']);
            } catch (\Exception $e) {
                \Log::error('Invalid birth date format: ' . $row['tanggal_lahir'] . ' - ' . $e->getMessage());
                $this->failedCount++;
                DB::rollBack();
                return null;
            }

            try {
                $tanggalMasuk = $this->parseDate($row['tanggal_masuk']);
            } catch (\Exception $e) {
                \Log::error('Invalid entry date format: ' . $row['tanggal_masuk'] . ' - ' . $e->getMessage());
                $this->failedCount++;
                DB::rollBack();
                return null;
            }

            // Create siswa profile
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $kelas->id,
                'nis' => trim((string) $row['nis']),
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => strtoupper(trim((string) $row['jenis_kelamin'])),
                'alamat' => trim((string) $row['alamat']),
                'no_telepon' => trim((string) $row['no_telepon']),
                'tanggal_masuk' => $tanggalMasuk,
                'foto' => 'avatar.png', // Default photo
            ]);

            DB::commit();
            $this->successCount++;

            return $siswa;

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            \Log::error('Row data: ', $row);
            DB::rollBack();
            $this->failedCount++;
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nis' => 'required|max:20|unique:siswas,nis', // Remove string validation, allow numeric
            'kelas' => 'required|string',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'alamat' => 'required|string',
            'no_telepon' => 'required|max:15', // Remove string validation, allow numeric
            'tanggal_masuk' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama siswa harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'nis.required' => 'NIS harus diisi',
            'nis.unique' => 'NIS sudah terdaftar',
            'kelas.required' => 'Kelas harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'alamat.required' => 'Alamat harus diisi',
            'no_telepon.required' => 'Nomor telepon harus diisi',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'nama' => 'Nama',
            'email' => 'Email',
            'nis' => 'NIS',
            'kelas' => 'Kelas',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'alamat' => 'Alamat',
            'no_telepon' => 'No Telepon',
            'tanggal_masuk' => 'Tanggal Masuk',
        ];
    }

    public function getResults(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failedCount + count($this->failures()) + count($this->errors())
        ];
    }

    /**
     * Parse date from Excel (handles both serial number and d/m/Y format)
     */
    private function parseDate($value): string
    {
        // If numeric, treat as Excel serial date
        if (is_numeric($value)) {
            // Excel date serial starts from 1900-01-01
            // PhpSpreadsheet handles this conversion
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        // Try parsing as d/m/Y string format
        $trimmedValue = trim((string) $value);
        return \Carbon\Carbon::createFromFormat('d/m/Y', $trimmedValue)->format('Y-m-d');
    }
}