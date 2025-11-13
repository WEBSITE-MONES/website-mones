<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LaporanApprovalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_approval',
        'nama_approver',
        'jabatan',
        'urutan',
        'tanda_tangan',
        'signature_id',         
        'qr_code_path',         
        'signature_hash',       
        'signature_type',       
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke DigitalSignature
     */
    public function digitalSignature()
    {
        return $this->hasOne(DigitalSignature::class, 'signature_id', 'signature_id');
    }

    /**
     * Get full URL tanda tangan
     */
    public function getTandaTanganUrlAttribute()
    {
        if ($this->tanda_tangan) {
            return Storage::url($this->tanda_tangan);
        }
        return null;
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute()
    {
        $roles = [
            'manager_teknik' => 'Manager Teknik',
            'assisten_manager' => 'Assisten Manager',
            'direktur' => 'Direktur',
            'general_manager' => 'General Manager',
        ];

        return $roles[$this->role_approval] ?? $this->role_approval;
    }

    /**
     * Scope untuk active approvers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}