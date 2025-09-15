<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_id',
        'tanggal_payment',
        'nomor_payment',
        'nilai_payment',
        'invoice',
        'receipt',
        'nodin_payment',
        'bill',
    ];

    public function pr()
    {
        return $this->belongsTo(Pr::class);
    }
}