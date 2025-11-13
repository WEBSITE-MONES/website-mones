<?php

namespace App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Storage;


use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Pekerjaan;
use App\Models\MasterMinggu;
use App\Models\Pr;
use App\Models\Gambar;
use App\Models\Laporan;
use App\Models\SubPekerjaan;
use App\Models\Kontrak;
use App\Models\Korespondensi;
use App\Models\DokumenUsulan;
use Illuminate\Http\Request;
use App\Models\DailyProgress;
use App\Services\ProgressAggregator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;


class PekerjaanDetailController extends Controller
{
    public function index($id)
    {
        $pekerjaan = Pekerjaan::with('wilayah')->findOrFail($id);

        return view('Dashboard.Pekerjaan.detail', compact('pekerjaan'));
    }

    // menampilkan daftar sub-pekerjaan untuk pekerjaan {id}
    public function subPekerjaan($pekerjaan_id)
    {
        $pekerjaan = Pekerjaan::with('subPekerjaan')->findOrFail($pekerjaan_id);

        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.list_sub_pekerjaan', compact('pekerjaan'));
    }

    // Progress Fisik untuk SUB Pekerjaan
    public function progresFisikSub($pekerjaan_id, $sub_id)
    {
        try {
            $pekerjaan = Pekerjaan::findOrFail($pekerjaan_id);
            $sub = SubPekerjaan::findOrFail($sub_id);

            // cari PR yang terkait sub_pekerjaan_id
            $pr = Pr::where('sub_pekerjaan_id', $sub->id)->first();

            if (!$pr) {
                return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik_no_pr', compact('pekerjaan', 'sub'));
            }

            $po = $pr->po;
            if (!$po) {
                Log::warning("PO tidak ditemukan untuk PR ID: {$pr->id}");
                return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik_no_po', compact('pekerjaan', 'pr', 'sub'));
            }

            $progresses = $po->progresses()
                            ->with(['details', 'pekerjaanItem'])
                            ->get();

            if ($progresses->isEmpty()) {
                return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik_empty', compact('pekerjaan', 'pr', 'po', 'sub'));
            }

            $masterMinggu = MasterMinggu::where('po_id', $po->id)
                                        ->orderBy('minggu_ke')
                                        ->get();

            if ($masterMinggu->isEmpty()) {
                return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik_empty', compact('pekerjaan', 'pr', 'po', 'sub'));
            }

            // --- perhitungan chart sama seperti progresFisik biasa ---
            $chartLabels = [];
            $chartRencana = [];
            $chartRealisasi = [];
            $rencanaKumulatif = 0;
            $realisasiKumulatif = 0;

            foreach ($masterMinggu as $minggu) {
                $chartLabels[] = $minggu->kode_minggu;

                $totalRencana = $progresses->sum(function($p) use ($minggu) {
                    $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                    return (float) ($detail?->bobot_rencana ?? 0);
                });

                $totalRealisasi = $progresses->sum(function($p) use ($minggu) {
                    $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                    return (float) ($detail?->bobot_realisasi ?? 0);
                });

                $rencanaKumulatif += $totalRencana;
                $realisasiKumulatif += $totalRealisasi;

                $chartRencana[] = round($rencanaKumulatif, 2);
                $chartRealisasi[] = round($realisasiKumulatif, 2);
            }

            $rencanaPct = $rencanaKumulatif;
            $realisasiPct = $realisasiKumulatif;
            $deviasiPct = $realisasiPct - $rencanaPct;

            $monthlyData = collect();
            foreach ($masterMinggu->groupBy('bulan') as $bulan => $minggus) {
                $rencana = 0;
                $realisasi = 0;

                foreach ($minggus as $minggu) {
                    $rencana += $progresses->sum(function($p) use ($minggu) {
                        $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                        return (float) ($detail?->bobot_rencana ?? 0);
                    });

                    $realisasi += $progresses->sum(function($p) use ($minggu) {
                        $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                        return (float) ($detail?->bobot_realisasi ?? 0);
                    });
                }

                $monthlyData->push([
                    'bulan' => $bulan,
                    'bulan_label' => \Carbon\Carbon::parse($bulan . '-01')->format('M Y'),
                    'rencana' => $rencana,
                    'realisasi' => $realisasi,
                    'deviasi' => $realisasi - $rencana,
                ]);
            }

            $rencanaKumulatifBulanan = [];
            $realisasiKumulatifBulanan = [];
            $totalRencanaBulan = 0;
            $totalRealisasiBulan = 0;

            foreach ($monthlyData as $item) {
                $totalRencanaBulan += $item['rencana'];
                $totalRealisasiBulan += $item['realisasi'];

                $rencanaKumulatifBulanan[] = round($totalRencanaBulan, 2);
                $realisasiKumulatifBulanan[] = round($totalRealisasiBulan, 2);
            }

            $labels = $monthlyData->pluck('bulan_label');

            return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik', compact(
                'pekerjaan',
                'pr',
                'po',
                'progresses',
                'masterMinggu',
                'rencanaPct',
                'realisasiPct',
                'deviasiPct',
                'chartLabels',
                'chartRencana',
                'chartRealisasi',
                'monthlyData',
                'labels',
                'rencanaKumulatifBulanan',
                'realisasiKumulatifBulanan',
                // jika view butuh tahu sub
                'sub'
            ));
        } catch (\Exception $e) {
            Log::error('Error di progresFisikSub:', [
                'pekerjaan_id' => $pekerjaan_id ?? null,
                'sub_id' => $sub_id ?? null,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.progress_fisik_error', [
                'pekerjaan' => Pekerjaan::find($pekerjaan_id),
                'error' => $e->getMessage()
            ]);
        }
    }



    // Progress RKAP
    public function penyerapanRkap($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.rkap', compact('pekerjaan'));
    }

    public function pembayaran($id)
    {
        $pekerjaan = Pekerjaan::with([
        'subPekerjaan', 
        'prs' => function($query) {
            $query->orderBy('created_at', 'asc');
        },
        'prs.po.termins.payment',
        'prs.payments' => function($query) {
            $query->orderBy('tanggal_payment', 'asc');
        },
        'prs.payments.termins'
    ])->findOrFail($id);

        
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.progress.pembayaran', compact('pekerjaan'));
    }


    // Bagian DATA INVESTASI

    // LAPORAN 
    public function laporan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.laporan', compact('pekerjaan'));
    }

    // laporan approval
        public function laporanApproval($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);

        $laporans = Laporan::where('pekerjaan_id', $id)->latest()->get();

        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.laporan.laporan_approval', compact('pekerjaan', 'laporans'));
    }

    public function storeLaporanApproval(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'file_laporan' => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // Pastikan folder upload tersedia
        if (!File::exists(public_path('uploads/laporan'))) {
            File::makeDirectory(public_path('uploads/laporan'), 0777, true);
        }

        $file = $request->file('file_laporan');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/laporan'), $fileName);

        // Simpan ke database
        Laporan::create([
            'pekerjaan_id'   => $id,
            'keterangan'     => $request->keterangan,
            'file_laporan'   => $fileName, // ✅ ini disesuaikan dengan kolom di tabel
            'tanggal_upload' => now()->toDateString(),
            'status'         => 'Menunggu',
        ]);

        return back()->with('success', 'Laporan Approval berhasil diunggah!');
    }


    public function destroyLaporanApproval($id)
    {
        $laporan = Laporan::findOrFail($id);

        // hapus file dari folder public/uploads/laporan_approval
        $filePath = public_path('uploads/laporan/' . $laporan->file);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // hapus data dari database
        $laporan->delete();

        return back()->with('success', 'Laporan approval berhasil dihapus!');
    }


    public function approveLaporanApproval($id, $laporan)
    {
        $laporan = Laporan::findOrFail($laporan);
        $laporan->update(['status' => 'Disetujui']);

        return back()->with('success', 'Laporan berhasil disetujui!');
    }

    public function rejectLaporanApproval($id, $laporan)
    {
        $laporan = Laporan::findOrFail($laporan);
        $laporan->update(['status' => 'Ditolak']);

        return back()->with('warning', 'Laporan telah ditolak!');
    }


    // end laporan approval
    public function laporanQaQc($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $laporanQa = [];
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.laporan.laporan_qa', compact('pekerjaan', 'laporanQa'));
    }

    // LAPORAN DOKUMENTASI APPROVE
    protected $progressAggregator;

public function __construct(ProgressAggregator $progressAggregator)
{
    $this->progressAggregator = $progressAggregator;
}

/**
 * Laporan Dokumentasi - Daily Progress dengan Approval
 */
public function laporanDokumentasi($id)
{
    $pekerjaan = Pekerjaan::findOrFail($id);
    
    // Ambil daily progress yang terkait dengan pekerjaan ini
    $laporanDokumentasi = DailyProgress::whereHas('po.pr.pekerjaan', function($query) use ($id) {
        $query->where('id', $id);
    })
    ->with(['po', 'pekerjaanItem', 'pelapor', 'approver'])
    ->orderBy('tanggal', 'desc')
    ->paginate(20);
    
    // Summary counts
    $summary = [
        'total' => DailyProgress::whereHas('po.pr.pekerjaan', function($q) use ($id) {
            $q->where('id', $id);
        })->count(),
        'pending' => DailyProgress::whereHas('po.pr.pekerjaan', function($q) use ($id) {
            $q->where('id', $id);
        })->pending()->count(),
        'approved' => DailyProgress::whereHas('po.pr.pekerjaan', function($q) use ($id) {
            $q->where('id', $id);
        })->approved()->count(),
        'rejected' => DailyProgress::whereHas('po.pr.pekerjaan', function($q) use ($id) {
            $q->where('id', $id);
        })->rejected()->count(),
    ];
    
    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.laporan.laporan_dokumentasi', compact('pekerjaan', 'laporanDokumentasi', 'summary'));
}

/**
 * Show Detail Daily Progress
 */
public function showDokumentasi($id, $dailyProgressId)
{
    $pekerjaan = Pekerjaan::findOrFail($id);
    $report = DailyProgress::with(['po', 'pekerjaanItem', 'pelapor', 'approver'])
        ->findOrFail($dailyProgressId);
    
    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.laporan.show_dokumentasi', compact('pekerjaan', 'report'));
}

/**
 * Approve Daily Progress
 */
public function approveDokumentasi($id, $dailyProgressId)
{
    try {
        DB::beginTransaction();
        
        $report = DailyProgress::findOrFail($dailyProgressId);
        
        if ($report->status_approval === 'approved') {
            return redirect()->back()->with('info', 'Laporan sudah di-approve sebelumnya.');
        }
        
        $report->update([
            'status_approval' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
        
        // Agregasi ke progress mingguan
        $this->progressAggregator->aggregateToWeekly($report->po);
        
        DB::commit();
        
        Log::info('Daily Progress Approved', [
            'report_id' => $dailyProgressId,
            'approved_by' => Auth::user()->name
        ]);
        
        return redirect()->back()->with('success', 'Dokumentasi berhasil di-approve dan progress mingguan telah diperbarui!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Approve Daily Progress Error', [
            'report_id' => $dailyProgressId,
            'error' => $e->getMessage()
        ]);
        
        return redirect()->back()->with('error', 'Gagal meng-approve dokumentasi: ' . $e->getMessage());
    }
}

/**
 * Reject Daily Progress
 */
public function rejectDokumentasi(Request $request, $id, $dailyProgressId)
{
    $request->validate([
        'rejection_reason' => 'required|string|min:10|max:500',
    ], [
        'rejection_reason.required' => 'Alasan penolakan wajib diisi',
        'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
    ]);
    
    try {
        $report = DailyProgress::findOrFail($dailyProgressId);
        
        $report->update([
            'status_approval' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        Log::info('Daily Progress Rejected', [
            'report_id' => $dailyProgressId,
            'rejected_by' => Auth::user()->name,
            'reason' => $request->rejection_reason
        ]);
        
        return redirect()->back()->with('warning', 'Dokumentasi telah ditolak.');
        
    } catch (\Exception $e) {
        Log::error('Reject Daily Progress Error', [
            'report_id' => $dailyProgressId,
            'error' => $e->getMessage()
        ]);
        
        return redirect()->back()->with('error', 'Gagal menolak dokumentasi: ' . $e->getMessage());
    }
}

/**
 * Revise - kembalikan ke pending
 */
public function reviseDokumentasi($id, $dailyProgressId)
{
    try {
        DB::beginTransaction();
        
        $report = DailyProgress::findOrFail($dailyProgressId);
        $oldStatus = $report->status_approval;
        
        $report->update([
            'status_approval' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
        
        if ($oldStatus === 'approved') {
            $this->progressAggregator->aggregateToWeekly($report->po);
        }
        
        DB::commit();
        
        Log::info('Daily Progress Revised', [
            'report_id' => $dailyProgressId,
            'revised_by' => Auth::user()->name,
            'old_status' => $oldStatus
        ]);
        
        return redirect()->back()->with('success', 'Dokumentasi dikembalikan ke status pending.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Revise Daily Progress Error', [
            'report_id' => $dailyProgressId,
            'error' => $e->getMessage()
        ]);
        
        return redirect()->back()->with('error', 'Gagal merevisi dokumentasi: ' . $e->getMessage());
    }
}

/**
 * Delete Daily Progress
 */
public function destroyDokumentasi($id, $dailyProgressId)
{
    try {
        DB::beginTransaction();
        
        $report = DailyProgress::findOrFail($dailyProgressId);
        $po = $report->po;
        $wasApproved = $report->status_approval === 'approved';
        
        // Hapus foto
        if (!empty($report->foto)) {
            foreach ($report->foto as $foto) {
                if (isset($foto['filename'])) {
                    Storage::disk('public')->delete('progress-photos/' . $foto['filename']);
                }
            }
        }
        
        $report->delete();
        
        if ($wasApproved) {
            $this->progressAggregator->aggregateToWeekly($po);
        }
        
        DB::commit();
        
        Log::info('Daily Progress Deleted by Admin', [
            'report_id' => $dailyProgressId,
            'deleted_by' => Auth::user()->name
        ]);
        
        return redirect()->back()->with('success', 'Dokumentasi berhasil dihapus!');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Delete Daily Progress Error', [
            'report_id' => $dailyProgressId,
            'error' => $e->getMessage()
        ]);
        
        return redirect()->back()->with('error', 'Gagal menghapus dokumentasi: ' . $e->getMessage());
    }
}

    // END LAPORAN DOKUMENTASI APPROVE
    // end laporan

    // start kontrak
    public function kontrak($id)
{
    $pekerjaan = Pekerjaan::findOrFail($id);
    $kontraks = $pekerjaan->kontraks; // relasi dari model Pekerjaan

    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.kontrak', compact('pekerjaan', 'kontraks'));
}


    public function storeKontrak(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'tanggal_kontrak' => 'required|date',
            'file_kontrak' => 'nullable|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file_kontrak')) {
            $filePath = $request->file('file_kontrak')->store('kontrak_files', 'public');
        }

        Kontrak::create([
            'pekerjaan_id' => $id,
            'keterangan' => $request->keterangan,
            'tanggal_kontrak' => $request->tanggal_kontrak,
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Kontrak berhasil ditambahkan!');
    }

        public function destroyKontrak(Kontrak $kontrak)
    {
        if ($kontrak->file_path && Storage::disk('public')->exists($kontrak->file_path)) {
            Storage::disk('public')->delete($kontrak->file_path);
        }
        $kontrak->delete();
        return back()->with('success', 'Kontrak berhasil dihapus!');
    }

    public function updateKontrak(Request $request, $id, Kontrak $kontrak)
{
    $request->validate([
        'keterangan' => 'required|string|max:255',
        'tanggal_kontrak' => 'required|date',
        'file_kontrak' => 'nullable|mimes:pdf,doc,docx,jpg,png|max:2048',
    ]);

    // hapus file lama jika user upload file baru
    if ($request->hasFile('file_kontrak')) {
        if ($kontrak->file_path && Storage::disk('public')->exists($kontrak->file_path)) {
            Storage::disk('public')->delete($kontrak->file_path);
        }
        $filePath = $request->file('file_kontrak')->store('kontrak_files', 'public');
        $kontrak->file_path = $filePath;
    }

    $kontrak->update([
        'keterangan' => $request->keterangan,
        'tanggal_kontrak' => $request->tanggal_kontrak,
    ]);

    return back()->with('success', 'Data kontrak berhasil diperbarui!');
}


    // end kontrak

    // start gambar

    public function gambar($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.gambar', compact('pekerjaan'));
    }

    public function storeGambar(Request $request, $id)
{
    $request->validate([
        'keterangan' => 'required|string',
        'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'tanggal_upload' => 'nullable|date',
    ]);

    $file = $request->file('file');
    $fileName = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path('uploads/gambar'), $fileName);

    Gambar::create([
        'pekerjaan_id' => $id,
        'keterangan' => $request->keterangan,
        'file' => $fileName,
        'status' => 'Pending',
        'tanggal_upload' => $request->tanggal_upload ?? now()->toDateString(), // otomatis isi hari ini kalau kosong
    ]);

    return back()->with('success', 'Gambar berhasil diupload!');
}

    public function approveGambar($id, $gambar_id)
    {
        $gambar = Gambar::findOrFail($gambar_id);
        $gambar->update(['status' => 'Approved']);

        return back()->with('success', 'Gambar berhasil disetujui!');
    }

    public function rejectGambar($id, $gambar_id)
    {
        $gambar = Gambar::findOrFail($gambar_id);
        $gambar->update(['status' => 'Rejected']);

        return back()->with('success', 'Gambar telah ditolak.');
    }

    public function destroyGambar($id, $gambar_id)
    {
        $gambar = Gambar::findOrFail($gambar_id);

        $path = public_path('uploads/gambar/' . $gambar->file);
        if (File::exists($path)) {
            File::delete($path);
        }

        $gambar->delete();

        return back()->with('success', 'Gambar berhasil dihapus!');
    }

    // end gambar

    // start korespondensi

    public function korespondensi($id)
{
    $pekerjaan = Pekerjaan::with('korespondensis')->findOrFail($id);
    return view('Dashboard.Pekerjaan.Pekerjaan_Detail.data.korespondensi', compact('pekerjaan'));
}

    // STORE
    public function storeKorespondensi(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|string',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'file_korespondensi' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file_korespondensi')) {
            $filePath = $request->file('file_korespondensi')->store('korespondensi', 'public');
        }

        Korespondensi::create([
            'pekerjaan_id' => $id,
            'jenis' => $request->jenis,
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Korespondensi berhasil ditambahkan.');
    }

    // UPDATE
    public function updateKorespondensi(Request $request, $id, Korespondensi $korespondensi)
    {
        $request->validate([
            'jenis' => 'required|string',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'file_korespondensi' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        if ($request->hasFile('file_korespondensi')) {
            if ($korespondensi->file_path && Storage::disk('public')->exists($korespondensi->file_path)) {
                Storage::disk('public')->delete($korespondensi->file_path);
            }
            $korespondensi->file_path = $request->file('file_korespondensi')->store('korespondensi', 'public');
        }

        $korespondensi->update([
            'jenis' => $request->jenis,
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'file_path' => $korespondensi->file_path,
        ]);

        return back()->with('success', 'Korespondensi berhasil diperbarui.');
    }

    // DESTROY
    public function destroyKorespondensi($id, Korespondensi $korespondensi)
    {
        if ($korespondensi->file_path && Storage::disk('public')->exists($korespondensi->file_path)) {
            Storage::disk('public')->delete($korespondensi->file_path);
        }

        $korespondensi->delete();

        return back()->with('success', 'Korespondensi berhasil dihapus.');
    }

    // end korespondensi

    // start dokumen investasi

    
    // end dokumen investasi


    // Bagian STATUS INVESTASI
    public function perencanaan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.perencanaan', compact('pekerjaan'));
    }

    public function pelelangan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.pelelangan', compact('pekerjaan'));
    }

    public function pelaksanaan($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.pelaksanaan', compact('pekerjaan'));
    }

    public function selesai($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        return view('Dashboard.Pekerjaan.Pekerjaan_Detail.status.selesai', compact('pekerjaan'));
    }
}