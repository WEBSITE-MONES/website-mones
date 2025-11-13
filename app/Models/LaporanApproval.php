<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_id',
        'user_id',
        'role_approval',
        'nama_approver',
        'status',
        'urutan',
        'komentar',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke LaporanInvestasi
     */
    public function laporan()
    {
        return $this->belongsTo(LaporanInvestasi::class, 'laporan_id');
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get role label yang human-readable
     */
    public function getRoleLabelAttribute()
    {
        $roles = [
            'manager_teknik' => 'Manager Teknik',
            'assisten_manager' => 'Assisten Manager',
            'direktur' => 'Direktur',
            'general_manager' => 'General Manager',
            'kepala_divisi' => 'Kepala Divisi',
            'supervisor' => 'Supervisor',
            'staff' => 'Staff',
        ];

        return $roles[$this->role_approval] ?? ucwords(str_replace('_', ' ', $this->role_approval));
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'approved' => '<span class="badge bg-success"><i class="fa fa-check-circle"></i> Approved</span>',
            'rejected' => '<span class="badge bg-danger"><i class="fa fa-times-circle"></i> Rejected</span>',
            'pending' => '<span class="badge bg-warning text-dark"><i class="fa fa-clock"></i> Pending</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}