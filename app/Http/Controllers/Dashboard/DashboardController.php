<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use App\Models\SettingAplikasi;
use App\Models\Pekerjaan;
use App\Models\Pr;
use App\Models\Po;
use App\Models\Gr;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $setting = SettingAplikasi::first();
    $tahun = $request->input('tahun', now()->year);

    $totalNilaiInvestasi = Po::whereHas('pr.pekerjaan', function($q) use ($tahun) {
            $q->where('tahun_usulan', $tahun);
        })
        ->sum('nilai_po');

    if ($totalNilaiInvestasi == 0) {
        $totalNilaiInvestasi = Pekerjaan::where('tahun_usulan', $tahun)
            ->sum('kebutuhan_dana');
    }

    $pekerjaans = Pekerjaan::with('masterInvestasi')
        ->where('tahun_usulan', $tahun)
        ->orderBy('id', 'desc')
        ->get();

    $jumlahProyek = $pekerjaans->count();
    $tahunLalu = $tahun - 1;

    $jumlahProyekTahunLalu = Pekerjaan::where('tahun_usulan', $tahunLalu)->count();
    
    $totalNilaiInvestasiTahunLalu = Po::whereHas('pr.pekerjaan', function($q) use ($tahunLalu) {
            $q->where('tahun_usulan', $tahunLalu);
        })
        ->sum('nilai_po');

    if ($totalNilaiInvestasiTahunLalu == 0) {
        $totalNilaiInvestasiTahunLalu = Pekerjaan::where('tahun_usulan', $tahunLalu)
            ->sum('kebutuhan_dana');
    }

    if ($jumlahProyekTahunLalu > 0) {
        $selisihProyek = $jumlahProyek - $jumlahProyekTahunLalu;
        $growthProyek = (($jumlahProyek - $jumlahProyekTahunLalu) / $jumlahProyekTahunLalu) * 100;
    } else {
        $selisihProyek = $jumlahProyek;
        $growthProyek = 0;
    }

    if ($totalNilaiInvestasiTahunLalu > 0) {
        $selisihNilaiInvestasi = $totalNilaiInvestasi - $totalNilaiInvestasiTahunLalu;
        $growthNilaiInvestasi = (($totalNilaiInvestasi - $totalNilaiInvestasiTahunLalu) / $totalNilaiInvestasiTahunLalu) * 100;
    } else {
        $selisihNilaiInvestasi = $totalNilaiInvestasi;
        $growthNilaiInvestasi = 0;
    }

    $dataPR = Pr::whereHas('pekerjaan', function($q) use ($tahun) {
            $q->where('tahun_usulan', $tahun);
        })
        ->select(
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('SUM(nilai_pr) as total_nilai')
        )
        ->first();

    $totalNilaiPR = $dataPR->total_nilai ?? 0;
    $jumlahPR = $dataPR->jumlah ?? 0;

    $dataPO = Po::whereHas('pr.pekerjaan', function($q) use ($tahun) {
            $q->where('tahun_usulan', $tahun);
        })
        ->select(
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('SUM(nilai_po) as total_nilai')
        )
        ->first();

    $totalNilaiPO = $dataPO->total_nilai ?? 0;
    $jumlahPO = $dataPO->jumlah ?? 0;

    $dataGR = Gr::whereHas('pr.pekerjaan', function($q) use ($tahun) {
            $q->where('tahun_usulan', $tahun);
        })
        ->select(
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('SUM(nilai_gr) as total_nilai')
        )
        ->first();

    $totalNilaiGR = $dataGR->total_nilai ?? 0;
    $jumlahGR = $dataGR->jumlah ?? 0;

    $dataPayment = Payment::whereHas('gr.pr.pekerjaan', function($q) use ($tahun) {
            $q->where('tahun_usulan', $tahun);
        })
        ->select(
            DB::raw('COUNT(*) as jumlah'),
            DB::raw('SUM(nilai_payment) as total_nilai')
        )
        ->first();

    $totalNilaiPayment = $dataPayment->total_nilai ?? 0;
    $jumlahPayment = $dataPayment->jumlah ?? 0;

    $persentasePR = $totalNilaiPO > 0 
        ? min(100, ($totalNilaiPR / $totalNilaiPO) * 100)
        : 0;

    $persentasePO = 100;

    $persentaseGR = $totalNilaiPO > 0 
        ? min(100, ($totalNilaiGR / $totalNilaiPO) * 100)
        : 0;

    $persentasePayment = $totalNilaiPO > 0 
        ? min(100, ($totalNilaiPayment / $totalNilaiPO) * 100)
        : 0;

    $chartData = [
        'labels' => ['PR', 'PO', 'GR', 'Payment'],
        'data' => [
            $totalNilaiPR / 1000000000,
            $totalNilaiPO / 1000000000,
            $totalNilaiGR / 1000000000,
            $totalNilaiPayment / 1000000000,
        ],
        'jumlah' => [
            $jumlahPR,
            $jumlahPO,
            $jumlahGR,
            $jumlahPayment,
        ]
    ];

    return view('Dashboard.index', compact(
        'setting',
        'tahun',
        'jumlahProyek',
        'totalNilaiInvestasi',
        'pekerjaans',
        'totalNilaiPR',
        'persentasePR',
        'totalNilaiPO',
        'persentasePO',
        'totalNilaiGR',
        'persentaseGR',
        'totalNilaiPayment',
        'persentasePayment',
        'chartData',
        'growthNilaiInvestasi',
        'selisihNilaiInvestasi',
        'growthProyek',
        'selisihProyek',
        'tahunLalu'
    ));
}

    public function kota($id)
    {
        $wilayah = Wilayah::with('pekerjaans')->findOrFail($id);
        $setting = SettingAplikasi::first();
        return view('Dashboard.kota', compact('wilayah', 'setting'));
    }
}