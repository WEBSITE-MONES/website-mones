<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgressDetail extends Model
{
    use HasFactory;

    protected $table = 'progress_details';

    protected $fillable = [
        'progress_id',
        'minggu_id',
        'bobot_rencana',
        'bobot_realisasi',
        'volume_realisasi',
        'keterangan',
    ];

    public function progress()
    {
        return $this->belongsTo(Progress::class, 'progress_id');
    }

    public function minggu()
    {
        return $this->belongsTo(MasterMinggu::class, 'minggu_id');
    }

}