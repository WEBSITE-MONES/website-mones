<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanDetail extends Model
{
    use HasFactory;

    protected $table = 'laporan_details';

    protected $fillable = [
        'laporan_id',
        'coa',
        'nomor_prodef_sap',
        'nama_investasi',
        'uraian_pekerjaan',
        'total_volume',
        'nilai_rkap',
        'target_sd_bulan',
        'nomor_po',
        'tanggal_po',
        'pelaksana',
        'waktu_pelaksanaan',   
        'estimated',           
        'mulai_kontrak',
        'selesai_kontrak',
        'realisasi_fisik',
        'realisasi_pembayaran',
    ];

    protected $casts = [
        'total_volume' => 'decimal:2',
        'nilai_rkap' => 'decimal:2',
        'target_sd_bulan' => 'decimal:2',
        'realisasi_fisik' => 'decimal:2',
        'realisasi_pembayaran' => 'decimal:2',
        'tanggal_po' => 'datetime', 
    ];

    /**
     * Relationships
     */
    public function laporan()
    {
        return $this->belongsTo(LaporanInvestasi::class, 'laporan_id');
    }
}