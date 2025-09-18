<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgressDetail extends Model
{
    use HasFactory;

    protected $table = 'progress_details';

    protected $fillable = [
        'sub_id',
        'minggu',
        'tanggal_awal_minggu',
        'tanggal_akhir_minggu',
        'rencana',
        'realisasi',
    ];

    public function sub()
    {
        return $this->belongsTo(ProgressSub::class, 'sub_id');
    }
    public function getDeviasiAttribute()
    {
        return $this->realisasi - $this->rencana;
    }

    

}