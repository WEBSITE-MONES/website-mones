<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pekerjaan;
use App\Models\Pr;
use App\Models\Po;
use App\Models\Gr;
use App\Models\Payment;

class RealisasiController extends Controller
{
    // Daftar PR
    public function index()
{
    $prs = Pr::with('pekerjaan')
        ->orderByRaw("
            CASE 
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PR' THEN 1
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PO' THEN 2
                WHEN UPPER(TRIM(status_pekerjaan)) = 'GR' THEN 3
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PAYMENT' THEN 4
                ELSE 5
            END
        ")
        ->orderBy('id', 'asc') // biar stabil kalau status sama
        ->paginate(10);

    return view('Dashboard.Pekerjaan.realisasi_pekerjaan', compact('prs'));
}


    // Form input PR
    public function createPR()
    {
        $pekerjaans = Pekerjaan::whereDoesntHave('prs')
    ->orderBy('nama_investasi')
    ->get();
        return view('Dashboard.Pekerjaan.Realisasi.create_pr', compact('pekerjaans'));
    }

    // Simpan PR
    public function storePR(Request $request)
    {
        $request->validate([
            'jenis_pekerjaan' => 'required|array|min:1',
            'jenis_pekerjaan.*' => 'in:Konsultan Perencana,Pelaksanaan Fisik,Konsultan Pengawas',
            'pekerjaan_id'    => 'required|exists:pekerjaan,id',
            'nilai_pr'        => 'required|numeric|min:0',
            'nomor_pr'        => 'required|string|max:255|unique:prs,nomor_pr',
            'tanggal_pr'      => 'required|date',
        ]);

        $pr = Pr::create([
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'pekerjaan_id' => $request->pekerjaan_id,
            'nilai_pr' => $request->nilai_pr,
            'nomor_pr' => $request->nomor_pr,
            'tanggal_pr' => $request->tanggal_pr,
            'status_pekerjaan' => 'PR',
        ]);

        // Update status pekerjaan langsung jadi PR
        $pr->pekerjaan->update(['status_realisasi' => 'PR']);

        return redirect()->route('realisasi.index')
                         ->with('success', 'Data PR berhasil ditambahkan.');
    }

    // Update status PR → PO → GR → Payment
    public function updateStatus(Pr $pr, $status)
    {
        $validStatus = ['PR', 'PO', 'GR', 'Payment'];
        if (!in_array($status, $validStatus)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $pr->update(['status_pekerjaan' => $status]);
        $pr->pekerjaan->update(['status_realisasi' => $status]);

        return redirect()->back()->with('success', "Status berhasil diubah ke $status");
    }

    // Form edit PR
public function editPR(Pr $pr)
{
    $pekerjaans = Pekerjaan::orderBy('nama_investasi')->get();
    return view('Dashboard.Pekerjaan.Realisasi.edit_pr', compact('pr', 'pekerjaans'));
}

// Update PR
public function updatePR(Request $request, Pr $pr)
{
    $request->validate([
        'jenis_pekerjaan' => 'required|array|min:1',
        'jenis_pekerjaan.*' => 'in:Konsultan Perencana,Pelaksanaan Fisik,Konsultan Pengawas',
        'pekerjaan_id' => 'required|exists:pekerjaan,id',
        'nilai_pr' => 'required|numeric|min:0',
        'nomor_pr' => 'required|string|max:255|unique:prs,nomor_pr,' . $pr->id,
        'tanggal_pr' => 'required|date',
    ]);

    $pr->update([
        'jenis_pekerjaan' => $request->jenis_pekerjaan,
        'pekerjaan_id' => $request->pekerjaan_id,
        'nilai_pr' => $request->nilai_pr,
        'nomor_pr' => $request->nomor_pr,
        'tanggal_pr' => $request->tanggal_pr,
    ]);

    return redirect()->route('realisasi.index')->with('success', 'PR berhasil diperbarui.');
}

// PO
public function createPO(Pr $pr)
{
    return view('Dashboard.Pekerjaan.Realisasi.create_po', compact('pr'));
}

// STORE
public function storePO(Request $request, Pr $pr)
{
    $request->validate([
        'nomor_po' => 'required|string|max:255|unique:pos,nomor_po',
        'nilai_po' => 'required|numeric|min:0',
        'tanggal_po' => 'required|date',
        'nomor_kontrak' => 'nullable|string|max:255',
        'estimated' => 'nullable|string|max:255',
        'waktu_pelaksanaan' => 'nullable|string|max:255',
        'pelaksana' => 'nullable|string|max:255',
        'mekanisme_pembayaran' => 'nullable|in:Uang muka,Termin',
    ]);

    // Simpan ke tabel pos
    Po::create([
        'pr_id' => $pr->id,
        'nomor_po' => $request->nomor_po,
        'nomor_kontrak' => $request->nomor_kontrak,
        'nilai_po' => $request->nilai_po,
        'estimated' => $request->estimated,
        'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
        'pelaksana' => $request->pelaksana,
        'mekanisme_pembayaran' => $request->mekanisme_pembayaran,
        'tanggal_po' => $request->tanggal_po,
    ]);

    // Update status pekerjaan
    $pr->update(['status_pekerjaan' => 'PO']);
    $pr->pekerjaan->update(['status_realisasi' => 'PO']);

    return redirect()->route('realisasi.index')->with('success', 'PO berhasil ditambahkan.');
}

// EDIT
 public function editPO(Po $po)
    {
        return view('Dashboard.Pekerjaan.Realisasi.edit_po', compact('po'));
    }
    
// UPDATE
public function updatePO(Request $request, Po $po)
{
    $request->validate([
        'nomor_po' => 'required|string|max:255|unique:pos,nomor_po,' . $po->id,
        'nilai_po' => 'required|numeric|min:0',
        'tanggal_po' => 'required|date',
        'nomor_kontrak' => 'nullable|string|max:255',
        'estimated_start' => 'nullable|date',
        'estimated_end' => 'nullable|date|after_or_equal:estimated_start',
        'waktu_pelaksanaan' => 'nullable|numeric|min:1',
        'pelaksana' => 'nullable|string|max:255',
        'mekanisme_pembayaran' => 'nullable|in:Uang muka,Termin',
    ]);

    $po->update([
        'nomor_po' => $request->nomor_po,
        'nomor_kontrak' => $request->nomor_kontrak,
        'nilai_po' => $request->nilai_po,
        'estimated_start' => $request->estimated_start,
        'estimated_end' => $request->estimated_end,
        'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
        'pelaksana' => $request->pelaksana,
        'mekanisme_pembayaran' => $request->mekanisme_pembayaran,
        'tanggal_po' => $request->tanggal_po,
    ]);

    return redirect()->route('realisasi.index')->with('success', 'PO berhasil diperbarui.');
}

// PROGRES
// FORM EDIT
public function editProgress(Po $po)
{
    $progresses = $po->progresses()->orderBy('bulan')->get();

    return view('Dashboard.Pekerjaan.Realisasi.edit_progress', compact('po', 'progresses'));
}

// UPDATE / SIMPAN
public function updateProgress(Request $request, Po $po)
{
    $request->validate([
        'progress' => 'required|integer|min:0|max:100',
        'bulan' => 'required|integer|min:1|max:12', // pastikan 1-12
        'nomor_ba_mulai_kerja' => 'nullable|string|max:100',
        'tanggal_ba_mulai_kerja' => 'nullable|date',
    ]);

    $data = $request->only(['progress','bulan','nomor_ba_mulai_kerja','tanggal_ba_mulai_kerja']);

    

    // cek apakah progress untuk bulan tsb sudah ada
    $progress = $po->progresses()->where('bulan', $request->bulan)->first();

    if ($progress) {
        // update progress bulan tsb
        $progress->update($data);
    } else {
        // buat progress baru untuk bulan tsb
        $po->progresses()->create($data);
    }

    return redirect()->route('realisasi.index')->with('success', 'Progress bulan '.$request->bulan.' berhasil disimpan.');
}



// GR
// Form input GR
public function createGR(Pr $pr)
{
    
    $po = $pr->po ?? null;
    return view('Dashboard.Pekerjaan.Realisasi.create_gr', compact('pr', 'po'));
}

// Simpan GR
public function storeGR(Request $request, Pr $pr)
{
    $request->validate([
        'tanggal_gr' => 'required|date',
        'nomor_gr'   => 'required|string|max:255|unique:grs,nomor_gr',
        'nilai_gr'   => 'required|numeric|min:0',
    ]);

    Gr::create([
        'pr_id'      => $pr->id,
        'tanggal_gr' => $request->tanggal_gr,
        'nomor_gr'   => $request->nomor_gr,
        'nilai_gr'   => $request->nilai_gr,
    ]);

    // update status pekerjaan
    $pr->update(['status_pekerjaan' => 'GR']);
    $pr->pekerjaan->update(['status_realisasi' => 'GR']);

    return redirect()->route('realisasi.index')->with('success', 'GR berhasil ditambahkan.');
}

// Form edit GR
public function editGR(Pr $pr)
{
    $gr = $pr->gr ?? null;
    $po = $pr->po ?? null;

    if (!$gr) {
        return redirect()->back()->with('error', 'GR belum tersedia untuk PR ini.');
    }

    return view('Dashboard.Pekerjaan.Realisasi.edit_gr', compact('pr', 'po', 'gr'));
}

// Update GR
public function updateGR(Request $request, Pr $pr, Gr $gr)
{
    $request->validate([
        'tanggal_gr' => 'required|date',
        'nomor_gr'   => 'required|string|max:255|unique:grs,nomor_gr,' . $gr->id,
        'nilai_gr'   => 'required|numeric|min:0',
    ]);

    $gr->update([
        'tanggal_gr' => $request->tanggal_gr,
        'nomor_gr'   => $request->nomor_gr,
        'nilai_gr'   => $request->nilai_gr,
    ]);

    return redirect()->route('realisasi.index')->with('success', 'GR berhasil diperbarui.');
}

// FORM CREATE PAYMENT
public function createPayment(Pr $pr)
{
    $gr = $pr->gr ?? null; // ambil GR untuk nilai default
    return view('Dashboard.Pekerjaan.Realisasi.create_payment', compact('pr', 'gr'));
}

// STORE PAYMENT
public function storePayment(Request $request, Pr $pr)
{
    $request->validate([
        'tanggal_payment' => 'required|date',
        'nomor_payment' => 'required|string|unique:payments,nomor_payment',
        'nilai_payment' => 'required|numeric|min:0',
        'invoice' => 'nullable|file|mimes:pdf',
        'receipt' => 'nullable|file|mimes:pdf',
        'nodin_payment' => 'nullable|file|mimes:pdf',
        'bill' => 'nullable|file|mimes:pdf',
    ]);

    $data = $request->all();

    // Upload files
    foreach(['invoice','receipt','nodin_payment','bill'] as $file) {
        if($request->hasFile($file)) {
            $data[$file] = $request->file($file)->store('payments', 'public');
        }
    }

    $data['pr_id'] = $pr->id;

    Payment::create($data);

    // Update status PR → Payment
    $pr->update(['status_pekerjaan' => 'Payment']);
    $pr->pekerjaan->update(['status_realisasi' => 'Payment']);

    return redirect()->route('realisasi.index')->with('success', 'Payment Request berhasil ditambahkan.');
}




    // Hapus PR
    public function destroy(Pr $pr)
    {
        $pr->delete();
        return redirect()->back()->with('success', 'PR berhasil dihapus.');
    }
}