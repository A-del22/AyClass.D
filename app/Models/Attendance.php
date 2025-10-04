<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'waktu_masuk',
        'status',
        'keterangan',
        'surat_izin',
        'method',
        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime:H:i:s',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'terlambat');
    }

    public function scopeTidakHadir($query)
    {
        return $query->whereIn('status', ['izin', 'sakit']);
    }
}
