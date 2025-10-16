<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubPekerjaan extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'sub_pekerjaan';

    // Mass assignment
    protected $fillable = [
        'pekerjaan_id',
        'pr_id',
        'nama_sub',
    ];

    // Relasi ke Pekerjaan (parent)
    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    public function pr()
{
    return $this->belongsTo(\App\Models\Pr::class, 'pr_id');
}

}