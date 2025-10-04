<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Kelas extends Model
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

    /**
     * Relasi ke tabel tingkat_kelas
     */
    public function tingkatKelas(): BelongsTo
    {
        return $this->belongsTo(TingkatKelas::class);
    }

    /**
     * Relasi ke tabel jurusans
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relasi ke tabel siswas
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    /**
     * Accessor untuk nama lengkap kelas (fallback jika nama_kelas kosong)
     */
    public function getNamaLengkapAttribute(): string
    {
        // Prioritaskan nama_kelas jika ada, jika tidak gunakan kombinasi tingkat + kode jurusan
        if (!empty($this->nama_kelas)) {
            return $this->nama_kelas;
        }

        return $this->tingkatKelas->tingkat . ' ' . $this->jurusan->kode_jurusan;
    }

    /**
     * Scope berdasarkan tingkat
     */
    public function scopeByTingkat($query, $tingkat)
    {
        return $query->whereHas('tingkatKelas', function ($q) use ($tingkat) {
            $q->where('tingkat', $tingkat);
        });
    }

    /**
     * Scope berdasarkan jurusan
     */
    public function scopeByJurusan($query, $kodeJurusan)
    {
        return $query->whereHas('jurusan', function ($q) use ($kodeJurusan) {
            $q->where('kode_jurusan', $kodeJurusan);
        });
    }

    /**
     * Get jumlah siswa di kelas
     */
    public function getJumlahSiswaAttribute(): int
    {
        return $this->siswas()->count();
    }

    /**
     * Get jumlah siswa laki-laki
     */
    public function getJumlahSiswaLakiLakiAttribute(): int
    {
        return $this->siswas()->where('jenis_kelamin', 'L')->count();
    }

    /**
     * Get jumlah siswa perempuan
     */
    public function getJumlahSiswaPerempuanAttribute(): int
    {
        return $this->siswas()->where('jenis_kelamin', 'P')->count();
    }

    /**
     * Cek apakah kelas sudah penuh (default kapasitas 36)
     */
    public function isFull($kapasitasMaksimal = 36): bool
    {
        return $this->siswas()->count() >= $kapasitasMaksimal;
    }

    /**
     * Get sisa kapasitas kelas (default kapasitas 36)
     */
    public function getSisaKapasitas($kapasitasMaksimal = 36): int
    {
        return $kapasitasMaksimal - $this->jumlah_siswa;
    }

    /**
     * Get persentase kelas terisi (default kapasitas 36)
     */
    public function getPersentaseTerisi($kapasitasMaksimal = 36): float
    {
        if ($kapasitasMaksimal == 0) return 0;
        return round(($this->jumlah_siswa / $kapasitasMaksimal) * 100, 1);
    }

    /**
     * Generate nama kelas otomatis berdasarkan tingkat dan jurusan
     */
    public static function generateNamaKelas($tingkatKelasId, $jurusanId): string
    {
        $tingkatKelas = TingkatKelas::find($tingkatKelasId);
        $jurusan = Jurusan::find($jurusanId);

        return $tingkatKelas->tingkat . ' ' . $jurusan->kode_jurusan;
    }
}
