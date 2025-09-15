<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterInvestasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pekerjaan_id',
        'tipe',           
        'coa_sub',
        'kategori',
        'manfaat',
        'jenis',
        'sifat',
        'urgensi',
    ];

    public function pekerjaan()
{
    return $this->belongsTo(Pekerjaan::class); // id_pekerjaan di master_investasis
}
}