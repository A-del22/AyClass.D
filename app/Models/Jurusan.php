<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Jurusan extends Model
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
     * Relasi ke tabel kelas
     */
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    /**
     * Get jumlah kelas per jurusan
     */
    public function getJumlahKelasAttribute(): int
    {
        return $this->kelas()->count();
    }

    /**
     * Get total siswa di jurusan ini
     */
    public function getTotalSiswaAttribute(): int
    {
        return $this->kelas()->withCount('siswas')->get()->sum('siswas_count');
    }
}
