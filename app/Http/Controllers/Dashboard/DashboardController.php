<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use App\Models\SettingAplikasi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $setting = SettingAplikasi::first(); // ambil data setting aplikasi
        return view('Dashboard.index', compact('setting'));
    }
    
    public function kota($id)
    {
        $wilayah = Wilayah::with('pekerjaans')->findOrFail($id);
        $setting = SettingAplikasi::first(); // juga ambil setting aplikasi
        return view('Dashboard.kota', compact('wilayah', 'setting'));
    }    
}