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
        'jenis_pekerjaan',
        'nomor_ba_mulai_kerja',
        'tanggal_ba_mulai_kerja',
        'file_ba',
    ];

    public function po()
    {
        return $this->belongsTo(Po::class);
    }

    public function getDeviasiAttribute()
    {
        $rencana = $this->details->sum('rencana');
        $realisasi = $this->details->sum('realisasi');
        return $realisasi - $rencana;
    }

    
    public function subs()
    {
        return $this->hasMany(ProgressSub::class, 'progress_id');
    }
    
public function details()
    {
        // (target, through, foreignKeyOnThrough, foreignKeyOnFinal, localKey, localKeyOnThrough)
        return $this->hasManyThrough(
            ProgressDetail::class,
            ProgressSub::class,
            'progress_id', // foreign key on progress_sub -> progress.id
            'sub_id',      // foreign key on progress_details -> progress_sub.id
            'id',          // local key on progress
            'id'           // local key on progress_sub
        );
    }


}