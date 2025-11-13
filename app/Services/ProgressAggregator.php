<?php

namespace App\Services;

use App\Models\Po;
use App\Models\PekerjaanItem;
use App\Models\DailyProgress;
use App\Models\Progress;
use App\Models\ProgressDetail;
use App\Models\MasterMinggu;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgressAggregator
{
    /**
     * Agregasi progress harian ke mingguan
     * Dipanggil setiap kali ada input/update DailyProgress
     */
    public function aggregateToWeekly(Po $po)
    {
        // Ambil progress utama
        $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
        
        if (!$progressUtama) {
            throw new \Exception('Progress utama belum dibuat. Silakan buat BA/PCM terlebih dahulu.');
        }

        // Ambil semua master minggu
        $masterMinggu = MasterMinggu::where('progress_id', $progressUtama->id)
            ->orderBy('tanggal_awal')
            ->get();

        if ($masterMinggu->isEmpty()) {
            throw new \Exception('Master minggu belum dibuat.');
        }

        // Ambil semua item pekerjaan
        $items = PekerjaanItem::where('po_id', $po->id)->get();

        foreach ($items as $item) {
            // Ambil/buat progress untuk item ini
            $progressItem = Progress::firstOrCreate([
                'po_id' => $po->id,
                'pekerjaan_item_id' => $item->id,
            ]);

            // Loop setiap minggu
            foreach ($masterMinggu as $minggu) {
                // Hitung total volume realisasi dari daily progress
                $totalVolumeRealisasi = DailyProgress::where('po_id', $po->id)
                    ->where('pekerjaan_item_id', $item->id)
                    ->whereBetween('tanggal', [$minggu->tanggal_awal, $minggu->tanggal_akhir])
                    ->sum('volume_realisasi');

                // Ambil detail progress untuk minggu ini
                $detail = ProgressDetail::firstOrNew([
                    'progress_id' => $progressItem->id,
                    'minggu_id' => $minggu->id,
                ]);

                // Hitung bobot realisasi
                // Rumus: (volume_realisasi / volume_total_item) * bobot_rencana
                $volumeTotal = (float) $item->volume;
                $bobotRencana = (float) $detail->bobot_rencana;
                
                $bobotRealisasi = 0;
                if ($volumeTotal > 0 && $bobotRencana > 0) {
                    $bobotRealisasi = ($totalVolumeRealisasi / $volumeTotal) * $bobotRencana;
                    
                    // Maksimal sesuai bobot rencana
                    $bobotRealisasi = min($bobotRealisasi, $bobotRencana);
                }

                // Update detail
                $detail->volume_realisasi = $totalVolumeRealisasi;
                $detail->bobot_realisasi = round($bobotRealisasi, 2);
                $detail->save();
            }
        }

        return true;
    }

    /**
     * Get weekly progress summary
     */
    public function getWeeklySummary(Po $po)
    {
        $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
        
        if (!$progressUtama) {
            return [];
        }

        $masterMinggu = MasterMinggu::where('progress_id', $progressUtama->id)
            ->orderBy('tanggal_awal')
            ->get();

        $summary = [];

        foreach ($masterMinggu as $minggu) {
            $rencana = 0;
            $realisasi = 0;

            foreach ($po->progresses as $progress) {
                if (!$progress->pekerjaan_item_id) continue;

                $detail = $progress->details->firstWhere('minggu_id', $minggu->id);
                if ($detail) {
                    $rencana += (float) $detail->bobot_rencana;
                    $realisasi += (float) $detail->bobot_realisasi;
                }
            }

            $summary[] = [
                'minggu' => $minggu->kode_minggu,
                'tanggal_awal' => $minggu->tanggal_awal,
                'tanggal_akhir' => $minggu->tanggal_akhir,
                'rencana' => round($rencana, 2),
                'realisasi' => round($realisasi, 2),
                'deviasi' => round($realisasi - $rencana, 2),
            ];
        }

        return $summary;
    }

    /**
     * Get cumulative progress
     */
    public function getCumulativeProgress(Po $po)
    {
        $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
        
        if (!$progressUtama) {
            return [
                'rencana' => 0,
                'realisasi' => 0,
                'deviasi' => 0,
            ];
        }

        $totalRencana = 0;
        $totalRealisasi = 0;

        foreach ($po->progresses as $progress) {
            if (!$progress->pekerjaan_item_id) continue;

            foreach ($progress->details as $detail) {
                $totalRencana += (float) $detail->bobot_rencana;
                $totalRealisasi += (float) $detail->bobot_realisasi;
            }
        }

        return [
            'rencana' => round($totalRencana, 2),
            'realisasi' => round($totalRealisasi, 2),
            'deviasi' => round($totalRealisasi - $totalRencana, 2),
        ];
    }
}