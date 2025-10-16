<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Gambar extends Model
{
    use HasFactory;

    protected $fillable = [
        'pekerjaan_id',
        'keterangan',
        'file',
        'status',
    ];

   public function pekerjaan()
{
    return $this->belongsTo(Pekerjaan::class);
}

}