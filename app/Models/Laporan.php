<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pekerjaan_id',
        'keterangan',
        'file_laporan',
        'tanggal_upload',
        'status',
    ];

    // Relasi ke tabel pekerjaan
    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }
}