<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gr extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_id',
        'po_id',
        'termin_id',
        'tanggal_gr',
        'nomor_gr',
        'nilai_gr',
        'file_ba_pemeriksaan',
        'file_ba_serah_terima',
        'file_ba_pembayaran',
        'file_laporan_dokumentasi',
    ];

    public function pr()
    {
        return $this->belongsTo(Pr::class);
    }

    public function po() { return $this->belongsTo(Po::class); }
    public function termin() { return $this->belongsTo(Termin::class);}

    public function payments()
{
    return $this->hasMany(Payment::class);
}

        
}