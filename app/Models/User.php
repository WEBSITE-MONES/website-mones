<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom yang bisa diisi mass assignment
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'wilayah_id',
    ];

    // Kolom yang disembunyikan saat serialisasi
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting kolom tertentu
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Profile (User punya satu Profile)
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // Event: saat User baru dibuat, otomatis bikin Profile kosong
    protected static function booted()
    {
        static::created(function ($user) {
            $user->profile()->create([
                'jabatan'        => null,
                'tanggal_lahir'  => null,
                'agama'          => null,
                'jenis_kelamin'  => null,
                'nomor_telepon'  => null,
                'alamat'         => null,
                
            ]);
        });
    }

    public function wilayah()
{
    return $this->belongsTo(Wilayah::class, 'wilayah_id');
}

public function pekerjaans(): HasMany
    {
        return $this->hasMany(Pekerjaan::class, 'user_id');
    }
}