<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Kontrak extends Model
{
    use HasFactory;

    protected $table = 'kontraks';

    protected $fillable = [
        'keterangan',
        'pekerjaan_id',
        'tanggal_kontrak',
        'file_path',
    ];

    public function pekerjaan()
{
    return $this->belongsTo(Pekerjaan::class);
}

}