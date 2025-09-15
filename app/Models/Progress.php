<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'progress',
        'bulan',
        'nomor_ba_mulai_kerja',
        'tanggal_ba_mulai_kerja',
        'file_ba',
    ];

    public function po()
    {
        return $this->belongsTo(Po::class);
    }

    

}