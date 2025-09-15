<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gr extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_id',
        'tanggal_gr',
        'nomor_gr',
        'nilai_gr',
    ];

    public function pr()
    {
        return $this->belongsTo(Pr::class);
    }
}