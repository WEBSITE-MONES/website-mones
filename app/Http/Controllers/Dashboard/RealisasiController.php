<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
use App\Models\DailyProgress;
use Illuminate\Support\Facades\Storage;


class RealisasiController extends Controller
{
    // Daftar PR
    public function index()
    {
        $prs = Pr::with('pekerjaan', 'subPekerjaan')
            ->orderByRaw("
            CASE 
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PR' THEN 1
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PO' THEN 2
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PROGRESS' THEN 3
                WHEN UPPER(TRIM(status_pekerjaan)) = 'GR' THEN 4
                WHEN UPPER(TRIM(status_pekerjaan)) = 'PAYMENT' THEN 5
                ELSE 6
            END
        ")
            ->orderBy('id', 'asc')
            ->get();

        // Ambil semua pekerjaan dengan master_investasi
        $pekerjaans = \App\Models\Pekerjaan::with('masterInvestasi', 'wilayah')
            ->orderBy('id', 'desc')
            ->get();

        return view('Dashboard.Pekerjaan.realisasi_pekerjaan', compact('prs', 'pekerjaans'));
    }


    // Form input PR
    public function createPR()
    {
        $pekerjaans = Pekerjaan::orderBy('nomor_prodef_sap')->get();

        return view('Dashboard.Pekerjaan.Realisasi.create_pr', compact('pekerjaans'));
    }

    // Simpan PR
    public function storePR(Request $request)
    {
        $nilaiPR = preg_replace('/[^\d]/', '', $request->nilai_pr);
        $request->merge(['nilai_pr' => $nilaiPR]);

        $request->validate([
            'jenis_pekerjaan' => 'required|in:Konsultan Perencana,Pelaksanaan Fisik,Konsultan Pengawas',
            'pekerjaan_id'    => 'required|exists:pekerjaan,id',
            'nilai_pr'        => 'required|numeric|min:0',
            'nomor_pr'        => 'required|string|max:255|unique:prs,nomor_pr',
            'tanggal_pr'      => 'required|date',
            'sub_pekerjaan'   => 'required|string|max:255',
        ]);

        $pr = Pr::create([
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'pekerjaan_id'    => $request->pekerjaan_id,
            'nilai_pr'        => $nilaiPR,
            'nomor_pr'        => $request->nomor_pr,
            'tanggal_pr'      => $request->tanggal_pr,
            'tahun_anggaran'  => $request->tahun_anggaran,
            'status_pekerjaan' => 'PR',
        ]);

        $pr->pekerjaan->update(['status_realisasi' => 'PR']);

        if ($request->filled('sub_pekerjaan')) {
            $pr->subPekerjaan()->create([
                'pekerjaan_id' => $pr->pekerjaan_id,
                'nama_sub'     => $request->sub_pekerjaan,
            ]);
        }

        return redirect()->route('realisasi.index')
            ->with('success', 'Data PR beserta sub-pekerjaan berhasil ditambahkan.');
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
            'sub_pekerjaan'   => 'required|string|max:255', // tambahkan validasi sub-pekerjaan
        ]);

        $pr->update([
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'pekerjaan_id'    => $request->pekerjaan_id,
            'nilai_pr'        => $nilaiPR,
            'nomor_pr'        => $request->nomor_pr,
            'tanggal_pr'      => $request->tanggal_pr,
            'tahun_anggaran'  => $request->tahun_anggaran ?? $pr->tahun_anggaran,
        ]);

        // Update sub-pekerjaan (single string)
        $sub = $pr->pekerjaan->subPekerjaan()->firstOrNew([]);
        $sub->nama_sub = $request->sub_pekerjaan;
        $sub->save();


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
        // ✅ CEK STATUS PR TERLEBIH DAHULU
        if (!in_array($pr->status_pekerjaan, ['PR'])) {
            return back()->withErrors([
                'status_pekerjaan' => "Tidak bisa menambahkan PO karena status pekerjaan saat ini adalah '{$pr->status_pekerjaan}'. 
                Hanya PR yang bisa dibuatkan PO."
            ])->withInput();
        }

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

    // INI WILAYAH PROGRESS

    // PROGRES
    public function editProgress(Po $po)
    {
        try {
            // Load relasi
            $po->load(['progresses.details.minggu', 'progresses.pekerjaanItem']);

            // Ambil progress utama (yang tidak terkait item spesifik)
            $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();

            // ✅ Ambil master minggu
            $masterMinggu = MasterMinggu::where('progress_id', $progressUtama->id ?? 0)
                ->orderBy('tanggal_awal')
                ->get();

            // ✅ VALIDASI: Jika master minggu kosong
            if ($masterMinggu->isEmpty()) {
                Log::warning('⚠️ Master Minggu kosong untuk PO', [
                    'po_id' => $po->id,
                    'progress_utama_id' => $progressUtama->id ?? 'NULL'
                ]);
            }

            // Ambil items pekerjaan (hierarki WBS)
            $items = PekerjaanItem::where('po_id', $po->id)
                ->with(['children.children'])
                ->whereNull('parent_id')
                ->orderBy('kode_pekerjaan')
                ->get();

            // ✅ Buat mapping bulan untuk header tabel
            $monthMap = [];
            foreach ($masterMinggu as $minggu) {
                $monthName = $minggu->tanggal_awal->translatedFormat('F Y');
                if (!isset($monthMap[$monthName])) {
                    $monthMap[$monthName] = ['colspan' => 0, 'minggus' => []];
                }
                $monthMap[$monthName]['colspan'] += 3; // 3 kolom per minggu (Rencana, Volume, Realisasi)
                $monthMap[$monthName]['minggus'][] = $minggu;
            }

            // ✅ Range tanggal untuk display
            $dateRanges = $masterMinggu->map(
                fn($m) => $m->tanggal_awal->format('d M') . ' - ' . $m->tanggal_akhir->format('d M')
            );

            // ✅ Identifikasi parent items (untuk exclude dari summary)
            $parentItemIds = PekerjaanItem::where('po_id', $po->id)
                ->whereNotNull('parent_id')
                ->distinct()
                ->pluck('parent_id')
                ->toArray();

            // ✅ Hitung total rencana & realisasi per minggu
            $totalRencanaPerMinggu = [];
            $totalRealisasiPerMinggu = [];

            foreach ($masterMinggu as $minggu) {
                $rencana = 0;
                $realisasi = 0;

                foreach ($po->progresses as $p) {
                    // Skip jika bukan item pekerjaan atau adalah parent
                    if (!$p->pekerjaan_item_id) continue;
                    if (in_array($p->pekerjaan_item_id, $parentItemIds)) continue;

                    $detail = $p->details->firstWhere('minggu_id', $minggu->id);
                    if ($detail) {
                        $rencana += (float) $detail->bobot_rencana;
                        $realisasi += (float) $detail->bobot_realisasi;
                    }
                }

                $totalRencanaPerMinggu[$minggu->id] = $rencana;
                $totalRealisasiPerMinggu[$minggu->id] = $realisasi;
            }

            // ✅ Kumulatif total untuk summary cards
            $rencanaPct = round(array_sum($totalRencanaPerMinggu), 2);
            $realisasiPct = round(array_sum($totalRealisasiPerMinggu), 2);
            $deviasiPct = round($realisasiPct - $rencanaPct, 2);

            // ✅ Map progress details untuk akses cepat di view
            $progressDetailsMap = [];
            foreach ($po->progresses as $progress) {
                if (!$progress->pekerjaan_item_id) continue;

                foreach ($progress->details as $detail) {
                    $progressDetailsMap[$progress->pekerjaan_item_id][$detail->minggu_id] = [
                        'bobot_rencana' => (float) $detail->bobot_rencana,
                        'volume_realisasi' => (float) $detail->volume_realisasi,
                        'bobot_realisasi' => (float) $detail->bobot_realisasi,
                        'keterangan' => $detail->keterangan
                    ];
                }
            }

            // ✅ Generate data untuk Kurva S
            $chartData = $this->generateCurveSData($po, $masterMinggu);

            // ✅ DEBUG LOG
            Log::info('📊 Edit Progress Data Prepared', [
                'po_id' => $po->id,
                'master_minggu_count' => $masterMinggu->count(),
                'items_count' => $items->count(),
                'chart_data_points' => count($chartData),
                'rencana_pct' => $rencanaPct,
                'realisasi_pct' => $realisasiPct,
                'deviasi_pct' => $deviasiPct
            ]);

            return view('Dashboard.Pekerjaan.Realisasi.edit_progress', compact(
                'po',
                'items',
                'masterMinggu',
                'monthMap',
                'dateRanges',
                'progressDetailsMap',
                'parentItemIds',
                'rencanaPct',
                'realisasiPct',
                'deviasiPct',
                'chartData'
            ));
        } catch (\Exception $e) {
            Log::error('❌ Error editProgress', [
                'po_id' => $po->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('realisasi.index')
                ->with('error', 'Gagal memuat halaman progress: ' . $e->getMessage());
        }
    }

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

                $progress = $po->progresses()->firstOrCreate([
                    'po_id' => $po->id,
                    'pekerjaan_item_id' => null
                ]);
                $progress->nomor_ba_mulai_kerja   = $validated['nomor_ba_mulai_kerja'] ?? $progress->nomor_ba_mulai_kerja;
                $progress->tanggal_ba_mulai_kerja = $validated['tanggal_ba_mulai_kerja'] ?? $progress->tanggal_ba_mulai_kerja;

                if ($request->hasFile('file_ba')) {
                    // Hapus file lama jika ada
                    if ($progress->file_ba && Storage::disk('public')->exists($progress->file_ba)) {
                        Storage::disk('public')->delete($progress->file_ba);
                    }
                    $progress->file_ba = $request->file('file_ba')->store('ba_files', 'public');
                }

                $progress->nomor_pcm_mulai_kerja   = $validated['nomor_pcm_mulai_kerja'] ?? $progress->nomor_pcm_mulai_kerja;
                $progress->tanggal_pcm_mulai_kerja = $validated['tanggal_pcm_mulai_kerja'] ?? $progress->tanggal_pcm_mulai_kerja;

                if ($request->hasFile('file_pcm')) {
                    if ($progress->file_pcm && Storage::disk('public')->exists($progress->file_pcm)) {
                        Storage::disk('public')->delete($progress->file_pcm);
                    }
                    $progress->file_pcm = $request->file('file_pcm')->store('pcm_files', 'public');
                }

                $progress->save();

                if (!empty($progress->tanggal_ba_mulai_kerja)) {
                    $existing = MasterMinggu::where('progress_id', $progress->id)->count();

                    if ($existing == 0) {
                        $start = Carbon::parse($progress->tanggal_ba_mulai_kerja);
                        $awal = $start->copy()->startOfWeek();
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
                ->with('success', 'Dokumen BA & PCM berhasil diperbarui!')
                ->with('activeTab', 'formProgress');
        } catch (\Throwable $e) {
            Log::error('Update Progress Error', [
                'po_id' => $po->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('realisasi.editProgress', $po->id)
                ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }
    public function importExcel(Request $request, Po $po)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new ProgressImport($po->id), $request->file('file'));

            return redirect()->back()
                ->with('success', 'Rencana progress berhasil diimport!')
                ->with('activeTab', 'rekapProgress');
        } catch (\Throwable $e) {

            return redirect()->back()
                ->with('error', 'Import gagal: ' . $e->getMessage())
                ->with('activeTab', 'rekapProgress');
        }
    }

    /**
     * GENERATE DATA KURVA S - KUMULATIF VERSION
     */
    private function generateCurveSData(Po $po, $masterMinggu)
    {
        try {
            $chartData = [];
            $cumulativeRencana = 0;
            $cumulativeRealisasi = 0;

            // ✅ PERBAIKAN: Identifikasi HANYA parent yang punya children
            $parentIds = PekerjaanItem::where('po_id', $po->id)
                ->whereHas('children') // ← Hanya yang benar-benar punya anak
                ->pluck('id')
                ->toArray();

            Log::info('🔍 Parent items identified', [
                'po_id' => $po->id,
                'parent_ids' => $parentIds
            ]);

            foreach ($masterMinggu as $minggu) {
                $rencanaWeek = 0;
                $realisasiWeek = 0;

                // Loop semua progress untuk minggu ini
                foreach ($po->progresses as $progress) {
                    // Skip jika bukan item pekerjaan
                    if (!$progress->pekerjaan_item_id) continue;

                    // ✅ PERBAIKAN: Skip HANYA jika item adalah parent yang punya children
                    if (in_array($progress->pekerjaan_item_id, $parentIds)) {
                        Log::debug('⏭️ Skipping parent item', [
                            'item_id' => $progress->pekerjaan_item_id,
                            'minggu' => $minggu->kode_minggu
                        ]);
                        continue;
                    }

                    // Ambil detail untuk minggu ini
                    $detail = $progress->details->firstWhere('minggu_id', $minggu->id);

                    if ($detail) {
                        $bobotRencana = (float) $detail->bobot_rencana;
                        $bobotRealisasi = (float) $detail->bobot_realisasi;

                        $rencanaWeek += $bobotRencana;
                        $realisasiWeek += $bobotRealisasi;

                        // ✅ LOG DETAIL untuk debugging
                        if ($bobotRealisasi > 0) {
                            Log::info('✅ Found realisasi data', [
                                'item_id' => $progress->pekerjaan_item_id,
                                'minggu' => $minggu->kode_minggu,
                                'bobot_rencana' => $bobotRencana,
                                'bobot_realisasi' => $bobotRealisasi,
                                'volume_realisasi' => $detail->volume_realisasi
                            ]);
                        }
                    }
                }

                // ✅ Akumulasi kumulatif
                $cumulativeRencana += $rencanaWeek;
                $cumulativeRealisasi += $realisasiWeek;

                // ✅ LOG per minggu
                Log::info('📊 Week calculation', [
                    'minggu' => $minggu->kode_minggu,
                    'rencana_week' => round($rencanaWeek, 4),
                    'realisasi_week' => round($realisasiWeek, 4),
                    'cumulative_rencana' => round($cumulativeRencana, 4),
                    'cumulative_realisasi' => round($cumulativeRealisasi, 4)
                ]);

                // ✅ Simpan data untuk chart (gunakan 4 desimal!)
                $chartData[] = [
                    'week' => $minggu->kode_minggu,
                    'week_label' => $minggu->tanggal_awal->format('d M') . ' - ' . $minggu->tanggal_akhir->format('d M'),
                    'rencana' => round($cumulativeRencana, 4), // ← 4 desimal!
                    'realisasi' => round($cumulativeRealisasi, 4), // ← 4 desimal!
                    'deviasi' => round($cumulativeRealisasi - $cumulativeRencana, 4),
                    'rencana_week' => round($rencanaWeek, 4),
                    'realisasi_week' => round($realisasiWeek, 4)
                ];
            }

            // ✅ Jika tidak ada data, buat dummy
            if (empty($chartData)) {
                Log::warning('⚠️ Chart data kosong', ['po_id' => $po->id]);

                $chartData = [[
                    'week' => 'M1',
                    'week_label' => 'Belum ada data',
                    'rencana' => 0,
                    'realisasi' => 0,
                    'deviasi' => 0,
                    'rencana_week' => 0,
                    'realisasi_week' => 0
                ]];
            }

            Log::info('📊 Chart Data Generated - FINAL', [
                'po_id' => $po->id,
                'data_points' => count($chartData),
                'final_rencana' => round($cumulativeRencana, 4),
                'final_realisasi' => round($cumulativeRealisasi, 4),
                'sample_data' => array_slice($chartData, 0, 3)
            ]);

            return $chartData;
        } catch (\Exception $e) {
            Log::error('❌ Error generateCurveSData', [
                'po_id' => $po->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return dummy data jika error
            return [[
                'week' => 'Error',
                'week_label' => 'Gagal memuat data',
                'rencana' => 0,
                'realisasi' => 0,
                'deviasi' => 0
            ]];
        }
    }

    /**
     * DOWNLOAD TEMPLATE EXCEL
     */
    public function downloadTemplate()
    {
        $path = public_path('templates/template_progress.xlsx');

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Template tidak ditemukan.');
        }

        return response()->download($path, 'template_progress.xlsx');
    }

    // -------------------------------------------------------------- sekarang kerja gr dan payment

    public function createGR(Pr $pr)
    {
        $po = $pr->po()->first();

        if (!$po) {
            return redirect()->back()->with('error', 'PO belum tersedia untuk PR ini.');
        }

        return view('Dashboard.Pekerjaan.Realisasi.create_gr', compact('pr', 'po'));
    }

    public function storeGR(Request $request, Pr $pr)
    {

        $request->merge([
            'nilai_gr' => isset($request->nilai_gr) ? preg_replace('/[^\d\-]/', '', $request->nilai_gr) : null,
        ]);

        $validated = $request->validate([
            'tanggal_gr' => 'required|date',
            'nomor_gr'   => 'required|string|max:255|unique:grs,nomor_gr',
            'nilai_gr'   => 'required|numeric|min:0',
            'ba_pemeriksaan'      => 'nullable|file|mimes:pdf|max:2048',
            'ba_serah_terima'     => 'nullable|file|mimes:pdf|max:2048',
            'ba_pembayaran'       => 'nullable|file|mimes:pdf|max:2048',
            'laporan_dokumentasi' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $po = $pr->po()->first();

        if (!$po) {
            return redirect()->back()->with('error', 'PO tidak ditemukan.');
        }

        $gr = \App\Models\Gr::create([
            'pr_id'      => $pr->id,
            'po_id'      => $po->id,
            'tanggal_gr' => $validated['tanggal_gr'],
            'nomor_gr'   => $validated['nomor_gr'],
            'nilai_gr'   => $validated['nilai_gr'],
        ]);

        foreach (
            [
                'ba_pemeriksaan'      => 'file_ba_pemeriksaan',
                'ba_serah_terima'     => 'file_ba_serah_terima',
                'ba_pembayaran'       => 'file_ba_pembayaran',
                'laporan_dokumentasi' => 'file_laporan_dokumentasi',
            ] as $inputName => $columnName
        ) {
            if ($request->hasFile($inputName)) {
                $path = $request->file($inputName)->store('gr', 'public');
                $gr->update([$columnName => $path]);
            }
        }

        // Update status PR dan pekerjaan
        $pr->update(['status_pekerjaan' => 'GR']);
        if ($pr->pekerjaan) {
            $pr->pekerjaan->update(['status_realisasi' => 'GR']);
        }

        return redirect()->route('realisasi.index')->with('success', 'GR berhasil ditambahkan.');
    }


    public function editGR(Pr $pr)
    {
        $gr = $pr->gr ?? null;
        $po = $pr->po ?? null;

        if (!$gr) {
            return redirect()->back()->with('error', 'GR belum tersedia untuk PR ini.');
        }

        $terminYangSudahDigunakan = Gr::where('po_id', $po->id)
            ->where('id', '!=', $gr->id)
            ->whereNotNull('termin_id')
            ->pluck('termin_id')
            ->toArray();

        // Termin yang masih bisa dipilih
        $termins = $po->termins()
            ->where(function ($query) use ($terminYangSudahDigunakan, $gr) {
                $query->whereNotIn('id', $terminYangSudahDigunakan)
                    ->orWhere('id', $gr->termin_id);
            })
            ->orderBy('id', 'asc')
            ->get();

        return view('Dashboard.Pekerjaan.Realisasi.edit_gr', compact('pr', 'po', 'gr', 'termins'));
    }


    public function updateGR(Request $request, Pr $pr, Gr $gr)
    {
        // Bersihkan format angka jadi murni number
        $cleanedNilai = preg_replace('/[^\d]/', '', $request->nilai_gr ?? '0');
        $request->merge(['nilai_gr' => $cleanedNilai]);

        $validated = $request->validate([
            'tanggal_gr' => 'required|date',
            'nomor_gr'   => 'required|string|max:255|unique:grs,nomor_gr,' . $gr->id,
            'nilai_gr'   => 'required|numeric|min:0',
            'termin_id'  => 'nullable|exists:termins,id', // 🔄 dibuat nullable
            'ba_pemeriksaan'      => 'nullable|file|mimes:pdf|max:2048',
            'ba_serah_terima'     => 'nullable|file|mimes:pdf|max:2048',
            'ba_pembayaran'       => 'nullable|file|mimes:pdf|max:2048',
            'laporan_dokumentasi' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $po = $pr->po()->first();
        if (!$po) {
            return redirect()->back()->with('error', 'PO tidak ditemukan.');
        }

        // 🔹 Jika termin diisi, pastikan belum digunakan GR lain
        if (!empty($validated['termin_id'])) {
            $terminSudahDipakai = Gr::where('po_id', $po->id)
                ->where('id', '!=', $gr->id)
                ->where('termin_id', $validated['termin_id'])
                ->exists();

            if ($terminSudahDipakai) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['termin_id' => 'Termin ini sudah digunakan GR lain. Pilih termin yang berbeda.']);
            }
        }

        // 🔹 Update data GR utama
        $gr->update([
            'tanggal_gr' => $validated['tanggal_gr'],
            'nomor_gr'   => $validated['nomor_gr'],
            'nilai_gr'   => $validated['nilai_gr'],
            'termin_id'  => $validated['termin_id'] ?? null,
        ]);

        foreach (
            [
                'ba_pemeriksaan'      => 'file_ba_pemeriksaan',
                'ba_serah_terima'     => 'file_ba_serah_terima',
                'ba_pembayaran'       => 'file_ba_pembayaran',
                'laporan_dokumentasi' => 'file_laporan_dokumentasi',
            ] as $inputName => $columnName
        ) {
            if ($request->hasFile($inputName)) {
                if ($gr->$columnName && Storage::disk('public')->exists($gr->$columnName)) {
                    Storage::disk('public')->delete($gr->$columnName);
                }

                $path = $request->file($inputName)->store('gr', 'public');
                $gr->update([$columnName => $path]);
            }
        }

        return redirect()->route('realisasi.index')->with('success', 'Data GR berhasil diperbarui.');
    }




    // FORM CREATE PAYMENT
    public function createPayment(Pr $pr)
    {
        $gr = $pr->gr;

        // Ambil PO yang terkait dengan PR ini
        $po = $pr->po;

        if (!$po) {
            return redirect()->back()->with('error', 'PO belum dibuat untuk PR ini.');
        }

        $termins = Termin::where('po_id', $po->id)
            ->whereNull('payment_id')
            ->orderBy('id')
            ->get();

        return view('Dashboard.Pekerjaan.Realisasi.create_payment', compact('pr', 'gr', 'termins'));
    }

    // STORE PAYMENT - PERBAIKAN
    public function storePayment(Request $request, Pr $pr)
    {
        Log::info('Store Payment - Data masuk:', $request->all());

        $request->validate([
            'gr_id'           => 'required|exists:grs,id',
            'tanggal_payment' => 'required|date',
            'nomor_payment'   => 'required|string|unique:payments,nomor_payment',
            'termin_ids'      => 'required|array|min:1',
            'termin_ids.*'    => 'exists:termins,id',
            'invoice'         => 'nullable|file|mimes:pdf|max:5120',
            'receipt'         => 'nullable|file|mimes:pdf|max:5120',
            'nodin_payment'   => 'nullable|file|mimes:pdf|max:5120',
            'bill'            => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'termin_ids.required' => 'Pilih minimal 1 termin untuk dibayar',
            'termin_ids.min' => 'Pilih minimal 1 termin untuk dibayar',
        ]);

        try {
            DB::beginTransaction();

            // Ambil PO dari PR
            $po = $pr->po;
            if (!$po) {
                throw new \Exception('PO tidak ditemukan untuk PR ini.');
            }

            // Ambil termin yang dipilih dari PO ini
            $selectedTermins = Termin::whereIn('id', $request->termin_ids)
                ->where('po_id', $po->id)
                ->whereNull('payment_id')
                ->get();

            Log::info('Termin yang dipilih:', [
                'po_id' => $po->id,
                'requested_ids' => $request->termin_ids,
                'found_count' => $selectedTermins->count(),
                'found_ids' => $selectedTermins->pluck('id')->toArray()
            ]);

            if ($selectedTermins->isEmpty()) {
                throw new \Exception('Termin yang dipilih tidak valid atau sudah dibayar');
            }

            // Hitung total nilai payment
            $totalNilaiPayment = $selectedTermins->sum('nilai_pembayaran');

            // Siapkan data payment
            $paymentData = [
                'pr_id'           => $pr->id,
                'gr_id'           => $request->gr_id,
                'tanggal_payment' => $request->tanggal_payment,
                'nomor_payment'   => $request->nomor_payment,
                'nilai_payment'   => $totalNilaiPayment,
            ];

            // Upload file jika ada
            foreach (['invoice', 'receipt', 'nodin_payment', 'bill'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $paymentData[$fileField] = $request->file($fileField)->store('payments', 'public');
                }
            }
            $payment = Payment::create($paymentData);

            Log::info('Payment dibuat:', ['id' => $payment->id]);

            $updatedCount = 0;
            foreach ($selectedTermins as $termin) {
                $termin->payment_id = $payment->id;
                if ($termin->save()) {
                    $updatedCount++;
                }
            }

            Log::info('Termin di-update:', [
                'expected' => $selectedTermins->count(),
                'success' => $updatedCount
            ]);

            // Update status PR & pekerjaan
            $pr->update(['status_pekerjaan' => 'Payment']);
            if ($pr->pekerjaan) {
                $pr->pekerjaan->update(['status_realisasi' => 'Payment']);
            }

            DB::commit();

            return redirect()->route('realisasi.index')
                ->with('success', "Payment Request berhasil! {$updatedCount} termin dibayar (Total: Rp " . number_format($totalNilaiPayment, 0, ',', '.') . ")");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Store Payment:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // EDIT PAYMENT
    public function editPayment(Pr $pr, Payment $payment)
    {
        $gr = $pr->gr;

        // Ambil PO dari PR
        $po = $pr->po;

        if (!$po) {
            return redirect()->back()->with('error', 'PO tidak ditemukan untuk PR ini.');
        }

        $selectedTermins = Termin::where('payment_id', $payment->id)
            ->where('po_id', $po->id)
            ->get();

        $availableTermins = Termin::where('po_id', $po->id)
            ->whereNull('payment_id')
            ->orderBy('id')
            ->get();

        return view('Dashboard.Pekerjaan.Realisasi.edit_payment', compact(
            'pr',
            'gr',
            'payment',
            'selectedTermins',
            'availableTermins'
        ));
    }

    // UPDATE PAYMENT
    public function updatePayment(Request $request, Pr $pr, Payment $payment)
    {
        Log::info('Update Payment - Data masuk:', $request->all());

        $request->validate([
            'gr_id'           => 'required|exists:grs,id',
            'tanggal_payment' => 'required|date',
            'nomor_payment'   => 'required|string|unique:payments,nomor_payment,' . $payment->id,
            'termin_ids'      => 'required|array|min:1',
            'termin_ids.*'    => 'exists:termins,id',
            'invoice'         => 'nullable|file|mimes:pdf|max:5120',
            'receipt'         => 'nullable|file|mimes:pdf|max:5120',
            'nodin_payment'   => 'nullable|file|mimes:pdf|max:5120',
            'bill'            => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'termin_ids.required' => 'Pilih minimal 1 termin untuk dibayar',
            'termin_ids.min' => 'Pilih minimal 1 termin untuk dibayar',
        ]);

        try {
            DB::beginTransaction();

            // Ambil PO dari PR
            $po = $pr->po;
            if (!$po) {
                throw new \Exception('PO tidak ditemukan untuk PR ini.');
            }

            $oldTermins = Termin::where('payment_id', $payment->id)->get();
            foreach ($oldTermins as $termin) {
                $termin->payment_id = null;
                $termin->save();
            }

            Log::info('Termin lama di-reset:', [
                'payment_id' => $payment->id,
                'count' => $oldTermins->count()
            ]);

            // 2. Validasi termin baru yang dipilih
            $selectedTermins = Termin::whereIn('id', $request->termin_ids)
                ->where('po_id', $po->id)
                ->whereNull('payment_id') // pastikan belum dibayar
                ->get();

            Log::info('Termin baru yang valid:', [
                'requested_ids' => $request->termin_ids,
                'found_count' => $selectedTermins->count(),
                'found_ids' => $selectedTermins->pluck('id')->toArray()
            ]);

            if ($selectedTermins->isEmpty()) {
                throw new \Exception('Termin yang dipilih tidak valid atau sudah dibayar oleh payment lain');
            }

            $totalNilaiPayment = $selectedTermins->sum('nilai_pembayaran');

            $data = [
                'gr_id'           => $request->gr_id,
                'tanggal_payment' => $request->tanggal_payment,
                'nomor_payment'   => $request->nomor_payment,
                'nilai_payment'   => $totalNilaiPayment,
            ];

            foreach (['invoice', 'receipt', 'nodin_payment', 'bill'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    if ($payment->$fileField) {
                        Storage::disk('public')->delete($payment->$fileField);
                    }
                    $data[$fileField] = $request->file($fileField)->store('payments', 'public');

                    Log::info("File {$fileField} di-upload:", [
                        'path' => $data[$fileField]
                    ]);
                }
            }

            $payment->update($data);

            Log::info('Payment di-update:', [
                'payment_id' => $payment->id,
                'nilai_payment' => $totalNilaiPayment
            ]);

            $updatedCount = 0;
            foreach ($selectedTermins as $termin) {
                $termin->payment_id = $payment->id;
                if ($termin->save()) {
                    $updatedCount++;
                }
            }

            Log::info('Termin baru di-link:', [
                'expected' => $selectedTermins->count(),
                'success' => $updatedCount
            ]);

            DB::commit();

            return redirect()->route('realisasi.index')
                ->with('success', "Payment Request berhasil diperbarui! {$updatedCount} termin terkait (Total: Rp " . number_format($totalNilaiPayment, 0, ',', '.') . ")");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Update Payment:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }




    // --------------------------------------------------TERMIN-----------------------------------------------
    // Form input termin
    public function createTermin(Po $po)
    {
        return view('Dashboard.Pekerjaan.Realisasi.create_termin', compact('po'));
    }
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


    // Dropdown mingguan
    public function showWeeklyBreakdown(Po $po, $mingguId)
    {
        $minggu = MasterMinggu::findOrFail($mingguId);

        // Ambil semua daily progress untuk minggu ini
        $dailyReports = DailyProgress::with(['pekerjaanItem', 'pelapor'])
            ->where('po_id', $po->id)
            ->whereBetween('tanggal', [$minggu->tanggal_awal, $minggu->tanggal_akhir])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Group by item pekerjaan
        $reportsByItem = $dailyReports->groupBy('pekerjaan_item_id');

        return view('Dashboard.Pekerjaan.Realisasi.weekly_breakdown', compact(
            'po',
            'minggu',
            'reportsByItem'
        ));
    }
}