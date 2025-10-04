<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'kelas_id',
        'waktu_mulai',
        'waktu_selesai',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime:H:i:s',
        'waktu_selesai' => 'datetime:H:i:s',
        'is_active' => 'boolean',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeForKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }
}
