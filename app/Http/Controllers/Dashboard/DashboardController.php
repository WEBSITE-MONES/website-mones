<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use App\Models\SettingAplikasi;
use App\Models\Pekerjaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $setting = SettingAplikasi::first(); // ambil data setting aplikasi
         $jumlahProyek = Pekerjaan::count(); 
         $totalNilaiInvestasi = Pekerjaan::sum('kebutuhan_dana');
          // Ambil semua pekerjaan dengan master_investasi
        $pekerjaans = \App\Models\Pekerjaan::with('masterInvestasi')
        ->orderBy('id', 'desc')
        ->paginate(10);
        
        return view('Dashboard.index', compact('setting', 'jumlahProyek', 'totalNilaiInvestasi', 'pekerjaans'));
    }
    
    public function kota($id)
    {
        $wilayah = Wilayah::with('pekerjaans')->findOrFail($id);
        $setting = SettingAplikasi::first(); // juga ambil setting aplikasi
        return view('Dashboard.kota', compact('wilayah', 'setting'));
    }    
}