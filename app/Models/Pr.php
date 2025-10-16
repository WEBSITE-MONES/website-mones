<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pr extends Model
{
    use HasFactory;

    protected $table = 'prs';

    protected $fillable = [
        'jenis_pekerjaan',
        'pekerjaan_id',
        'nilai_pr',
        'nomor_pr',
        'tanggal_pr',
        'status_pekerjaan',
    ];

    protected $casts = [
        'tanggal_pr' => 'date',
    ];

    // Relasi ke Pekerjaan
    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id');
    }

    public function po()
{
    return $this->hasOne(Po::class);
}

// public function pos()
// {
//     return $this->hasMany(Po::class, 'pr_id');
// }


public function gr()
{
    return $this->hasOne(GR::class, 'pr_id');
}

    public function payment()
    {
        return $this->hasOne(Payment::class, 'pr_id'); // pastikan kolom foreign key di tabel payments adalah pr_id
    }

        public function payments()
    {
        return $this->hasMany(Payment::class, 'pr_id');
    }

public function statusLabel()
{
    return match($this->status_pekerjaan) {
        'PR' => ['label' => 'Perencanaan (PR)', 'class' => 'text-warning'],
        'PO' => ['label' => 'Kontrak (PO)', 'class' => 'text-primary'],
        'GR' => ['label' => 'Progress (GR)', 'class' => 'text-purple'],
        'Payment' => ['label' => 'Payment', 'class' => 'text-success'],
        default => ['label' => 'Belum ada status', 'class' => 'text-muted'],
    };
}

public function subPekerjaan()
{
    return $this->hasMany(\App\Models\SubPekerjaan::class, 'pr_id');
}



}