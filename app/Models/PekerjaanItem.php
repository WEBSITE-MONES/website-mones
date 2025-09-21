<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PekerjaanItem extends Model
{
    use HasFactory;

    protected $table = 'pekerjaan_items';

    protected $fillable = [
        'po_id',
        'kode_pekerjaan',
        'parent_id',
        'jenis_pekerjaan_utama',
        'sub_pekerjaan',
        'sub_sub_pekerjaan',
        'volume',
        'sat',
        'harga_satuan',
        'jumlah_harga',
        'bobot',
    ];

    
    public function po()
    {
        return $this->belongsTo(Po::class, 'po_id');
    }

    public function parent()
    {
        return $this->belongsTo(PekerjaanItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PekerjaanItem::class, 'parent_id');
    }

    public function progresses()
    {
        return $this->hasMany(Progress::class, 'pekerjaan_item_id');
    }
}