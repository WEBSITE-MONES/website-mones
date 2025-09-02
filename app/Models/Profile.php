<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'user_id',
        'jabatan',
        'tanggal_lahir',
        'agama',
        'jenis_kelamin',
        'nomor_telepon',
        'alamat',
    ];

    // Relasi ke User (Profile dimiliki oleh User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}