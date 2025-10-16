<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Pekerjaan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;





class PekerjaanController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Pekerjaan::with(['wilayah', 'masterInvestasi']); 

        if ($user->role !== 'superadmin' && $user->wilayah_id) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('nama_investasi', 'like', "%$search%")
                  ->orWhere('program_investasi', 'like', "%$search%")
                  ->orWhere('tipe_investasi', 'like', "%$search%")
                  ->orWhere('coa', 'like', "%$search%")
                  ->orWhere('nomor_prodef_sap', 'like', "%$search%");
            })->orWhereHas('wilayah', function($q) use ($search){
                $q->where('nama', 'like', "%$search%");
            });
        }

        $pekerjaans = $query->orderBy('created_at', 'desc')->get();

        return view('Dashboard.rencana_pekerjaan', compact('pekerjaans'));
    }

    public function create(): View
    {
        $wilayahs = Wilayah::all();
        return view('Dashboard.Pekerjaan.create', compact('wilayahs'));
    }

    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'wilayah_id'        => 'required|exists:wilayah,id',
        'coa'               => 'required|string|max:255',
        'tipe'              => 'required|string|max:255',
        'tipe_investasi'    => 'required|string|max:255',
        'nomor_prodef_sap'  => 'nullable|string|max:255',
        'nama_investasi'    => 'required|string|max:255',
        'kebutuhan_dana'    => 'required|numeric',
        'rkap'              => 'required|array',     
        'rkap.*'            => 'nullable|numeric',    
        'tahun_usulan'      => 'required|digits:4|integer',
        'coa_sub'           => 'required|string|max:255',
        'kategori'          => 'required|string|max:255',
        'manfaat'           => 'required|string|max:255',
        'jenis'             => 'required|string|max:255',
        'sifat'             => 'required|string|max:255',
        'urgensi'           => 'required|string|max:255',
    ]);

    // hitung total dari semua RKAP per tahun
    $totalRkap = array_sum($request->rkap);

    $gambarPath = null;

    if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('gambar_pekerjaan', 'public');
    }

    // simpan pekerjaan utama
    $pekerjaan = Pekerjaan::create([
        'wilayah_id'        => $request->wilayah_id,
        'coa'               => $request->coa,
        'tipe_investasi'    => $request->tipe_investasi,
        'nomor_prodef_sap'  => $request->nomor_prodef_sap,
        'nama_investasi'    => $request->nama_investasi,
        'kebutuhan_dana'    => $request->kebutuhan_dana,
        'total_dana'        => $totalRkap, // tambahkan field ini di tabel pekerjaan
        'tahun_usulan'      => $request->tahun_usulan,
        'gambar'            => $gambarPath,
        'user_id'           => auth()->id(),
    ]);

    // simpan detail master investasi
    $pekerjaan->masterInvestasi()->create([
        'tipe'      => $request->tipe,
        'coa_sub'   => $request->coa_sub,
        'kategori'  => $request->kategori,
        'manfaat'   => $request->manfaat,
        'jenis'     => $request->jenis,
        'sifat'     => $request->sifat,
        'urgensi'   => $request->urgensi,
    ]);

    // simpan RKAP per tahun ke tabel rkap_pekerjaan
    foreach ($request->rkap as $tahun => $nilai) {
        $pekerjaan->rkapDetails()->create([
            'tahun' => $tahun,
            'nilai' => $nilai ?? 0,
        ]);
    }

    return redirect()->route('pekerjaan.index')
                     ->with('success', 'Rencana kerja, master investasi, dan RKAP per tahun berhasil ditambahkan!');
}


    public function edit(int $id): View
    {
        $pekerjaan = Pekerjaan::with(['masterInvestasi', 'rkapDetails'])->findOrFail($id);
        $wilayahs = Wilayah::all();
        
        // Siapkan data RKAP existing dari rkapDetails
        $existingRkap = [];
        foreach ($pekerjaan->rkapDetails as $detail) {
            $existingRkap[$detail->tahun] = $detail->nilai;
        }
        
        return view('Dashboard.Pekerjaan.edit', compact('pekerjaan', 'wilayahs', 'existingRkap'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'wilayah_id'        => 'required|exists:wilayah,id',
            'coa'               => 'nullable|string|max:255',
            'program_investasi' => 'nullable|string|max:255',
            'tipe'              => 'required|string|max:255',
            'tipe_investasi'    => 'required|string|max:255',
            'nomor_prodef_sap'  => 'nullable|string|max:255',
            'nama_investasi'    => 'required|string|max:255',
            'kebutuhan_dana'    => 'required|numeric',
            'rkap'              => 'required|array',
            'rkap.*'            => 'nullable|numeric',
            'tahun_usulan'      => 'required|digits:4|integer',
            'coa_sub'           => 'required|string|max:255',
            'kategori'          => 'required|string|max:255',
            'manfaat'           => 'required|string|max:255',
            'jenis'             => 'required|string|max:255',
            'sifat'             => 'required|string|max:255',
            'urgensi'           => 'required|string|max:255',
        ]);

        $pekerjaan = Pekerjaan::findOrFail($id);

        // Hitung ulang total RKAP
        $totalRkap = array_sum($request->rkap);

        $gambarPath = $pekerjaan->gambar;

        if ($request->hasFile('gambar')) {
            // hapus gambar lama jika ada
            if ($pekerjaan->gambar && Storage::disk('public')->exists($pekerjaan->gambar)) {
                Storage::disk('public')->delete($pekerjaan->gambar);
            }
            $gambarPath = $request->file('gambar')->store('gambar_pekerjaan', 'public');
        }

        // Update pekerjaan utama
        $pekerjaan->update([
            'wilayah_id'        => $request->wilayah_id,
            'coa'               => $request->coa,
            'program_investasi' => $request->program_investasi,
            'tipe_investasi'    => $request->tipe_investasi,
            'nomor_prodef_sap'  => $request->nomor_prodef_sap,
            'nama_investasi'    => $request->nama_investasi,
            'kebutuhan_dana'    => $request->kebutuhan_dana,
            'total_dana'        => $totalRkap,
            'tahun_usulan'      => $request->tahun_usulan,
            'gambar'            => $gambarPath,
            'user_id'           => auth()->id(),
        ]);

        // Update atau create master investasi
        if ($pekerjaan->masterInvestasi) {
            $pekerjaan->masterInvestasi->update([
                'tipe'      => $request->tipe,
                'coa_sub'   => $request->coa_sub,
                'kategori'  => $request->kategori,
                'manfaat'   => $request->manfaat,
                'jenis'     => $request->jenis,
                'sifat'     => $request->sifat,
                'urgensi'   => $request->urgensi,
            ]);
        } else {
            $pekerjaan->masterInvestasi()->create([
                'tipe'      => $request->tipe,
                'coa_sub'   => $request->coa_sub,
                'kategori'  => $request->kategori,
                'manfaat'   => $request->manfaat,
                'jenis'     => $request->jenis,
                'sifat'     => $request->sifat,
                'urgensi'   => $request->urgensi,
            ]);
        }

        // Refresh RKAP detail (hapus lama, insert baru)
        $pekerjaan->rkapDetails()->delete();
        foreach ($request->rkap as $tahun => $nilai) {
            $pekerjaan->rkapDetails()->create([
                'tahun' => $tahun,
                'nilai' => $nilai ?? 0,
            ]);
        }

        return redirect()->route('pekerjaan.index')
                        ->with('success', 'Rencana kerja, master investasi, dan RKAP per tahun berhasil diperbarui!');
    }


    public function destroy(int $id): RedirectResponse
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        if ($pekerjaan->masterInvestasi) {
            $pekerjaan->masterInvestasi()->delete();
        }
        $pekerjaan->delete();

        return redirect()->route('pekerjaan.index')
                         ->with('success', 'Rencana kerja dan master investasi berhasil dihapus!');
    }

    
    
    // /**
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getJsonDetails($id)
    // {
    //     $pekerjaan = Pekerjaan::with('wilayah')->find($id);

    //     if (!$pekerjaan) {
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     }
    //     return response()->json($pekerjaan);
    // }
}