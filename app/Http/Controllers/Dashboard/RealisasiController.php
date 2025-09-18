<?php

namespace App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; //GEGARA LUPA TAMBAH INI ERRORKU 2 JAM SIELLL
use Illuminate\Http\Request;
use App\Models\Pekerjaan;
use App\Models\Pr;
use App\Models\Po;
use App\Models\Gr;
use App\Models\Payment;
use App\Models\Termin;
use App\Models\Progress;
use App\Models\ProgressSub;
use App\Models\ProgressDetail;

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
    // Bersihkan nilai PR dari titik, koma, spasi
    $nilaiPR = preg_replace('/[^\d]/', '', $request->nilai_pr);

    $request->merge([
        'nilai_pr' => $nilaiPR,
    ]);

    $request->validate([
        'jenis_pekerjaan' => 'required|in:Konsultan Perencana,Pelaksanaan Fisik,Konsultan Pengawas',
        'pekerjaan_id'    => 'required|exists:pekerjaan,id',
        'nilai_pr'        => 'required|numeric|min:0',
        'nomor_pr'        => 'required|string|max:255|unique:prs,nomor_pr',
        'tanggal_pr'      => 'required|date',
    ]);

    $pr = Pr::create([
        'jenis_pekerjaan' => $request->jenis_pekerjaan, // langsung string
        'pekerjaan_id'    => $request->pekerjaan_id,
        'nilai_pr'        => $nilaiPR,
        'nomor_pr'        => $request->nomor_pr,
        'tanggal_pr'      => $request->tanggal_pr,
        'tahun_anggaran'  => $request->tahun_anggaran,
        'status_pekerjaan'=> 'PR',
    ]);

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

// update PR
public function updatePR(Request $request, Pr $pr)
{
    $nilaiPR = preg_replace('/[^\d]/', '', $request->nilai_pr);

    $request->merge([
        'nilai_pr' => $nilaiPR,
    ]);

    $request->validate([
        'jenis_pekerjaan' => 'required|in:Konsultan Perencana,Pelaksanaan Fisik,Konsultan Pengawas',
        'pekerjaan_id'    => 'required|exists:pekerjaan,id',
        'nilai_pr'        => 'required|numeric|min:0',
        'nomor_pr'        => 'required|string|max:255|unique:prs,nomor_pr,' . $pr->id,
        'tanggal_pr'      => 'required|date',
    ]);

    $pr->update([
        'jenis_pekerjaan' => $request->jenis_pekerjaan,
        'pekerjaan_id'    => $request->pekerjaan_id,
        'nilai_pr'        => $nilaiPR,
        'nomor_pr'        => $request->nomor_pr,
        'tanggal_pr'      => $request->tanggal_pr,
        'tahun_anggaran'  => $request->tahun_anggaran ?? $pr->tahun_anggaran,
    ]);

    return redirect()->route('realisasi.index')->with('success', 'PR berhasil diperbarui.');
}

// PO
// CREATE
public function createPO(Pr $pr)
{
    return view('Dashboard.Pekerjaan.Realisasi.create_po', compact('pr'));
}

// STORE
public function storePO(Request $request, Pr $pr)
{
    // Bersihkan angka rupiah (Rp, titik, koma, dll)
    $request->merge([
        'nilai_po' => preg_replace('/[^\d]/', '', $request->nilai_po),
    ]);

    // Validasi input (sama seperti sebelumnya)
    $validated = $request->validate([
        'nomor_po'   => 'required|string|max:255|unique:pos,nomor_po',
        'nilai_po'   => 'required|numeric|min:0',
        'tanggal_po' => 'required|date',
        'nomor_kontrak' => 'nullable|string|max:255',
        'estimated_start' => 'nullable|date',
        'estimated_end'   => 'nullable|date|after_or_equal:estimated_start',
        'pelaksana' => 'nullable|string|max:255',
        'mekanisme_pembayaran' => 'nullable|in:uang_muka,termin',

        'termins' => 'nullable|array',
        'termins.*.uraian' => 'required_with:termins|string|max:255',
        'termins.*.persentase' => 'required_with:termins|numeric|min:0|max:100',
        'termins.*.syarat_pembayaran' => 'required_with:termins|string|min:1|max:65535',
    ]);

    // Buang termin kosong -> perbaikan: gunakan isset agar '0' tidak dianggap kosong
    $termins = collect($request->termins ?? [])->filter(function ($termin) {
        return isset($termin['uraian']) && trim($termin['uraian']) !== ''
            && isset($termin['persentase']) && $termin['persentase'] !== '';
    })->values()->toArray();

    $validated['termins'] = $termins;

    // Gabungkan estimated
    $validated['estimated'] = $request->estimated_start && $request->estimated_end
        ? $request->estimated_start . ' s/d ' . $request->estimated_end
        : null;

    // Hitung waktu pelaksanaan otomatis
    $validated['waktu_pelaksanaan'] = null;
    if ($request->estimated_start && $request->estimated_end) {
        $start = \Carbon\Carbon::parse($request->estimated_start);
        $end   = \Carbon\Carbon::parse($request->estimated_end);
        $validated['waktu_pelaksanaan'] = $start->diffInDays($end) + 1;
    }

    // hitung total persen hanya dari termin biasa (bukan uang muka)
    $totalPersen = collect($validated['termins'])
        ->filter(fn($item) => stripos($item['uraian'], 'uang muka') === false)
        ->sum(fn($item) => (float) $item['persentase']);

    if ($totalPersen != 100) {
        return back()->withErrors([
            'termins' => "Total persentase termin (tanpa uang muka) harus 100%. Saat ini: {$totalPersen}%",
        ])->withInput();
    }

    // mapping mekanisme_pembayaran -> ke format DB (minimal fix untuk menghindari error enum)
    $mapMekanisme = [
        'uang_muka' => 'Uang muka',
        'termin' => 'Termin',
    ];
    $dbMekanisme = isset($validated['mekanisme_pembayaran'])
        ? ($mapMekanisme[$validated['mekanisme_pembayaran']] ?? $validated['mekanisme_pembayaran'])
        : null;

    // Simpan ke DB
    \DB::transaction(function () use ($validated, $pr, $dbMekanisme) {
        $po = Po::create([
            'pr_id' => $pr->id,
            'nomor_po' => $validated['nomor_po'],
            'nomor_kontrak' => $validated['nomor_kontrak'],
            'nilai_po' => $validated['nilai_po'],
            'tanggal_po' => $validated['tanggal_po'],
            'estimated' => $validated['estimated'],
            'waktu_pelaksanaan' => $validated['waktu_pelaksanaan'],
            'pelaksana' => $validated['pelaksana'] ?? null,
            'mekanisme_pembayaran' => $dbMekanisme,
        ]);

        $nilaiPO = $validated['nilai_po'];

        foreach ($validated['termins'] as $termin) {
            $persen = $termin['persentase'];
            $nilai  = ($persen / 100) * $nilaiPO;

            $po->termins()->create([
                'uraian' => $termin['uraian'],
                'persentase' => $persen,
                'syarat_pembayaran' => $termin['syarat_pembayaran'] ?? null,
                'nilai_pembayaran' => round($nilai),
            ]);
        }

        $pr->update(['status_pekerjaan' => 'PO']);
        if ($pr->pekerjaan) {
            $pr->pekerjaan->update(['status_realisasi' => 'PO']);
        }
    });

    return redirect()->route('realisasi.index')->with('success', 'PO berhasil ditambahkan.');
}

// EDIT
public function editPO(Po $po)
{
    $po->load('termins');

    if ($po->estimated && str_contains($po->estimated, ' s/d ')) {
        [$start, $end] = explode(' s/d ', $po->estimated);
        $po->estimated_start = $start;
        $po->estimated_end   = $end;
    } else {
        $po->estimated_start = null;
        $po->estimated_end   = null;
    }

    // mapping balik mekanisme untuk form: DB punya "Uang muka"/"Termin" -> form pakai "uang_muka"/"termin"
    if (!empty($po->mekanisme_pembayaran)) {
        $lower = strtolower($po->mekanisme_pembayaran);
        if (strpos($lower, 'uang') !== false) {
            $po->mekanisme_pembayaran = 'uang_muka';
        } elseif (strpos($lower, 'termin') !== false) {
            $po->mekanisme_pembayaran = 'termin';
        }
    }

    return view('Dashboard.Pekerjaan.Realisasi.edit_po', compact('po'));
}

// UPDATE
public function updatePO(Request $request, Po $po)
{
    $rawNilaiPO = str_replace(['.', ','], '', $request->nilai_po);
    $request->merge(['nilai_po' => $rawNilaiPO]);

    // Buang termin kosong -> perbaikan sama seperti store
    $termins = collect($request->termins ?? [])->filter(function ($termin) {
        return isset($termin['uraian']) && trim($termin['uraian']) !== ''
            && isset($termin['persentase']) && $termin['persentase'] !== '';
    })->values()->toArray();

    $request->merge([
        'termins' => $termins,
        'nilai_po' => preg_replace('/[^\d]/', '', $request->nilai_po), // cleaning angka rupiah
    ]);

    $validated = $request->validate([
        'nomor_po'   => 'required|string|max:255|unique:pos,nomor_po,' . $po->id,
        'nilai_po'   => 'required|numeric|min:0',
        'tanggal_po' => 'required|date',
        'nomor_kontrak' => 'nullable|string|max:255',
        'estimated_start' => 'nullable|date',
        'estimated_end'   => 'nullable|date|after_or_equal:estimated_start',
        'waktu_pelaksanaan' => 'nullable|numeric|min:1',
        'pelaksana' => 'nullable|string|max:255',
        'mekanisme_pembayaran' => 'nullable|in:uang_muka,termin',

        'termins' => 'nullable|array',
        'termins.*.uraian' => 'required_with:termins|string|max:255',
        'termins.*.persentase' => 'required_with:termins|numeric|min:0|max:100',
        'termins.*.syarat_pembayaran' => 'required_with:termins|string|min:1|max:65535',
    ]);

    // Gabungkan estimated
    $validated['estimated'] = $request->estimated_start && $request->estimated_end
        ? $request->estimated_start . ' s/d ' . $request->estimated_end
        : null;

    // Validasi total persentase termin (tanpa uang muka)
    $totalPersen = collect($validated['termins'])
        ->filter(fn($item) => stripos($item['uraian'], 'uang muka') === false)
        ->sum(fn($item) => (float) $item['persentase']);

    if ($totalPersen != 100) {
        return back()->withErrors([
            'termins' => "Total persentase termin (tanpa uang muka) harus 100%. Saat ini: {$totalPersen}%",
        ])->withInput();
    }

    // mapping mekanisme_pembayaran -> ke format DB
    $mapMekanisme = [
        'uang_muka' => 'Uang muka',
        'termin' => 'Termin',
    ];
    $dbMekanisme = isset($validated['mekanisme_pembayaran'])
        ? ($mapMekanisme[$validated['mekanisme_pembayaran']] ?? $validated['mekanisme_pembayaran'])
        : null;

    \DB::transaction(function () use ($validated, $po, $dbMekanisme) {
        // simpan update (pastikan kolom yang di-update sesuai fillable)
        $po->update(array_merge($validated, [
            'mekanisme_pembayaran' => $dbMekanisme,
        ]));

        $nilaiPO = $validated['nilai_po'];

        // hapus termin lama, buat termin baru
        $po->termins()->delete();
        foreach ($validated['termins'] as $termin) {
            $persen = $termin['persentase'];
            // Perbaikan: jangan kurangi dengan DP; hitung langsung dari persentase total PO
            $nilai  = ($persen / 100) * $nilaiPO;

            $po->termins()->create([
                'uraian' => $termin['uraian'],
                'persentase' => $persen,
                'syarat_pembayaran' => $termin['syarat_pembayaran'] ?? null,
                'nilai_pembayaran' => round($nilai),
            ]);
        }
    });

    return redirect()->route('realisasi.index')->with('success', 'PO berhasil diperbarui.');
}

// PROGRES
    // EDIT
    public function editProgress(Po $po)
{
    $po->load('progresses.subs.details'); // supaya eager load
    return view('Dashboard.Pekerjaan.Realisasi.edit_progress', compact('po'));
}


public function updateProgress(Request $request, Po $po)
{
    $request->validate([
        'nomor_ba_mulai_kerja'     => 'nullable|string|max:255',
        'tanggal_ba_mulai_kerja'   => 'nullable|date',
        'file_ba'                  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

        'progress.*.jenis_pekerjaan'   => 'nullable|string|max:255',
        'subs.*.sub_pekerjaan'         => 'nullable|string|max:255',
        'subs.*.volume'                => 'nullable|numeric',
        'subs.*.satuan'                => 'nullable|string|max:50',
        'subs.*.bobot'                 => 'nullable|numeric',
        'details.*.minggu'             => 'nullable|integer',
        'details.*.tanggal_awal_minggu'=> 'nullable|date',
        'details.*.tanggal_akhir_minggu'=> 'nullable|date',
        'details.*.rencana'            => 'nullable|numeric',
        'details.*.realisasi'          => 'nullable|numeric',
    ]);

    /** ---------------- HEADER ---------------- **/
    $progressHeader = $po->progresses()->firstOrCreate([], []);
    $progressHeader->update([
        'nomor_ba_mulai_kerja'   => $request->nomor_ba_mulai_kerja,
        'tanggal_ba_mulai_kerja' => $request->tanggal_ba_mulai_kerja,
    ]);

    if ($request->hasFile('file_ba')) {
        $filePath = $request->file('file_ba')->store('ba_files', 'public');
        $progressHeader->update(['file_ba' => $filePath]);
    }

    /** ---------------- UPDATE DATA LAMA ---------------- **/
    foreach ($request->input('progress', []) as $id => $data) {
        Progress::where('id', $id)->update([
            'jenis_pekerjaan' => $data['jenis_pekerjaan'] ?? null,
        ]);
    }

    foreach ($request->input('subs', []) as $id => $data) {
        ProgressSub::where('id', $id)->update([
            'sub_pekerjaan' => $data['sub_pekerjaan'] ?? null,
            'volume'        => $data['volume'] ?? null,
            'satuan'        => $data['satuan'] ?? null,
            'bobot'         => $data['bobot'] ?? null,
        ]);
    }

    foreach ($request->input('details', []) as $id => $data) {
        ProgressDetail::where('id', $id)->update([
            'minggu'              => $data['minggu'] ?? null,
            'tanggal_awal_minggu' => $data['tanggal_awal_minggu'] ?? null,
            'tanggal_akhir_minggu'=> $data['tanggal_akhir_minggu'] ?? null,
            'rencana'             => $data['rencana'] ?? null,
            'realisasi'           => $data['realisasi'] ?? null,
        ]);
    }

    /** ---------------- INSERT BARU ---------------- **/
    $progressMap = []; // mapping progress baru
    foreach ($request->input('new_progress', []) as $idx => $data) {
        $progress = $po->progresses()->create([
            'jenis_pekerjaan' => $data['jenis_pekerjaan'] ?? null,
        ]);
        $progressMap["new_{$idx}"] = $progress->id;
    }

    $subMap = []; // mapping sub baru
    foreach ($request->input('new_subs', []) as $progressKey => $subs) {
        $realProgressId = $progressMap[$progressKey] ?? (is_numeric($progressKey) ? $progressKey : null);
        if (!$realProgressId) continue;

        foreach ($subs as $subIdx => $data) {
            $sub = ProgressSub::create([
                'progress_id'   => $realProgressId,
                'sub_pekerjaan' => $data['sub_pekerjaan'] ?? null,
                'volume'        => $data['volume'] ?? null,
                'satuan'        => $data['satuan'] ?? null,
                'bobot'         => $data['bobot'] ?? null,
            ]);

            // simpan mapping sub baru
            $subMap["{$progressKey}_{$subIdx}"] = $sub->id;
        }
    }

    /** ---------------- INSERT DETAIL BARU ---------------- **/
    foreach ($request->input('new_details', []) as $subKey => $details) {
        // kalau sub baru → ambil dari mapping
        $realSubId = $subMap[$subKey] ?? null;

        // kalau sub lama → key numeric langsung id sub
        if (!$realSubId && is_numeric($subKey)) {
            $realSubId = $subKey;
        }

        if (!$realSubId) continue;

        foreach ($details as $detail) {
            ProgressDetail::create([
                'sub_id'              => $realSubId,
                'minggu'              => $detail['minggu'] ?? null,
                'tanggal_awal_minggu' => $detail['tanggal_awal_minggu'] ?? null,
                'tanggal_akhir_minggu'=> $detail['tanggal_akhir_minggu'] ?? null,
                'rencana'             => $detail['rencana'] ?? null,
                'realisasi'           => $detail['realisasi'] ?? null,
            ]);
        }
    }

    /** ---------------- DELETE ---------------- **/
    ProgressDetail::destroy($request->input('delete_details', []));
    ProgressSub::destroy($request->input('delete_subs', []));
    Progress::destroy($request->input('delete_progress', []));

    return redirect()
        ->route('realisasi.editProgress', $po->id)
        ->with('success', 'Progress berhasil diperbarui.');
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

// Form input termin
public function createTermin(Po $po)
{
    return view('Dashboard.Pekerjaan.Realisasi.create_termin', compact('po'));
}

// Simpan termin
public function storeTermin(Request $request, Po $po)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'persentase' => 'required|numeric|min:0|max:100',
        'tanggal' => 'required|date',
    ]);

    $po->termins()->create($request->only('nama','persentase','tanggal'));

    return redirect()->back()->with('success', 'Termin berhasil ditambahkan.');
}

// Edit termin
public function editTermin(Termin $termin)
{
    return view('Dashboard.Pekerjaan.Realisasi.edit_termin', compact('termin'));
}

// Update termin
public function updateTermin(Request $request, Termin $termin)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'persentase' => 'required|numeric|min:0|max:100',
        'tanggal' => 'required|date',
    ]);

    $termin->update($request->only('nama','persentase','tanggal'));

    return redirect()->back()->with('success', 'Termin berhasil diperbarui.');
}

// Hapus termin
public function destroyTermin(Termin $termin)
{
    $termin->delete();
    return redirect()->back()->with('success', 'Termin berhasil dihapus.');
}



    // Hapus PR
    public function destroy(Pr $pr)
    {
        $pr->delete();
        return redirect()->back()->with('success', 'PR berhasil dihapus.');
    }
}