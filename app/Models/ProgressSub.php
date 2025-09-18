<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressSub extends Model
{
    use HasFactory;

    protected $table = 'progress_sub';

    protected $fillable = [
        'progress_id',
        'sub_pekerjaan',
        'volume',
        'satuan',
        'bobot',
    ];


    public function details()
    {
        return $this->hasMany(ProgressDetail::class, 'sub_id');
    }


    public function progress()
    {
        return $this->belongsTo(Progress::class, 'progress_id');
    }
    
    public function getDeviasiAttribute()
    {
        $rencana = $this->details->sum('rencana');
        $realisasi = $this->details->sum('realisasi');
        return $realisasi - $rencana;
    }

}