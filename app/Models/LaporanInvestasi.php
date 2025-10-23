<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanInvestasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_investasi';

    protected $fillable = [
        'kode_laporan',
        'jenis_laporan',
        'tahun',
        'bulan',
        'periode_label',
        'status_approval',
        'catatan',
        'tanggal_dibuat',
        'tanggal_disubmit',
        'tanggal_approved',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
        'tanggal_disubmit' => 'datetime',
        'tanggal_approved' => 'datetime',
    ];

    // Relasi
    public function pembuatLaporan()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function approvals()
    {
        return $this->hasMany(LaporanApproval::class, 'laporan_id')->orderBy('urutan');
    }

    public function details()
    {
        return $this->hasMany(LaporanDetail::class, 'laporan_id');
    }

    // Accessor untuk badge status
    public function getStatusBadgeAttribute()
    {
        return match($this->status_approval) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    // Accessor untuk nama bulan
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '';
    }
}