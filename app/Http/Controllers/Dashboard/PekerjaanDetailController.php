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

    $progress = $pekerjaan->progress->sortBy('bulan')->values()->map(function ($item) {
        $item->bulan_label = \Carbon\Carbon::parse($item->bulan . '-01')->format('M Y');
        $item->defiasi = $item->realisasi - $item->rencana;
        return $item;
    });

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

public function createProgress($pekerjaanId)
{
    $pekerjaan = Pekerjaan::findOrFail($pekerjaanId);
    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.create_progres', compact('pekerjaan'));
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

    // Redirect ke halaman progress fisik setelah berhasil disimpan
    return redirect()->route('pekerjaan.progres.fisik', $id)
                     ->with('success', 'Progress berhasil ditambahkan');
}

public function editProgress($pekerjaanId, $progressId)
{
    $pekerjaan = Pekerjaan::findOrFail($pekerjaanId);
    $progress  = ProgressFisik::findOrFail($progressId);

    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.edit_progress', compact('pekerjaan', 'progress'));
}
// Update progress
public function updateProgress(Request $request, $pekerjaanId, $progressId)
{
    $request->validate([
        'bulan' => 'required|date',
        'rencana' => 'required|numeric|min:0|max:100',
        'realisasi' => 'required|numeric|min:0|max:100',
    ]);

    $progress = ProgressFisik::findOrFail($progressId);
    $progress->update([
        'bulan' => $request->bulan,
        'rencana' => $request->rencana,
        'realisasi' => $request->realisasi,
        'defiasi' => $request->realisasi - $request->rencana,
    ]);

    return redirect()->route('pekerjaan.progres.fisik', $pekerjaanId)
                     ->with('success', 'Progress berhasil diperbarui');
}

public function destroyProgress($pekerjaanId, $progressId)
{
    $progress = ProgressFisik::findOrFail($progressId);
    $progress->delete();

    return redirect()->route('pekerjaan.progres.fisik', $pekerjaanId)
                     ->with('success', 'Progress berhasil dihapus');
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