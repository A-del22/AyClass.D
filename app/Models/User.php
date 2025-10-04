<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel siswas
     */
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    /**
     * Cek apakah user adalah siswa
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Cek apakah user adalah guru
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Scope berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk siswa saja
     */
    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    /**
     * Scope untuk guru saja
     */
    public function scopeGuru($query)
    {
        return $query->where('role', 'guru');
    }

    /**
     * Get nama dengan role
     */
    public function getNamaDenganRoleAttribute(): string
    {
        $roleText = match ($this->role) {
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'admin' => 'Admin',
            default => ucfirst($this->role)
        };

        return $this->name . ' (' . $roleText . ')';
    }
}
