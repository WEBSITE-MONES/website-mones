<?php

namespace App\Helpers;

use Carbon\Carbon;

class MingguHelper
{
    public static function generateMinggu(Carbon $startDate, $jumlahMinggu = 20)
    {
        $mingguList = collect();
        for ($i = 0; $i < $jumlahMinggu; $i++) {
            $awal = $startDate->copy()->addWeeks($i);
            $akhir = $awal->copy()->endOfWeek();
            $mingguList->push((object)[
                'kode_minggu'   => 'M' . ($i + 1),
                'tanggal_awal'  => $awal,
                'tanggal_akhir' => $akhir,
            ]);
        }
        return $mingguList;
    }
}