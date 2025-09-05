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
        return view('Dashboard.PekerjaanDetail.Progres.fisik', compact('pekerjaan'));
    }

    public function penyerapanRkap($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Progres.rkap', compact('pekerjaan'));
    }

    public function pembayaran($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Progres.pembayaran', compact('pekerjaan'));
    }

    // =======================
    // Bagian DATA INVESTASI
    // =======================
    public function kontrak($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Data.kontrak', compact('pekerjaan'));
    }

    public function gambar($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Data.gambar', compact('pekerjaan'));
    }

    public function laporan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Data.laporan', compact('pekerjaan'));
    }

    public function korespondensi($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Data.korespondensi', compact('pekerjaan'));
    }

    // =======================
    // Bagian STATUS INVESTASI
    // =======================
    public function perencanaan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Status.perencanaan', compact('pekerjaan'));
    }

    public function pelelangan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Status.pelelangan', compact('pekerjaan'));
    }

    public function pelaksanaan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Status.pelaksanaan', compact('pekerjaan'));
    }

    public function selesai($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.PekerjaanDetail.Status.selesai', compact('pekerjaan'));
    }
}