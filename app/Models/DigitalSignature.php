<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DigitalSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'signature_id',
        'user_id',
        'role_approval',
        'nama_approver',
        'jabatan',
        'signature_hash',
        'algorithm',
        'qr_code_path',
        'original_signature_path',
        'hybrid_signature_path',
        'metadata',
        'is_revoked',
        'revoked_at',
        'revoked_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_revoked' => 'boolean',
        'revoked_at' => 'datetime',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laporanApprovals()
    {
        return $this->hasMany(LaporanApproval::class, 'signature_id', 'signature_id');
    }

    public function verifications()
    {
        return $this->hasMany(SignatureVerification::class, 'signature_id', 'signature_id');
    }

    // Accessors
    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    public function getHybridSignatureUrlAttribute()
    {
        return $this->hybrid_signature_path ? Storage::url($this->hybrid_signature_path) : null;
    }

    public function getOriginalSignatureUrlAttribute()
    {
        return $this->original_signature_path ? Storage::url($this->original_signature_path) : null;
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('is_revoked', false);
    }

    public function scopeRevoked($query)
    {
        return $query->where('is_revoked', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role_approval', $role);
    }

    // Methods
    public function revoke(string $reason = null): bool
    {
        return $this->update([
            'is_revoked' => true,
            'revoked_at' => now(),
            'revoked_reason' => $reason,
        ]);
    }

    public function isValid(): bool
    {
        return !$this->is_revoked;
    }

    public function logVerification(string $result, array $details = []): void
    {
        SignatureVerification::create([
            'signature_id' => $this->signature_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'verification_result' => $result,
            'verification_details' => $details,
        ]);
    }
}