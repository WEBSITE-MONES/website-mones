<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pekerjaan extends Model
{
    use HasFactory;

    protected $table = 'pekerjaan';

    protected $fillable = [
        'wilayah_id', 'nama_pekerjaan', 'status', 'nilai', 'kebutuhan_dana', 'tahun', 'tanggal','user_id'
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function progress()
{
    return $this->hasMany(ProgressFisik::class, 'pekerjaan_id')
                ->orderBy('bulan', 'asc'); 
}

}