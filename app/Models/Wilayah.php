<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';
    protected $fillable = ['nama'];

    public function pekerjaans()
    {
        return $this->hasMany(Pekerjaan::class, 'wilayah_id', 'id');
    }
}