<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Po extends Model
{
    protected $fillable = [
    'pr_id',
    'nomor_po',
    'nomor_kontrak',
    'nilai_po',
    'estimated',
    'waktu_pelaksanaan',
    'pelaksana',
    'tanggal_po',
    'mekanisme_pembayaran',
];



    public function pr()
    {
        return $this->belongsTo(Pr::class);
    }

    public function progresses()
{
    return $this->hasMany(Progress::class);
}

public function getRouteKeyName()
{
    return 'id';
}

public function termins()
{
    return $this->hasMany(Termin::class);
}


public function pekerjaan()
    {
        return $this->pr->pekerjaan(); 
    }

}