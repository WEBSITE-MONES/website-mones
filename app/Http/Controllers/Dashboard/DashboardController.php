<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use App\Models\SettingAplikasi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
public function index(Request $request)
{
    $setting = SettingAplikasi::first();
    $tahun = $request->input('tahun', date('Y'));

    // ambil tahun yang dikirim lewat dropdown, default tahun sekarang
    $tahun = $request->get('tahun', now()->year);

    // ambil data pekerjaan sesuai tahun
    $pekerjaans = \App\Models\Pekerjaan::with('masterInvestasi')
        ->where('tahun_usulan', $tahun)
        ->orderBy('id', 'desc')
        ->get();

    $jumlahProyek = $pekerjaans->count();
    $totalNilaiInvestasi = $pekerjaans->sum('kebutuhan_dana');

    // === Hitung total PR, PO, GR, PAYMENT untuk tahun tsb ===
    $totalNilaiPR = \App\Models\Pr::whereYear('created_at', $tahun)->sum('nilai_pr');
    $persentasePR = $totalNilaiInvestasi > 0 ? ($totalNilaiPR / $totalNilaiInvestasi) * 100 : 0;

    $totalNilaiPO = \App\Models\Po::whereYear('created_at', $tahun)->sum('nilai_po');
    $persentasePO = $totalNilaiInvestasi > 0 ? ($totalNilaiPO / $totalNilaiInvestasi) * 100 : 0;

    $totalNilaiGR = \App\Models\Gr::whereYear('created_at', $tahun)->sum('nilai_gr');
    $persentaseGR = $totalNilaiInvestasi > 0 ? ($totalNilaiGR / $totalNilaiInvestasi) * 100 : 0;

    $totalNilaiPayment = \App\Models\Payment::whereYear('created_at', $tahun)->sum('nilai_payment');
    $persentasePayment = $totalNilaiInvestasi > 0 ? ($totalNilaiPayment / $totalNilaiInvestasi) * 100 : 0;

    return view('Dashboard.index', compact(
        'setting',
        'tahun',
        'jumlahProyek',
        'totalNilaiInvestasi',
        'pekerjaans',
        'totalNilaiPR',
        'persentasePR',
        'totalNilaiPO',
        'persentasePO',
        'totalNilaiGR',
        'persentaseGR',
        'totalNilaiPayment',
        'persentasePayment'
    ));
}


    public function kota($id)
    {
        $wilayah = Wilayah::with('pekerjaans')->findOrFail($id);
        $setting = SettingAplikasi::first(); 
        return view('Dashboard.kota', compact('wilayah', 'setting'));
    }    
}