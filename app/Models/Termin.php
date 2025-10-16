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
        'payment_id',
        'persentase',
        'syarat_pembayaran',   
        'nilai_pembayaran',  
    ];

    protected $casts = [
        'nilai_pembayaran' => 'float',
        'persentase' => 'float',
    ];
    
    public function po()
    {
        return $this->belongsTo(Po::class);
    }

    public function payment()
{
    return $this->belongsTo(Payment::class);
}

public function getSudahDibayarAttribute()
    {
        return !is_null($this->payment_id);
    }

    public function getNomorTerminAttribute()
    {
        preg_match('/\d+/', $this->uraian, $matches);
        return isset($matches[0]) ? (int)$matches[0] : 0;
    }
}