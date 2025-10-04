<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'url',
        'nama_sekolah',
        'alamat_sekolah',
    ];
}
