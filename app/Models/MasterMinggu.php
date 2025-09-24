<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterMinggu extends Model
{
    use HasFactory;

    protected $table = 'master_minggu';

    protected $fillable = [
        'progress_id', 
        'kode_minggu',
        'tanggal_awal',
        'tanggal_akhir',
    ];

    protected $casts = [
    'tanggal_awal' => 'date',
    'tanggal_akhir' => 'date',
];

    /**
     * Relasi ke ProgressDetail
     */
    public function progressDetails()
    {
        return $this->hasMany(ProgressDetail::class, 'minggu_id');
    }
    public function progress()
    {
        return $this->belongsTo(Progress::class, 'progress_id');
    }
}