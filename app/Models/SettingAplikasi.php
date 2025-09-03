<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingAplikasi extends Model
{
    protected $table = 'settings_aplikasi'; 
    protected $fillable = ['nama_aplikasi', 'nama_perusahaan', 'ucapan', 'logo'];
    public $timestamps = true;
}