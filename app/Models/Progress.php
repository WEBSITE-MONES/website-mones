<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'po_id',
        'pekerjaan_item_id', 
        'nomor_ba_mulai_kerja',
        'tanggal_ba_mulai_kerja',
        'file_ba',
        'nomor_pcm_mulai_kerja',
        'tanggal_pcm_mulai_kerja',
        'file_pcm',
    ];

    public function po()
    {
        return $this->belongsTo(Po::class, 'po_id');
    }

    public function pekerjaanItem()
    {
        return $this->belongsTo(PekerjaanItem::class, 'pekerjaan_item_id');
    }

    public function details()
    {
        return $this->hasMany(ProgressDetail::class, 'progress_id');
    }

    // // Relasi ke PO
    // public function po()
    // {
    //     return $this->belongsTo(Po::class);
    // }

    // // Relasi ke sub-pekerjaan
    // public function subs()
    // {
    //     return $this->hasMany(ProgressSub::class, 'progress_id');
    // }

}