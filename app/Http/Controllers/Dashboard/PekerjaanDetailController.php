<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pekerjaan;
use App\Models\ProgressFisik;

class PekerjaanDetailController extends Controller
{
    /**
     * Halaman utama detail pekerjaan (3 kolom: Progress, Data, Status)
     */
    public function index($id)
    {
        $pekerjaan = Pekerjaan::with('wilayah')->findOrFail($id);

        return view('Dashboard.Pekerjaan.detail', compact('pekerjaan'));
    }


    // Bagian PROGRES INVESTASI
    // Progress Fisik
public function progresFisik($id)
{
    $pekerjaan = Pekerjaan::with('progress')->findOrFail($id);

    // Ambil progress + buat label bulan
    $progress = $pekerjaan->progress->map(function ($item) {
        $item->bulan_label = \Carbon\Carbon::parse($item->bulan . '-01')->format('M Y');
        $item->deviasi = $item->realisasi - $item->rencana;
        return $item;
    });

    // Data untuk chart
    $labels   = $progress->pluck('bulan_label');
    $rencana  = $progress->pluck('rencana');
    $realisasi = $progress->pluck('realisasi');

    // Hitung kumulatif
    $rencanaKumulatif = [];
    $realisasiKumulatif = [];
    $totalRencana = 0;
    $totalRealisasi = 0;

    foreach ($progress as $item) {
        $totalRencana += $item->rencana;
        $totalRealisasi += $item->realisasi;

        $rencanaKumulatif[] = $totalRencana;
        $realisasiKumulatif[] = $totalRealisasi;
    }

    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik', compact(
        'pekerjaan', 'progress', 'labels', 'rencana', 'realisasi',
        'rencanaKumulatif', 'realisasiKumulatif'
    ));
}


public function storeProgress(Request $request, $id)
{
    $request->validate([
        'bulan' => 'required|date',
        'rencana' => 'required|numeric|min:0|max:100',
        'realisasi' => 'required|numeric|min:0|max:100',
    ]);

    $defiasi = $request->realisasi - $request->rencana;

    ProgressFisik::create([
        'pekerjaan_id' => $id,
        'bulan' => $request->bulan,
        'rencana' => $request->rencana,
        'realisasi' => $request->realisasi,
        'defiasi' => $defiasi,
    ]);

    return back()->with('success', 'Progress berhasil ditambahkan');
}


    // Progress RKAP
    public function penyerapanRkap($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.rkap', compact('pekerjaan'));
    }

    public function pembayaran($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.pembayaran', compact('pekerjaan'));
    }

    // Bagian DATA INVESTASI
    public function kontrak($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.kontrak', compact('pekerjaan'));
    }

    public function gambar($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.gambar', compact('pekerjaan'));
    }

    public function laporan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.laporan', compact('pekerjaan'));
    }

    public function korespondensi($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.korespondensi', compact('pekerjaan'));
    }

    // Bagian STATUS INVESTASI
    public function perencanaan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.perencanaan', compact('pekerjaan'));
    }

    public function pelelangan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.pelelangan', compact('pekerjaan'));
    }

    public function pelaksanaan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.pelaksanaan', compact('pekerjaan'));
    }

    public function selesai($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.selesai', compact('pekerjaan'));
    }
}