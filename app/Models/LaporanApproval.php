<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanApproval extends Model
{
    use HasFactory;

    protected $table = 'laporan_approvals';

    protected $fillable = [
        'laporan_id',
        'user_id',
        'role_approval',
        'nama_approver',
        'status',
        'komentar',
        'tanggal_approval',
        'urutan',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanInvestasi::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}