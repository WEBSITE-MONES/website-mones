<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'signature_id',
        'ip_address',
        'user_agent',
        'verification_result',
        'verification_details',
    ];

    protected $casts = [
        'verification_details' => 'array',
    ];

    public function signature()
    {
        return $this->belongsTo(DigitalSignature::class, 'signature_id', 'signature_id');
    }
}