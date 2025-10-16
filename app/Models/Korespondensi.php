<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Korespondensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pekerjaan_id',
        'jenis',
        'judul',
        'tanggal',
        'file_path',
    ];

    // relasi ke pekerjaan
    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }
}