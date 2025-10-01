<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pekerjaan extends Model
{
    use HasFactory;

    // Nama tabel sesuai database
    protected $table = 'pekerjaan';

    protected $fillable = [
        'wilayah_id',
        'user_id',
        'nama_investasi',
        'kebutuhan_dana',
        'rkap',
        'tahun_usulan',
        'coa',
        'program_investasi',
        'tipe_investasi',
        'nomor_prodef_sap',
    ];

    // Relasi ke Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Progress
    public function progress()
    {
        return $this->hasMany(ProgressFisik::class, 'pekerjaan_id')
                    ->orderBy('bulan', 'asc');
    }

    // Relasi ke PR
    public function prs()
    {
        return $this->hasMany(Pr::class, 'pekerjaan_id');
    }

    // Relasi ke MasterInvestasi
    // public function masterInvestasis()
    // {
    //     return $this->hasMany(MasterInvestasi::class, 'pekerjaan_id');
    // }

    public function masterInvestasi()
{
    return $this->hasOne(MasterInvestasi::class, 'pekerjaan_id', 'id');
}

}