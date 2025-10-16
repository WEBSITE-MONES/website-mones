<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkapPekerjaan extends Model
{
    protected $table = 'rkap_pekerjaan';

    protected $fillable = [
        'pekerjaan_id',
        'tahun',
        'nilai',
    ];

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }
}