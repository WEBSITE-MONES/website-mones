<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProgress extends Model
{
    use HasFactory;

    protected $table = 'daily_progress';

    protected $fillable = [
        'po_id',
        'pekerjaan_item_id',
        'tanggal',
        'jenis_pekerjaan',
        'volume_realisasi',
        'satuan',
        'deskripsi',
        'jumlah_pekerja',
        'alat_berat',
        'material',
        'cuaca_suhu',
        'cuaca_deskripsi',
        'cuaca_kelembaban',
        'jam_kerja',
        'kondisi_lapangan',
        'kendala',
        'solusi',
        'foto',
        'gps_latitude',
        'gps_longitude',
        'lokasi_nama',
        'rencana_besok',
        'pelapor_id',
        'status_approval',
        'approved_by',          // ✅ Sesuai dengan database
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'volume_realisasi' => 'decimal:2',
        'jumlah_pekerja' => 'integer',
        'jam_kerja' => 'decimal:1',
        'cuaca_suhu' => 'decimal:1',
        'cuaca_kelembaban' => 'integer',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'foto' => 'array',
        'approved_at' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function po()
    {
        return $this->belongsTo(Po::class, 'po_id');
    }

    public function pekerjaanItem()
    {
        return $this->belongsTo(PekerjaanItem::class, 'pekerjaan_item_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // ✅ PERBAIKAN: Gunakan approved_by sesuai database
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==================== HELPERS ====================

    public function getWeekNumber()
    {
        return $this->tanggal->weekOfYear;
    }

    public function getYear()
    {
        return $this->tanggal->year;
    }

    // ==================== STATUS HELPERS ====================

    public function isPending()
    {
        return $this->status_approval === 'pending';
    }

    public function isApproved()
    {
        return $this->status_approval === 'approved';
    }

    public function isRejected()
    {
        return $this->status_approval === 'rejected';
    }

    // ==================== QUERY SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status_approval', 'rejected');
    }

    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        return $query->where('tanggal', '>=', $startDate);
    }

    public function scopeByPelapor($query, $pelaporId)
    {
        return $query->where('pelapor_id', $pelaporId);
    }

    public function scopeByPo($query, $poId)
    {
        return $query->where('po_id', $poId);
    }
}