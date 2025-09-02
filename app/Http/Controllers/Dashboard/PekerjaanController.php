<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Pekerjaan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PekerjaanController extends Controller
{
    /**
     * Tampilkan daftar pekerjaan
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = auth()->user();

        $query = Pekerjaan::with('wilayah');

        // ✅ Superadmin bisa lihat semua, selain itu hanya wilayahnya sendiri
        if ($user->role !== 'superadmin' && $user->wilayah_id) {
            $query->where('wilayah_id', $user->wilayah_id);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pekerjaan', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%");
            })->orWhereHas('wilayah', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $pekerjaans = $query->orderBy('tahun', 'desc')->paginate(10);

        // Ambil list tahun unik sesuai role
        $tahunListQuery = Pekerjaan::query();
        if ($user->role !== 'superadmin' && $user->wilayah_id) {
            $tahunListQuery->where('wilayah_id', $user->wilayah_id);
        }

        $tahunList = $tahunListQuery->select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('Dashboard.rencana_pekerjaan', compact('pekerjaans', 'tahunList'));
    }

    /**
     * Form tambah pekerjaan
     *
     * @return View
     */
    public function create(): View
    {
        $wilayahs = Wilayah::all();
        return view('Dashboard.Pekerjaan.create', compact('wilayahs'));
    }

    /**
     * Simpan pekerjaan baru
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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

        Pekerjaan::create([
            'wilayah_id'     => $request->wilayah_id,
            'user_id'        => auth()->id(), // ✅ user login otomatis tersimpan
            'nama_pekerjaan' => $request->nama_pekerjaan,
            'status'         => $request->status,
            'nilai'          => $request->nilai,
            'kebutuhan_dana' => $request->kebutuhan_dana,
            'tahun'          => $request->tahun,
            'tanggal'        => $request->tanggal,
        ]);

        return redirect()->route('dashboard.kota', ['id' => $request->wilayah_id])
                         ->with('success', 'Rencana kerja berhasil ditambahkan!');
    }

    /**
     * Form edit pekerjaan
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $wilayahs = Wilayah::all();
        return view('Dashboard.Pekerjaan.edit', compact('pekerjaan', 'wilayahs'));
    }

    /**
     * Update pekerjaan
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
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
        $pekerjaan->update([
            'wilayah_id'     => $request->wilayah_id,
            'user_id'        => auth()->id(), // ✅ update tetap catat user yang edit
            'nama_pekerjaan' => $request->nama_pekerjaan,
            'status'         => $request->status,
            'nilai'          => $request->nilai,
            'kebutuhan_dana' => $request->kebutuhan_dana,
            'tahun'          => $request->tahun,
            'tanggal'        => $request->tanggal,
        ]);

        return redirect()->route('dashboard.kota', ['id' => $request->wilayah_id])
                         ->with('success', 'Rencana kerja berhasil diupdate!');
    }

    /**
     * Hapus pekerjaan
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $wilayah_id = $pekerjaan->wilayah_id;
        $pekerjaan->delete();

        return redirect()->route('dashboard.kota', ['id' => $wilayah_id])
                         ->with('success', 'Rencana kerja berhasil dihapus!');
    }
}