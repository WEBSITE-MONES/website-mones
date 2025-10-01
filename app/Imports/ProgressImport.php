<?php

namespace App\Imports;

use App\Models\PekerjaanItem;
use App\Models\Progress;
use App\Models\ProgressDetail;
use App\Models\MasterMinggu;
use App\Models\Po;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProgressImport implements ToCollection
{
    protected $poId;
    protected $itemMap = [];
    protected $header = [];

    public function __construct($poId)
    {
        $this->poId = $poId;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            Log::error("ProgressImport: File Excel kosong");
            return;
        }

        // --- Ambil header
        $this->header = $rows->shift()->toArray();
        Log::info("ProgressImport: Header Excel", $this->header);

        // --- Cari kolom minggu
        $mingguColumns = collect($this->header)
            ->filter(fn($col) => preg_match('/^M\d+$/i', trim($col)))
            ->values();
        Log::info("ProgressImport: Kolom minggu ditemukan", $mingguColumns->toArray());

        // --- Ambil progress utama PO
        $po = Po::find($this->poId);
        if (!$po) {
            Log::error("ProgressImport: PO tidak ditemukan", ['po_id' => $this->poId]);
            return;
        }

        $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
        if (!$progressUtama || empty($progressUtama->tanggal_ba_mulai_kerja)) {
            Log::error("ProgressImport: Progress utama belum dibuat atau tanggal BA belum diisi", ['po_id' => $this->poId]);
            return;
        }

        // --- Pastikan minggu di MasterMinggu
        foreach ($mingguColumns as $col) {
            $mingguKode = strtoupper(trim($col));
            $existing = MasterMinggu::where('progress_id', $progressUtama->id)
                ->where('kode_minggu', $mingguKode)
                ->first();

            if (!$existing) {
                $index = (int) filter_var($mingguKode, FILTER_SANITIZE_NUMBER_INT) - 1;
                $awal  = Carbon::parse($progressUtama->tanggal_ba_mulai_kerja)->addWeeks($index);
                $akhir = $awal->copy()->endOfWeek();

                MasterMinggu::create([
                    'progress_id'   => $progressUtama->id,
                    'kode_minggu'   => $mingguKode,
                    'tanggal_awal'  => $awal,
                    'tanggal_akhir' => $akhir,
                ]);
                Log::info("ProgressImport: MasterMinggu dibuat", ['kode' => $mingguKode]);
            }
        }

        // --- Loop baris pekerjaan
        foreach ($rows as $rowIndex => $row) {
            $rowArray = $row->toArray();

            // skip baris kosong
            if (count(array_filter($rowArray, fn($v) => $v !== null && $v !== '')) === 0) {
                Log::info("ProgressImport: Skip baris kosong", ['row' => $rowIndex + 2]);
                continue;
            }

            // --- Mapping sesuai struktur Excel
            $kodePekerjaan       = trim($row[0] ?? '');
            $jenisPekerjaanUtama = trim($row[2] ?? ''); // bisa kosong
            $subPekerjaan        = trim($row[3] ?? '');
            $subSubPekerjaan     = trim($row[4] ?? '');
            $volume              = is_numeric($row[5] ?? null) ? (float)$row[5] : 0;
            $sat                 = trim($row[6] ?? '');
            $bobotTotal          = $this->parsePercent($row[7] ?? 0);

            // skip baris subtotal/total
            if (
                empty($kodePekerjaan) ||
                preg_match('/^(Jumlah|Total)/i', $kodePekerjaan) ||
                preg_match('/^(Jumlah|Total)/i', $subPekerjaan)
            ) {
                Log::info("ProgressImport: Skip subtotal/total", ['row' => $rowIndex + 2]);
                continue;
            }

            // --- Cari parent_id
            $parentId = null;
            if (str_contains($kodePekerjaan, '.')) {
                $parentKode = substr($kodePekerjaan, 0, strrpos($kodePekerjaan, '.'));
                $parentId   = $this->itemMap[$parentKode] ?? null;
            }

            // --- Simpan/Update pekerjaan item
            $item = PekerjaanItem::updateOrCreate(
                [
                    'po_id'          => $this->poId,
                    'kode_pekerjaan' => $kodePekerjaan,
                ],
                [
                    'jenis_pekerjaan_utama' => $jenisPekerjaanUtama,
                    'sub_pekerjaan'         => $subPekerjaan,
                    'sub_sub_pekerjaan'     => $subSubPekerjaan,
                    'volume'                => $volume,
                    'sat'                   => $sat,
                    'bobot'                 => $bobotTotal,
                    'parent_id'             => $parentId,
                ]
            );

            $this->itemMap[$kodePekerjaan] = $item->id;
            Log::info("ProgressImport: PekerjaanItem dibuat/diupdate", ['kode' => $kodePekerjaan]);

            // --- Buat/Update progress untuk item ini
            $progressItem = Progress::firstOrCreate([
                'po_id'             => $this->poId,
                'pekerjaan_item_id' => $item->id,
            ]);
            Log::info("ProgressImport: ProgressItem dibuat/ada", ['kode' => $kodePekerjaan]);

            // --- Loop tiap kolom minggu
            foreach ($rowArray as $colIndex => $value) {
                $headerName = $this->header[$colIndex] ?? null;
                if ($headerName && preg_match('/^M(\d+)$/i', $headerName)) {
                    $mingguKode = strtoupper($headerName);
                    $bobotRencana = $this->parsePercent($value);

                    if ($bobotRencana !== null) {
                        $minggu = MasterMinggu::where('kode_minggu', $mingguKode)
                            ->where('progress_id', $progressUtama->id)
                            ->first();

                        if ($minggu) {
                            ProgressDetail::updateOrCreate(
                                [
                                    'progress_id' => $progressItem->id,
                                    'minggu_id'   => $minggu->id,
                                ],
                                [
                                    'bobot_rencana'    => $bobotRencana,
                                    'volume_realisasi' => 0,
                                    'bobot_realisasi'  => 0,
                                    'keterangan'       => null,
                                ]
                            );
                            Log::info("ProgressImport: ProgressDetail dibuat/diupdate", [
                                'kode' => $kodePekerjaan,
                                'minggu' => $mingguKode,
                                'bobot_rencana' => $bobotRencana
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function parsePercent($val)
    {
        if ($val === null || $val === '') return 0;

        $val = str_replace('%', '', trim($val));

        return is_numeric($val) ? (float)$val : 0;
    }
}