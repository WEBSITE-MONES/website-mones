<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Pekerjaan;
use App\Models\MasterInvestasi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;





class PekerjaanController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Pekerjaan::with(['wilayah', 'masterInvestasi']); // ✅ singular

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

        $pekerjaans = $query->orderBy('created_at', 'desc')->paginate(10);
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
            'program_investasi' => 'required|string|max:255',
            'tipe'              => 'required|string|max:255',
            'tipe_investasi'    => 'required|string|max:255',
            'nomor_prodef_sap'  => 'nullable|string|max:255',
            'nama_investasi'    => 'required|string|max:255',
            'kebutuhan_dana'    => 'required|numeric',
            'rkap'              => 'required|numeric',
            'tahun_usulan'      => 'required|digits:4|integer',
            'coa_sub'           => 'required|string|max:255',
            'kategori'          => 'required|string|max:255',
            'manfaat'           => 'required|string|max:255',
            'jenis'             => 'required|string|max:255',
            'sifat'             => 'required|string|max:255',
            'urgensi'           => 'required|string|max:255',
        ]);

        $pekerjaan = Pekerjaan::create([
            'wilayah_id'        => $request->wilayah_id,
            'coa'               => $request->coa,
            'program_investasi' => $request->program_investasi,
            'tipe_investasi'    => $request->tipe_investasi,
            'nomor_prodef_sap'  => $request->nomor_prodef_sap,
            'nama_investasi'    => $request->nama_investasi,
            'kebutuhan_dana'    => $request->kebutuhan_dana,
            'rkap'              => $request->rkap,
            'tahun_usulan'      => $request->tahun_usulan,
            'user_id'           => auth()->id(),
        ]);

        $pekerjaan->masterInvestasi()->create([
            'tipe'      => $request->tipe,
            'coa_sub'   => $request->coa_sub,
            'kategori'  => $request->kategori,
            'manfaat'   => $request->manfaat,
            'jenis'     => $request->jenis,
            'sifat'     => $request->sifat,
            'urgensi'   => $request->urgensi,
        ]);

        return redirect()->route('pekerjaan.index')
                         ->with('success', 'Rencana kerja dan master investasi berhasil ditambahkan!');
    }

    public function edit(int $id): View
    {
        $pekerjaan = Pekerjaan::with('masterInvestasi')->findOrFail($id); // ✅ singular
        $wilayahs = Wilayah::all();
        return view('Dashboard.Pekerjaan.edit', compact('pekerjaan', 'wilayahs'));
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
            'rkap'              => 'required|numeric',
            'tahun_usulan'      => 'required|digits:4|integer',
            'coa_sub'           => 'required|string|max:255',
            'kategori'          => 'required|string|max:255',
            'manfaat'           => 'required|string|max:255',
            'jenis'             => 'required|string|max:255',
            'sifat'             => 'required|string|max:255',
            'urgensi'           => 'required|string|max:255',
        ]);

        $pekerjaan = Pekerjaan::findOrFail($id);

        $pekerjaan->update([
            'wilayah_id'        => $request->wilayah_id,
            'coa'               => $request->coa,
            'program_investasi' => $request->program_investasi,
            'tipe_investasi'    => $request->tipe_investasi,
            'nomor_prodef_sap'  => $request->nomor_prodef_sap,
            'nama_investasi'    => $request->nama_investasi,
            'kebutuhan_dana'    => $request->kebutuhan_dana,
            'rkap'              => $request->rkap,
            'tahun_usulan'      => $request->tahun_usulan,
            'user_id'           => auth()->id(),
        ]);

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

        return redirect()->route('pekerjaan.index')
                         ->with('success', 'Rencana kerja dan master investasi berhasil diupdate!');
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

    
    
}