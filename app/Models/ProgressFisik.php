<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgressFisik extends Model
{
    use HasFactory;

    protected $table = 'progress_fisik';

    protected $fillable = [
        'pekerjaan_id',
        'bulan',
        'rencana',
        'realisasi',
        'defiasi',
    ];

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    
}