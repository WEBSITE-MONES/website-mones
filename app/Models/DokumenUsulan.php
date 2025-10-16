<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DokumenUsulan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_usulan';

    protected $fillable = [
        'pekerjaan_id',
        'keterangan',
        'kategori',
        'tanggal_upload',
    ];

    // Relasi ke model Pekerjaan
    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }
}