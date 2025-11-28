<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

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
        'bobot',
    ];


    public function getDisplayNameAttribute()
    {
        $subSub = trim((string) ($this->sub_sub_pekerjaan ?? ''));
        $sub    = trim((string) ($this->sub_pekerjaan ?? ''));
        $jenis  = trim((string) ($this->jenis_pekerjaan_utama ?? ''));

        if ($subSub !== '') {
            return $subSub;
        }

        if ($sub !== '') {
            return $sub;
        }

        return $jenis;
    }


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