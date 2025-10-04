<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Siswa extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'jenis_kelamin' => 'string',
        'status' => 'string'
    ];

    /**
     * Relasi ke tabel users
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke tabel attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Accessor untuk mendapatkan umur siswa
     */
    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }

    /**
     * Accessor untuk jenis kelamin lengkap
     */
    public function getJenisKelaminLengkapAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Accessor untuk nama lengkap dengan kelas
     */
    public function getNamaLengkapDenganKelasAttribute(): string
    {
        $namaKelas = $this->kelas ? $this->kelas->nama_lengkap : 'Belum ada kelas';
        return $this->user->name . ' - ' . $namaKelas;
    }

    /**
     * Scope berdasarkan kelas
     */
    public function scopeByKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    /**
     * Scope berdasarkan tingkat
     */
    public function scopeByTingkat($query, $tingkat)
    {
        return $query->whereHas('kelas.tingkatKelas', function ($q) use ($tingkat) {
            $q->where('tingkat', $tingkat);
        });
    }

    /**
     * Scope berdasarkan jurusan
     */
    public function scopeByJurusan($query, $kodeJurusan)
    {
        return $query->whereHas('kelas.jurusan', function ($q) use ($kodeJurusan) {
            $q->where('kode_jurusan', $kodeJurusan);
        });
    }

    /**
     * Scope berdasarkan jenis kelamin
     */
    public function scopeByJenisKelamin($query, $jenisKelamin)
    {
        return $query->where('jenis_kelamin', $jenisKelamin);
    }

    /**
     * Get lama bersekolah dalam tahun
     */
    public function getLamaBersekolahAttribute(): int
    {
        return $this->tanggal_masuk->diffInYears(now());
    }
}
