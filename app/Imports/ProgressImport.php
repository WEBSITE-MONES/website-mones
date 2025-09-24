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
            throw new \Exception("File Excel kosong.");
        }

        // --- Ambil header baris pertama
        $this->header = $rows->shift()->toArray();

        // --- Cari kolom minggu (M1, M2, dst.)
        $mingguColumns = collect($this->header)
            ->filter(fn($col) => preg_match('/^M\d+$/i', $col))
            ->values();

        // --- Ambil progress utama PO
        $po = Po::find($this->poId);
        if (!$po) throw new \Exception("PO tidak ditemukan.");

        $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
        if (!$progressUtama || empty($progressUtama->tanggal_ba_mulai_kerja)) {
            throw new \Exception("Progress utama belum dibuat atau tanggal BA belum diisi.");
        }

        // --- Pastikan minggu dari Excel ada di MasterMinggu
        foreach ($mingguColumns as $col) {
            $mingguKode = strtoupper($col);

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
            }
        }

        // --- Loop isi baris pekerjaan
        foreach ($rows as $row) {
            $rowArray = $row->toArray();

            // skip baris kosong
            if (count(array_filter($rowArray, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $kodePekerjaan       = trim($row[0] ?? '');
            $jenisPekerjaanUtama = trim($row[2] ?? '');
            $subPekerjaan        = trim($row[3] ?? '');
            $subSubPekerjaan     = trim($row[4] ?? '');
            $volume              = is_numeric($row[5] ?? null) ? (float)$row[5] : 0;
            $sat                 = trim($row[6] ?? '');
            $hargaSatuan         = is_numeric($row[7] ?? null) ? (float)$row[7] : 0;
            $jumlahHargaExcel    = is_numeric($row[8] ?? null) ? (float)$row[8] : null;
            $bobotTotal          = is_numeric($row[9] ?? null) ? (float)$row[9] : 0;
            $volumeRealisasi     = is_numeric($row[10] ?? null) ? (float)$row[10] : 0;

            if ($kodePekerjaan === '') continue;

            // --- Cari parent_id kalau ada kode bercabang
            $parentId = null;
            if (str_contains($kodePekerjaan, '.')) {
                $parentKode = substr($kodePekerjaan, 0, strrpos($kodePekerjaan, '.'));
                $parentId   = $this->itemMap[$parentKode] ?? null;
            }

            // --- Hitung jumlah harga
            $jumlahHargaFinal = $jumlahHargaExcel ?? ($volume * $hargaSatuan);

            // --- Simpan/Update pekerjaan
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
                    'harga_satuan'          => $hargaSatuan,
                    'jumlah_harga'          => $jumlahHargaFinal,
                    'bobot'                 => $bobotTotal,
                    'parent_id'             => $parentId,
                ]
            );

            $this->itemMap[$kodePekerjaan] = $item->id;

            // --- Buat progress item
            $progressItem = Progress::firstOrCreate([
                'po_id'             => $this->poId,
                'pekerjaan_item_id' => $item->id,
            ]);

            // --- Loop tiap kolom untuk mapping ke minggu
            foreach ($rowArray as $colIndex => $value) {
                $headerName = $this->header[$colIndex] ?? null;
                if ($headerName && preg_match('/^M(\d+)$/i', $headerName)) {
                    $mingguKode = strtoupper($headerName);
                    $bobotRencana = is_numeric($value) ? (float)$value : null;

                    if ($bobotRencana !== null) {
                        $minggu = MasterMinggu::where('kode_minggu', $mingguKode)
                            ->where('progress_id', $progressUtama->id)
                            ->first();

                        if ($minggu) {
                            // --- Hitung bobot realisasi
                            $bobotRealisasi = 0;
                            if ($volume > 0 && $volumeRealisasi > 0) {
                                $bobotRealisasi = ($volumeRealisasi / $volume) * $bobotRencana;
                            }

                            ProgressDetail::updateOrCreate(
                                [
                                    'progress_id' => $progressItem->id,
                                    'minggu_id'   => $minggu->id,
                                ],
                                [
                                    'bobot_rencana'    => $bobotRencana,
                                    'volume_realisasi' => $volumeRealisasi,
                                    'bobot_realisasi'  => $bobotRealisasi,
                                    'keterangan'       => null,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}