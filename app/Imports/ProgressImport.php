<?php

namespace App\Imports;

use App\Models\Progress;
use App\Models\ProgressDetail;
use App\Models\MasterMinggu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProgressImport implements ToCollection
{
    protected $poId;
    protected $progressMap = [];

    public function __construct($poId)
    {
        $this->poId = $poId;
    }

    public function collection(Collection $rows)
    {
        // Skip header (baris pertama)
        $rows->shift();

        foreach ($rows as $row) {
            $kodePekerjaan      = trim($row[0] ?? null); // kode_pekerjaan
            $no                 = trim($row[1] ?? null); // no → skip
            $jenisPekerjaanUtama = trim($row[2] ?? null);
            $subPekerjaan       = trim($row[3] ?? null);
            $subSubPekerjaan    = trim($row[4] ?? null);
            $volume             = trim($row[5] ?? null);
            $sat               = trim($row[6] ?? null);
            $hargaSatuan        = trim($row[7] ?? null);
            $jumlahHarga        = trim($row[8] ?? null);
            $bobotTotal         = trim($row[9] ?? null);
            $mingguKode         = trim($row[10] ?? null);
            $bobotMingguan      = trim($row[11] ?? null);

            if (!$kodePekerjaan) {
                continue; // skip baris kosong
            }

            // Cari parent_id (misal P1.1 → parentnya P1)
            $parentId = null;
            if (str_contains($kodePekerjaan, '.')) {
                $parentKode = substr($kodePekerjaan, 0, strrpos($kodePekerjaan, '.'));
                $parentId = $this->progressMap[$parentKode] ?? null;
            }

            // Simpan progress
            $progress = Progress::create([
                'po_id'             => $this->poId,
                'kode_pekerjaan'      => $kodePekerjaan,
                'jenis_pekerjaan_utama' => $jenisPekerjaanUtama,
                'sub_pekerjaan'       => $subPekerjaan,
                'sub_sub_pekerjaan'     => $subSubPekerjaan,
                'volume'              => $volume ?: 0,
                'sat'                 => $sat,
                'harga_satuan'        => $hargaSatuan ?: 0,
                'jumlah_harga'        => $jumlahHarga ?: 0,
                'bobot_total'         => $bobotTotal ?: 0,
                'parent_id'           => $parentId,
            ]);

            // Simpan mapping buat child berikutnya
            $this->progressMap[$kodePekerjaan] = $progress->id;

            // Cari minggu_id dari master_minggu
            if ($mingguKode) {
                $minggu = MasterMinggu::where('kode_minggu', $mingguKode)->first();
                if ($minggu) {
                    ProgressDetail::create([
                        'progress_id'     => $progress->id,
                        'minggu_id'       => $minggu->id,
                        'bobot_rencana'   => $bobotMingguan ?: 0,
                        'bobot_realisasi' => 0,
                        'keterangan'      => null,
                    ]);
                }
            }

            // Debug: lihat progress yang baru saja dibuat
            dd($progress->toArray());
        }
    }
}