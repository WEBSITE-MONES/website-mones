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
use Carbon\Carbon;
use App\Models\MasterMinggu;
use App\Models\PekerjaanItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProgressImport;
use App\Models\Progress;

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
            'status_pekerjaan' => 'PR',
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
        // Simpan juga estimated_start & estimated_end ke validated
        $validated['estimated_start'] = $request->estimated_start;
        $validated['estimated_end']   = $request->estimated_end;

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
        DB::transaction(function () use ($validated, $pr, $dbMekanisme) {
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

        DB::transaction(function () use ($validated, $po, $dbMekanisme) {
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
        // Load progress beserta detail dan pekerjaan item
        $po->load(['progresses.details', 'progresses.pekerjaanItem']);

        // Ambil item pekerjaan top-level
        $items = PekerjaanItem::where('po_id', $po->id)
            ->with(['children.children'])
            ->whereNull('parent_id')
            ->orderBy('id')
            ->get();

        // Ambil progress pertama
        $progress = $po->progresses()->first();

        // Ambil MasterMinggu berdasarkan progress_id
        $masterMinggu = MasterMinggu::where('progress_id', $progress->id ?? 0)
            ->orderBy('tanggal_awal')
            ->get();

        // Header bulan
        $monthMap = [];
        foreach ($masterMinggu as $minggu) {
            $monthName = $minggu->tanggal_awal->translatedFormat('F Y');
            if (!isset($monthMap[$monthName])) {
                $monthMap[$monthName] = ['colspan' => 0, 'minggus' => []];
            }
            $monthMap[$monthName]['colspan'] += 5; 
            $monthMap[$monthName]['minggus'][] = $minggu;
        }

        // Range tanggal
        $dateRanges = $masterMinggu->map(fn($m) => $m->tanggal_awal->format('d M') . ' - ' . $m->tanggal_akhir->format('d M'));

        // Map progress per pekerjaan item
        $progressMap = $po->progresses->keyBy('pekerjaan_item_id');

        // Hitung kumulatif rencana & realisasi per minggu
        $totalRencanaPerMinggu = [];
        $totalRealisasiPerMinggu = [];
        foreach ($masterMinggu as $minggu) {
            $r = 0; $re = 0;
            foreach ($po->progresses as $p) {
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if ($d) {
                    $r += (float) $d->bobot_rencana;
                    $re += (float) $d->bobot_realisasi;
                }
            }
            $totalRencanaPerMinggu[$minggu->id] = $r;
            $totalRealisasiPerMinggu[$minggu->id] = $re;
        }

        $rencanaPct = round(array_sum($totalRencanaPerMinggu), 2);
        $realisasiPct = round(array_sum($totalRealisasiPerMinggu), 2);
        $deviasiPct = round($realisasiPct - $rencanaPct, 2);

        // Map detail (item + minggu)
        $progressDetailsMap = [];
        foreach ($po->progresses as $progress) {
            foreach ($progress->details as $detail) {
                $progressDetailsMap[$progress->pekerjaan_item_id][$detail->minggu_id] = $detail;
            }
        }

        return view('Dashboard.Pekerjaan.Realisasi.edit_progress', compact(
            'po',
            'items',
            'masterMinggu',
            'monthMap',
            'dateRanges',
            'progressMap',
            'progressDetailsMap',
            'rencanaPct',
            'realisasiPct',
            'deviasiPct'
        ));
    }
    // UPDATE PROGRESS BA DAN PCM
public function updateProgress(Request $request, Po $po)
{
    $rules = [
        // BA
        'nomor_ba_mulai_kerja'   => 'nullable|string|max:255',
        'tanggal_ba_mulai_kerja' => 'nullable|date',
        'file_ba'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

        // PCM
        'nomor_pcm_mulai_kerja'   => 'nullable|string|max:255',
        'tanggal_pcm_mulai_kerja' => 'nullable|date',
        'file_pcm'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ];

    $validated = $request->validate($rules);

    try {
        DB::transaction(function () use ($validated, $po, $request) {

            // ✅ Ambil atau buat progress utama (1 record per PO, tanpa pekerjaan_item_id)
            $progress = $po->progresses()->firstOrCreate(
                ['po_id' => $po->id, 'pekerjaan_item_id' => null]
            );

            // ✅ Update BA
            $progress->nomor_ba_mulai_kerja   = $validated['nomor_ba_mulai_kerja'] ?? $progress->nomor_ba_mulai_kerja;
            $progress->tanggal_ba_mulai_kerja = $validated['tanggal_ba_mulai_kerja'] ?? $progress->tanggal_ba_mulai_kerja;

            if ($request->hasFile('file_ba')) {
                $fileBa = $request->file('file_ba')->store('ba_files', 'public');
                $progress->file_ba = $fileBa;
            }

            // ✅ Update PCM
            $progress->nomor_pcm_mulai_kerja   = $validated['nomor_pcm_mulai_kerja'] ?? $progress->nomor_pcm_mulai_kerja;
            $progress->tanggal_pcm_mulai_kerja = $validated['tanggal_pcm_mulai_kerja'] ?? $progress->tanggal_pcm_mulai_kerja;

            if ($request->hasFile('file_pcm')) {
                $filePcm = $request->file('file_pcm')->store('pcm_files', 'public');
                $progress->file_pcm = $filePcm;
            }

            $progress->save();

            // ✅ Generate minggu pertama (M1) hanya sekali
            if (!empty($progress->tanggal_ba_mulai_kerja)) {
                $existing = MasterMinggu::where('progress_id', $progress->id)->count();

                if ($existing == 0) {
                    $start = Carbon::parse($progress->tanggal_ba_mulai_kerja);
                    $awal = $start->copy();
                    $akhir = $awal->copy()->endOfWeek();

                    MasterMinggu::create([
                        'progress_id'   => $progress->id,
                        'kode_minggu'   => 'M1',
                        'tanggal_awal'  => $awal,
                        'tanggal_akhir' => $akhir,
                    ]);
                }
            }
        });

        return redirect()->route('realisasi.editProgress', $po->id)
            ->with('success', 'Progress berhasil diperbarui.');
    } catch (\Throwable $e) {
        return redirect()->route('realisasi.editProgress', $po->id)
            ->with('error', 'Gagal memperbarui progress. Pesan error: ' . $e->getMessage());
    }
}



    public function importExcel(Request $request, Po $po)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    try {
        Excel::import(new ProgressImport($po->id), $request->file('file'));
        return back()->with('success', 'Data berhasil diimport!');
    } catch (\Throwable $e) {
        //dd($e->getMessage(), $e->getTraceAsString());
    }
}


    // TEMPLATE DOWNLOAD
    public function downloadTemplate()
    {
        $path = public_path('templates/template_progress.xlsx');
        return response()->download($path, 'template_progress.xlsx');
    }

    // DATA MODAL INPUT
    public function getModalData($itemId)
    {
        $item = PekerjaanItem::with('progress.details')->findOrFail($itemId);
        return view('Dashboard.Pekerjaan.Realisasi.modal_progress_form', compact('item'))->render();
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
        foreach (['invoice', 'receipt', 'nodin_payment', 'bill'] as $file) {
            if ($request->hasFile($file)) {
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

        $po->termins()->create($request->only('nama', 'persentase', 'tanggal'));

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

        $termin->update($request->only('nama', 'persentase', 'tanggal'));

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