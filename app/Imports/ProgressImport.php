<?php

namespace App\Imports;

use App\Models\PekerjaanItem;
use App\Models\Progress;
use App\Models\ProgressDetail;
use App\Models\MasterMinggu;
use App\Models\Po;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProgressImport implements ToCollection, WithCalculatedFormulas
{
    protected $poId;
    protected $itemMap = [];
    protected $header = [];
    protected $parentDataCache = [];

    public function __construct($poId)
    {
        $this->poId = $poId;
    }

    public function collection(Collection $rows)
{
    Log::info("[ProgressImport] 🚀 Mulai import untuk PO: {$this->poId}. Total baris: " . $rows->count());

    if ($rows->isEmpty()) {
        Log::warning("[ProgressImport] ⚠️ File kosong untuk PO: {$this->poId}");
        return;
    }

    // --- STEP 1: Temukan baris header ---
    $headerRowIndex = null;
    $maxScan = min(20, $rows->count());
    
    for ($i = 0; $i < $maxScan; $i++) {
        $rowArr = $rows[$i]->toArray();
        $joined = strtolower(implode(' ', array_map(fn($c) => trim((string)$c), $rowArr)));
        
        if ((str_contains($joined, 'kode') && str_contains($joined, 'pekerjaan')) ||
            (str_contains($joined, 'jenis') && str_contains($joined, 'volume')) ||
            str_contains($joined, 'sub_pekerjaan')) {
            $headerRowIndex = $i;
            Log::info("[ProgressImport] ✅ Header ditemukan di baris: {$i}");
            break;
        }
    }

    if ($headerRowIndex === null) {
        $headerRowIndex = 0;
        Log::warning("[ProgressImport] ⚠️ Header tidak ditemukan, fallback ke baris 0");
    }

    $this->header = $rows[$headerRowIndex]->toArray();
    
    for ($i = 0; $i <= $headerRowIndex; $i++) {
        $rows->shift();
    }

    Log::info("[ProgressImport] 📋 Header columns: " . json_encode(array_filter($this->header, fn($h) => !empty($h))));
    Log::info("[ProgressImport] 📊 Sisa baris data: " . $rows->count());

    // --- STEP 2: Build column mapping ---
    $colMap = [];
    foreach ($this->header as $idx => $col) {
        if ($col === null || trim($col) === '') continue;
        
        $name = strtolower(trim((string)$col));
        $name = preg_replace('/\s+/', '_', $name);
        $name = preg_replace('/[^a-z0-9_]/', '', $name);
        $colMap[$name] = $idx;
    }

    // --- STEP 3: FIXED COLUMN DETECTION ---
    $idxKode = $colMap['kode_pekerjaan'] ?? $colMap['kode'] ?? 0;

    // PRIORITY 1: Exact match
    $idxJenisUtama = $colMap['jenis_pekerjaan_utama'] ?? $colMap['jenispekerjaanautama'] ?? null;
    $idxSubPekerjaan = $colMap['sub_pekerjaan'] ?? $colMap['subpekerjaan'] ?? null;
    $idxSubSub = $colMap['sub_sub_pekerjaan'] ?? $colMap['subsubpekerjaan'] ?? null;

    // PRIORITY 2: Pattern matching - HANYA jika exact match gagal
    // CRITICAL: Deteksi sub_sub DULU (lebih spesifik)
    if ($idxSubSub === null) {
        foreach ($colMap as $key => $idx) {
            $subCount = substr_count($key, 'sub');
            if ($subCount >= 2) {  // "sub_sub_pekerjaan" → count = 2
                $idxSubSub = $idx;
                Log::debug("[ProgressImport] ✅ sub_sub found: key='{$key}', idx={$idx}, subcount={$subCount}");
                break;
            }
        }
    }

    // Baru cari sub_pekerjaan SETELAH sub_sub ketemu
    if ($idxSubPekerjaan === null) {
        foreach ($colMap as $key => $idx) {
            // Skip jika sudah dipakai untuk sub_sub
            if ($idx === $idxSubSub) {
                continue;
            }
            
            $subCount = substr_count($key, 'sub');
            if ($subCount === 1) {  // "sub_pekerjaan" → count = 1
                $idxSubPekerjaan = $idx;
                Log::debug("[ProgressImport] ✅ sub_pekerjaan found: key='{$key}', idx={$idx}, subcount={$subCount}");
                break;
            }
        }
    }

    if ($idxJenisUtama === null) {
        foreach ($colMap as $key => $idx) {
            if (str_contains($key, 'jenis') && str_contains($key, 'utama')) {
                $idxJenisUtama = $idx;
                break;
            }
        }
    }

    $idxVolume = $colMap['volume'] ?? $colMap['vol'] ?? null;
    $idxSat = $colMap['sat'] ?? $colMap['satuan'] ?? null;

    $idxBobot = null;
    foreach ($this->header as $ix => $col) {
        if ($col && preg_match('/bobot.*%?/i', trim((string)$col))) {
            $idxBobot = $ix;
            break;
        }
    }

    Log::info("[ProgressImport] 🎯 FINAL Column indexes:", [
        'kode' => $idxKode,
        'jenis_utama' => $idxJenisUtama ?? 'NOT FOUND',
        'sub_pekerjaan' => $idxSubPekerjaan ?? 'NOT FOUND ❌',
        'sub_sub' => $idxSubSub ?? 'NOT FOUND',
        'volume' => $idxVolume ?? 'NOT FOUND',
        'sat' => $idxSat ?? 'NOT FOUND',
        'bobot' => $idxBobot ?? 'NOT FOUND'
    ]);

    if ($idxSubPekerjaan === null) {
        Log::error("[ProgressImport] ❌ CRITICAL: sub_pekerjaan column NOT DETECTED!");
        Log::error("[ProgressImport] Column map:", $colMap);
    }

    // --- STEP 4: Identifikasi kolom minggu ---
    $mingguColumnIndexes = [];
    foreach ($this->header as $ix => $col) {
        if ($col === null) continue;
        $colTrim = strtoupper(trim((string)$col));
        
        if (preg_match('/^M(\d+)$/i', $colTrim, $matches)) {
            $mingguColumnIndexes[$ix] = $colTrim;
        }
    }

    Log::info("[ProgressImport] 📅 Kolom minggu: " . json_encode($mingguColumnIndexes));

    // --- STEP 5: Setup PO & Progress Utama ---
    $po = Po::find($this->poId);
    if (!$po) {
        Log::error("[ProgressImport] ❌ PO tidak ditemukan: {$this->poId}");
        return;
    }

    $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
    if (!$progressUtama || empty($progressUtama->tanggal_ba_mulai_kerja)) {
        Log::error("[ProgressImport] ❌ Progress utama tidak valid");
        return;
    }

    // --- STEP 6: Generate MasterMinggu ---
    foreach ($mingguColumnIndexes as $colIdx => $mingguKode) {
        $existing = MasterMinggu::where('progress_id', $progressUtama->id)
            ->where('kode_minggu', $mingguKode)
            ->first();

        if (!$existing) {
            preg_match('/\d+/', $mingguKode, $matches);
            $weekNumber = (int)($matches[0] ?? 1);
            $index = $weekNumber - 1;
            
            $awal = Carbon::parse($progressUtama->tanggal_ba_mulai_kerja)
                ->addWeeks($index)
                ->startOfWeek();
            $akhir = $awal->copy()->endOfWeek();

            MasterMinggu::create([
                'progress_id' => $progressUtama->id,
                'kode_minggu' => $mingguKode,
                'tanggal_awal' => $awal,
                'tanggal_akhir' => $akhir,
            ]);
        }
    }

    // --- STEP 7: Loop Baris Data ---
    $successCount = 0;
    $skippedCount = 0;
    $rowCounter = 0;

    foreach ($rows as $row) {
        $rowCounter++;
        $r = $row->toArray();

        $nonEmptyCells = count(array_filter($r, fn($v) => !is_null($v) && trim((string)$v) !== ''));
        if ($nonEmptyCells === 0) {
            $skippedCount++;
            continue;
        }

        try {
            $kodePekerjaan = isset($r[$idxKode]) ? trim((string)($r[$idxKode] ?? '')) : '';

            if (empty($kodePekerjaan)) {
                $skippedCount++;
                continue;
            }

            $kodeLower = strtolower($kodePekerjaan);
            if (str_contains($kodeLower, 'jumlah') || 
                str_contains($kodeLower, 'total') || 
                str_contains($kodeLower, 'ppn')) {
                $skippedCount++;
                continue;
            }

            if (!preg_match('/^P\d+(\.\d+)*$/i', $kodePekerjaan)) {
                $skippedCount++;
                continue;
            }

            // Ambil data kolom
            $jenisPekerjaanUtama = $idxJenisUtama !== null ? trim((string)($r[$idxJenisUtama] ?? '')) : '';
            $subPekerjaan = $idxSubPekerjaan !== null ? trim((string)($r[$idxSubPekerjaan] ?? '')) : '';
            $subSubPekerjaan = $idxSubSub !== null ? trim((string)($r[$idxSubSub] ?? '')) : '';
            
            // DEBUG: Log 3 baris pertama
            if ($rowCounter <= 3) {
                Log::info("[ProgressImport] 📝 Row {$rowCounter} ({$kodePekerjaan}):", [
                    'jenis_raw' => $jenisPekerjaanUtama,
                    'sub_raw' => $subPekerjaan,
                    'subsub_raw' => $subSubPekerjaan,
                ]);
            }

            $volumeRaw = $idxVolume !== null ? ($r[$idxVolume] ?? null) : null;
            $sat = $idxSat !== null ? trim((string)($r[$idxSat] ?? '')) : '';
            $bobotRaw = $idxBobot !== null ? ($r[$idxBobot] ?? null) : null;

            $volume = $this->parseNumeric($volumeRaw);
            $bobot = $this->parsePercent($bobotRaw);

            $isChild = str_contains($kodePekerjaan, '.');
            $parentId = null;

            if ($isChild) {
                $parentKode = substr($kodePekerjaan, 0, strrpos($kodePekerjaan, '.'));
                $parentId = $this->itemMap[$parentKode] ?? null;
            }

            // Logika jenis_pekerjaan_utama untuk DB yang NOT NULL
            if ($isChild) {
                if (!empty($jenisPekerjaanUtama)) {
                    $jenisToSave = $jenisPekerjaanUtama;
                } elseif (isset($this->parentDataCache[$parentKode]['jenis'])) {
                    $jenisToSave = $this->parentDataCache[$parentKode]['jenis'];
                } else {
                    $jenisToSave = $subPekerjaan ?: ($subSubPekerjaan ?: 'SUB ITEM');
                }
            } else {
                $jenisToSave = !empty($jenisPekerjaanUtama) ? $jenisPekerjaanUtama : 'ITEM PEKERJAAN';
            }

            if ($isChild && empty($sat) && isset($this->parentDataCache[$parentKode]['sat'])) {
                $sat = $this->parentDataCache[$parentKode]['sat'];
            }
            if (empty($sat)) {
                $sat = 'Unit';
            }

            $item = PekerjaanItem::updateOrCreate(
                [
                    'po_id' => $this->poId,
                    'kode_pekerjaan' => $kodePekerjaan,
                ],
                [
                    'jenis_pekerjaan_utama' => $jenisToSave,
                    'sub_pekerjaan' => $subPekerjaan,
                    'sub_sub_pekerjaan' => $subSubPekerjaan,
                    'volume' => $volume,
                    'sat' => $sat,
                    'bobot' => $bobot,
                    'parent_id' => $parentId,
                ]
            );

            if (!$isChild) {
                $this->parentDataCache[$kodePekerjaan] = [
                    'sat' => $sat,
                    'jenis' => $jenisToSave
                ];
            }

            $this->itemMap[$kodePekerjaan] = $item->id;
            $successCount++;

            $progressItem = Progress::firstOrCreate([
                'po_id' => $this->poId,
                'pekerjaan_item_id' => $item->id,
            ]);

            $weekDataCount = 0;
            foreach ($mingguColumnIndexes as $colIdx => $mingguKode) {
                $value = $r[$colIdx] ?? null;
                $persenRencana = $this->parsePercent($value);

                if ($persenRencana > 0) {
                    $minggu = MasterMinggu::where('kode_minggu', $mingguKode)
                        ->where('progress_id', $progressUtama->id)
                        ->first();

                    if ($minggu) {
                        ProgressDetail::updateOrCreate(
                            [
                                'progress_id' => $progressItem->id,
                                'minggu_id' => $minggu->id,
                            ],
                            [
                                'bobot_rencana' => $persenRencana,
                                'volume_realisasi' => 0,
                                'bobot_realisasi' => 0,
                                'keterangan' => 'From Excel Import',
                            ]
                        );
                        $weekDataCount++;
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error("[ProgressImport] ❌ Error row {$rowCounter}: " . $e->getMessage());
            $skippedCount++;
            continue;
        }
    }

    Log::info("[ProgressImport] 🏁 Import selesai", [
        'total_rows' => $rowCounter,
        'sukses' => $successCount,
        'skipped' => $skippedCount,
        'rate' => $rowCounter > 0 ? round(($successCount / $rowCounter) * 100, 2) . '%' : '0%'
    ]);
}

    private function parseNumeric($val)
    {
        if ($val === null || $val === '') {
            return 0;
        }

        $val = trim((string)$val);
        if (is_numeric($val)) {
            return (float)$val;
        }
        // remove thousand separators '.' and ',' (attempt)
        $val = str_replace(['.', ','], ['', ''], $val);
        return is_numeric($val) ? (float)$val : 0;
    }

    private function parsePercent($val)
    {
        if ($val === null || $val === '') {
            return 0;
        }

        if (is_string($val) && str_starts_with($val, '=')) {
            Log::debug("[ProgressImport] Nilai persen berbentuk formula, di-skip: {$val}");
            return 0;
        }

        if (is_numeric($val)) {
            $float = (float)$val;
            if ($float > 0 && $float < 1) {
                $float = $float * 100;
            }
            return round($float, 2);
        }

        if (is_string($val)) {
            $clean = str_replace(['%', ' '], ['', ''], trim($val));
            $clean = str_replace(',', '.', $clean);
            $clean = str_replace('%', '', $clean);
            if (!is_numeric($clean)) return 0;
            $float = (float)$clean;
            if ($float > 0 && $float < 1) $float = $float * 100;
            return round($float, 2);
        }

        return 0;
    }
}