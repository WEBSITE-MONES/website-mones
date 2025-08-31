<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Pekerjaan;

class PekerjaanController extends Controller
{
    public function create()
    {
        $wilayahs = Wilayah::all(); // ambil semua wilayah
        return view('Dashboard.Pekerjaan.create', compact('wilayahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayah,id',
            'nama_pekerjaan' => 'required|string|max:255',
            'status' => 'required|in:Pending,On Progress,Selesai',
            'nilai' => 'required|numeric',
            'kebutuhan_dana' => 'required|numeric',
            'tahun' => 'required|digits:4',
            'tanggal' => 'required|date',
        ]);

        \App\Models\Pekerjaan::create($request->all());

        return redirect()->route('dashboard.kota', ['id' => $request->wilayah_id])
                         ->with('success', 'Rencana kerja berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $wilayahs = Wilayah::all();
        return view('Dashboard.Pekerjaan.edit', compact('pekerjaan', 'wilayahs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayah,id',
            'nama_pekerjaan' => 'required|string|max:255',
            'status' => 'required|in:Pending,On Progress,Selesai',
            'nilai' => 'required|numeric',
            'kebutuhan_dana' => 'required|numeric',
            'tahun' => 'required|digits:4',
            'tanggal' => 'required|date',
        ]);

        $pekerjaan = Pekerjaan::findOrFail($id);
        $pekerjaan->update($request->all());

        return redirect()->route('dashboard.kota', ['id' => $request->wilayah_id])
                         ->with('success', 'Rencana kerja berhasil diupdate!');
    }

    
    // fungsi delete
    public function destroy($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $wilayah_id = $pekerjaan->wilayah_id;
        $pekerjaan->delete();

        return redirect()->route('dashboard.kota', ['id' => $wilayah_id])
                         ->with('success', 'Rencana kerja berhasil dihapus!');
    }

    public function index(Request $request)
{
    $query = Pekerjaan::with('wilayah');

    // Search by nama pekerjaan / status / wilayah
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_pekerjaan', 'like', "%$search%")
              ->orWhere('status', 'like', "%$search%");
        })->orWhereHas('wilayah', function($q) use ($search) {
            $q->where('nama', 'like', "%$search%");
        });
    }

    // Filter tahun
    if ($request->filled('tahun')) {
        $query->where('tahun', $request->tahun);
    }

    $pekerjaans = $query->orderBy('tahun', 'desc')->paginate(10);

    // Ambil semua tahun unik
    $tahunList = Pekerjaan::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

    return view('Dashboard.rencana_pekerjaan', compact('pekerjaans', 'tahunList'));
}

}