<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Termin extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'uraian',
        'persentase',
        'syarat_pembayaran',   
        'nilai_pembayaran',  
    ];

    public function po()
    {
        return $this->belongsTo(Po::class);
    }
}