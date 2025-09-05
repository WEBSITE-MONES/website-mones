<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pekerjaan;

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

    // =======================
    // Bagian PROGRES INVESTASI
    // =======================
    public function progresFisik($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik', compact('pekerjaan'));
    }

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

    // =======================
    // Bagian DATA INVESTASI
    // =======================
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

    // =======================
    // Bagian STATUS INVESTASI
    // =======================
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