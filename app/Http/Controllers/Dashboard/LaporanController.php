<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\LaporanInvestasi;
use App\Models\LaporanApproval;
use App\Models\LaporanDetail;
use App\Models\User;
use App\Models\LaporanApprovalSetting;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon; 

class LaporanController extends Controller
{
    /**
     * Display listing of laporan with filters
     */
    public function index(Request $request)
{
    $jenis = $request->get('jenis', 'rekap_activa'); 
    $tahun = $request->get('tahun', date('Y'));

    $laporan = LaporanInvestasi::where('jenis_laporan', $jenis)
        ->where('tahun', $tahun)
        ->with(['approvals', 'pembuatLaporan'])
        ->orderBy('bulan', 'desc')
        ->get();

    return view('Dashboard.Pekerjaan.Realisasi.Laporan.investasi', compact('laporan', 'jenis', 'tahun'));
}

    /**
     * Show detail laporan - FIXED VERSION
     */
    public function show($id)
    {
        try {
            // Load dengan eager loading yang benar
            $laporan = LaporanInvestasi::with([
                'approvals' => function($query) {
                    $query->orderBy('urutan');
                },
                'approvals.user',
                'details',
                'pembuatLaporan'
            ])->findOrFail($id);
            
            // Generate data laporan jika belum ada details
            if ($laporan->details->isEmpty()) {
                $data = $this->generateLaporanData($laporan->tahun, $laporan->bulan);
                
                // Save details jika ada data
                if (!empty($data)) {
                    foreach ($data as $item) {
                        $detailData = [
                            'laporan_id' => $laporan->id,
                            'coa' => $item->coa ?? '-',
                            'nomor_prodef_sap' => $item->nomor_prodef_sap ?? '-',
                            'nama_investasi' => $item->nama_investasi ?? '-',
                            'uraian_pekerjaan' => $item->uraian_pekerjaan ?? '-',
                            'total_volume' => $item->total_volume ?? 0,
                            'nilai_rkap' => $item->nilai_rkap ?? 0,
                            'target_sd_bulan' => $item->target_sd_bulan ?? 0,
                            'nomor_po' => $item->nomor_po ?? '-',
                            'tanggal_po' => $item->tanggal_po ?? null,
                            'pelaksana' => $item->pelaksana ?? '-',
                            'waktu_pelaksanaan' => $item->waktu_pelaksanaan ?? '-',
                            'estimated' => $item->estimated ?? '-',
                            'mulai_kontrak' => $item->mulai_kontrak ?? null,
                            'selesai_kontrak' => $item->selesai_kontrak ?? null,
                            'realisasi_fisik' => $item->realisasi_fisik ?? 0,
                            'realisasi_pembayaran' => $item->realisasi_pembayaran ?? 0,
                        ];
                        
                        LaporanDetail::create($detailData);
                    }
                    
                    // Refresh details
                    $laporan->load('details');
                }
            }

            // Parse tanggal_po untuk setiap detail
            foreach ($laporan->details as $detail) {
                if ($detail->tanggal_po && is_string($detail->tanggal_po)) {
                    try {
                        $detail->tanggal_po = Carbon::parse($detail->tanggal_po);
                    } catch (\Exception $e) {
                        $detail->tanggal_po = null;
                    }
                }
            }

            // Group data by COA untuk tampilan nested
            $groupedData = $this->groupDataByCOA($laporan->details);

            return view('Dashboard.Pekerjaan.Realisasi.Laporan.detail_laporan_investasi', compact('laporan', 'groupedData'));
            
        } catch (\Exception $e) {
            Log::error('Error showing laporan: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Gagal menampilkan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Show form create laporan
     */
    public function create(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        
        return view('Dashboard.Pekerjaan.Realisasi.Laporan.create_laporan', compact('tahun', 'bulan'));
    }

    /**
     * Store new laporan - IMPROVED WITH DETAILED LOGGING
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:rekap_activa,rekap_rincian',
            'tahun' => 'required|integer|min:2020|max:2100',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        DB::beginTransaction();
        try {
            Log::info('=== MULAI PROSES BUAT LAPORAN ===');
            Log::info('Request data:', $request->all());
            Log::info('User ID: ' . Auth::id());

            // Cek duplikasi
            $exists = LaporanInvestasi::where([
                'jenis_laporan' => $request->jenis_laporan,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
            ])->exists();
            
            if ($exists) {
                Log::warning('Laporan sudah ada untuk periode ini');
                return back()->with('error', 'Laporan untuk periode ini sudah ada!')->withInput();
            }

            // Generate kode laporan
            $kode = $this->generateKodeLaporan($request->tahun, $request->bulan);
            Log::info('Kode laporan generated: ' . $kode);
            
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            // Create laporan
            Log::info('Creating laporan record...');
            $laporan = LaporanInvestasi::create([
                'kode_laporan' => $kode,
                'jenis_laporan' => $request->jenis_laporan,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'periode_label' => "Laporan s.d {$namaBulan[$request->bulan]}",
                'status_approval' => 'draft',
                'dibuat_oleh' => Auth::id(),
                'tanggal_dibuat' => now(),
            ]);

            Log::info('Laporan created with ID: ' . $laporan->id);

            // Generate dan simpan data laporan
            Log::info('Mulai generate data laporan...');
            $data = $this->generateLaporanData($request->tahun, $request->bulan);
            
            Log::info('Data generated, jumlah records: ' . count($data));

            if (empty($data)) {
                DB::rollBack();
                Log::warning('Tidak ada data untuk periode yang dipilih');
                return back()->with('error', 'Tidak ada data untuk periode yang dipilih! Pastikan ada data pekerjaan dengan PR pada periode tersebut.')->withInput();
            }

            // Simpan details
            Log::info('Mulai simpan detail records...');
            $detailsSaved = 0;
            foreach ($data as $item) {
                try {
                    $detailData = [
                        'laporan_id' => $laporan->id,
                        'coa' => $item->coa ?? '-',
                        'nomor_prodef_sap' => $item->nomor_prodef_sap ?? '-',
                        'nama_investasi' => $item->nama_investasi ?? '-',
                        'uraian_pekerjaan' => $item->uraian_pekerjaan ?? '-',
                        'total_volume' => $item->total_volume ?? 0,
                        'nilai_rkap' => $item->nilai_rkap ?? 0,
                        'target_sd_bulan' => $item->target_sd_bulan ?? 0,
                        'nomor_po' => $item->nomor_po ?? '-',
                        'tanggal_po' => $item->tanggal_po ?? null,
                        'pelaksana' => $item->pelaksana ?? '-',
                        'waktu_pelaksanaan' => $item->waktu_pelaksanaan ?? '-',
                        'estimated' => $item->estimated ?? '-',
                        'mulai_kontrak' => $item->mulai_kontrak ?? null,
                        'selesai_kontrak' => $item->selesai_kontrak ?? null,
                        'realisasi_fisik' => $item->realisasi_fisik ?? 0,
                        'realisasi_pembayaran' => $item->realisasi_pembayaran ?? 0,
                    ];
                    
                    LaporanDetail::create($detailData);
                    $detailsSaved++;
                } catch (\Exception $e) {
                    Log::error('Error saving detail: ' . $e->getMessage());
                    Log::error('Detail data: ' . json_encode($detailData ?? []));
                }
            }

            Log::info("Berhasil simpan {$detailsSaved} detail records");

            // Create approval records
            Log::info('Mulai create approval records...');
            $this->createApprovalRecords($laporan->id);
            Log::info('Approval records created');

            DB::commit();
            Log::info('=== SELESAI PROSES BUAT LAPORAN ===');

            return redirect()->route('laporan.show', $laporan->id)
                ->with('success', 'Laporan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== ERROR BUAT LAPORAN ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->with('error', 'Gagal membuat laporan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * DELETE LAPORAN - NEW FEATURE
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $laporan = LaporanInvestasi::findOrFail($id);
            
            // Cek apakah sudah di-approve
            if ($laporan->status_approval === 'approved') {
                return back()->with('error', 'Laporan yang sudah di-approve tidak dapat dihapus!');
            }

            // Hapus details
            $laporan->details()->delete();
            
            // Hapus approvals
            $laporan->approvals()->delete();
            
            // Hapus laporan
            $laporan->delete();

            DB::commit();

            return redirect()->route('laporan.index')
                ->with('success', 'Laporan berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting laporan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }

    /**
     * Submit laporan for approval
     */
    public function submitForApproval($id)
    {
        DB::beginTransaction();
        try {
            $laporan = LaporanInvestasi::findOrFail($id);

            if ($laporan->status_approval !== 'draft') {
                return back()->with('error', 'Laporan sudah disubmit sebelumnya!');
            }

            $laporan->update([
                'status_approval' => 'pending',
                'tanggal_disubmit' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Laporan berhasil disubmit untuk approval!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal submit laporan: ' . $e->getMessage());
        }
    }

    /**
     * Approve laporan - FIXED VERSION
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'approval_id' => 'required|exists:laporan_approvals,id',
            'komentar' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $approval = LaporanApproval::findOrFail($request->approval_id);
            
            // Check if user is authorized
            if ($approval->user_id !== Auth::id()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk approve!');
            }

            // Check if already approved/rejected
            if ($approval->status !== 'pending') {
                return back()->with('error', 'Approval ini sudah diproses sebelumnya!');
            }

            $approval->update([
                'status' => 'approved',
                'komentar' => $request->komentar,
                'tanggal_approval' => now(),
            ]);

            // Check if all approvals are approved
            $laporan = LaporanInvestasi::findOrFail($id);
            $pendingCount = $laporan->approvals()->where('status', 'pending')->count();
            $rejectedCount = $laporan->approvals()->where('status', 'rejected')->count();

            if ($rejectedCount > 0) {
                // Ada yang reject, status tetap rejected
                $laporan->update(['status_approval' => 'rejected']);
            } elseif ($pendingCount === 0) {
                // Semua sudah approve
                $laporan->update([
                    'status_approval' => 'approved',
                    'tanggal_approved' => now(),
                ]);
            } else {
                // Masih ada yang pending
                $laporan->update(['status_approval' => 'pending']);
            }

            DB::commit();

            return back()->with('success', 'Laporan berhasil di-approve!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving laporan: ' . $e->getMessage());
            return back()->with('error', 'Gagal approve laporan: ' . $e->getMessage());
        }
    }

    /**
     * Reject laporan - FIXED VERSION
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'approval_id' => 'required|exists:laporan_approvals,id',
            'komentar' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $approval = LaporanApproval::findOrFail($request->approval_id);
            
            if ($approval->user_id !== Auth::id()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk reject!');
            }

            if ($approval->status !== 'pending') {
                return back()->with('error', 'Approval ini sudah diproses sebelumnya!');
            }

            $approval->update([
                'status' => 'rejected',
                'komentar' => $request->komentar,
                'tanggal_approval' => now(),
            ]);

            $laporan = LaporanInvestasi::findOrFail($id);
            $laporan->update(['status_approval' => 'rejected']);

            DB::commit();

            return back()->with('success', 'Laporan berhasil di-reject!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting laporan: ' . $e->getMessage());
            return back()->with('error', 'Gagal reject laporan: ' . $e->getMessage());
        }
    }

    /**
     * Export to PDF - FIXED VERSION WITH SIGNATURE
     */
    public function exportPdf($id)
    {
        try {
            $laporan = LaporanInvestasi::with(['approvals.user', 'details'])->findOrFail($id);
            
            // Validasi data
            if ($laporan->details->isEmpty()) {
                return back()->with('error', 'Laporan tidak memiliki data detail untuk di-export!');
            }

            // Load approval settings untuk mendapatkan tanda tangan
            foreach ($laporan->approvals as $approval) {
                $setting = LaporanApprovalSetting::where('user_id', $approval->user_id)
                    ->where('role_approval', $approval->role_approval)
                    ->first();
                
                $approval->setting = $setting;
            }

            $groupedData = $this->groupDataByCOA($laporan->details);

            // Generate PDF dengan options yang benar
            $pdf = Pdf::loadView('Dashboard.Pekerjaan.Realisasi.Laporan.pdf', compact('laporan', 'groupedData'))
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isPhpEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('chroot', [public_path(), storage_path()]);

            // Download PDF
            return $pdf->download("Laporan-Investasi-{$laporan->kode_laporan}.pdf");
            
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel($id)
    {
        try {
            if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return back()->with('error', 'Package Excel belum terinstall. Jalankan: composer require maatwebsite/excel');
            }

            $laporan = LaporanInvestasi::with('details')->findOrFail($id);
            
            if ($laporan->details->isEmpty()) {
                return back()->with('error', 'Laporan tidak memiliki data detail untuk di-export!');
            }

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanInvestasiExport($laporan), 
                "Laporan-Investasi-{$laporan->kode_laporan}.xlsx"
            );
            
        } catch (\Exception $e) {
            Log::error('Error exporting Excel: ' . $e->getMessage());
            return back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Generate kode laporan
     */
    private function generateKodeLaporan($tahun, $bulan)
    {
        $prefix = "LI-{$tahun}-" . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $count = LaporanInvestasi::where('kode_laporan', 'LIKE', "{$prefix}%")->count();
        
        return $prefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate laporan data - FINAL VERSION
     */
    private function generateLaporanData($tahun, $bulan)
    {
        try {
            Log::info("Generating data for tahun: {$tahun}, bulan: {$bulan}");

            $data = DB::select("
                SELECT 
                    pk.coa,
                    pk.nomor_prodef_sap,
                    pk.nama_investasi,
                    COALESCE(sp.nama_sub, '-') AS uraian_pekerjaan,
                    
                    COALESCE((
                        SELECT SUM(pi.volume)
                        FROM pekerjaan_items pi
                        WHERE pi.po_id = po.id
                    ), 0) AS total_volume,
                    
                    COALESCE(rkap.nilai, 0) AS nilai_rkap,
                    COALESCE(pr.nilai_pr, 0) AS target_sd_bulan,
                    
                    COALESCE(po.nomor_po, '-') AS nomor_po,
                    COALESCE(po.nomor_kontrak, '-') AS nomor_kontrak,
                    COALESCE(po.nilai_po, 0) AS nilai_po,
                    po.tanggal_po,
                    COALESCE(po.pelaksana, '-') AS pelaksana,
                    COALESCE(po.waktu_pelaksanaan, '-') AS waktu_pelaksanaan,
                    COALESCE(po.estimated, '-') AS estimated,
                    
                    ROUND(COALESCE((
                        SELECT AVG(pd.bobot_realisasi)
                        FROM pekerjaan_items pi
                        JOIN progress pgs ON pgs.pekerjaan_item_id = pi.id
                        JOIN progress_details pd ON pd.progress_id = pgs.id
                        WHERE pi.po_id = po.id
                    ), 0), 2) AS realisasi_fisik,
                    
                    COALESCE((
                        SELECT SUM(pay.nilai_payment)
                        FROM payments pay
                        WHERE pay.pr_id = pr.id
                    ), 0) AS realisasi_pembayaran

                FROM pekerjaan pk
                LEFT JOIN sub_pekerjaan sp ON sp.pekerjaan_id = pk.id
                LEFT JOIN rkap_pekerjaan rkap 
                    ON rkap.pekerjaan_id = pk.id 
                    AND rkap.tahun = ?
                LEFT JOIN prs pr 
                    ON pr.pekerjaan_id = pk.id
                    AND pr.sub_pekerjaan_id = sp.id
                    AND pr.tanggal_pr IS NOT NULL
                    AND YEAR(pr.tanggal_pr) <= ?
                    AND MONTH(pr.tanggal_pr) <= ?
                LEFT JOIN pos po ON po.pr_id = pr.id
                
                WHERE pr.id IS NOT NULL
                
                ORDER BY pk.coa, pk.nomor_prodef_sap, sp.id, pr.id
            ", [$tahun, $tahun, $bulan]);

            Log::info('Query executed, rows returned: ' . count($data));

            $tryParse = function ($dateStr) {
                if (!$dateStr) return null;
                $dateStr = trim($dateStr);
                if ($dateStr === '' || $dateStr === '-' || strtolower($dateStr) === 'null') {
                    return null;
                }
                try {
                    $c = Carbon::parse($dateStr);
                    return $c->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::debug("Failed to parse date: {$dateStr}");
                    return null;
                }
            };

            $splitPattern = '/\s*s[.\-\/\s]?d\s*/i';

            foreach ($data as $item) {
                $estimated = isset($item->estimated) ? trim($item->estimated) : '';
                if ($estimated === '' || $estimated === '-') {
                    $item->mulai_kontrak = null;
                    $item->selesai_kontrak = null;
                } else {
                    $parts = preg_split($splitPattern, $estimated, 2, PREG_SPLIT_NO_EMPTY);
                    $rawMulai = isset($parts[0]) ? trim($parts[0]) : null;
                    $rawSelesai = isset($parts[1]) ? trim($parts[1]) : null;
                    $item->mulai_kontrak = $tryParse($rawMulai) ?? $rawMulai;
                    $item->selesai_kontrak = $tryParse($rawSelesai) ?? $rawSelesai;
                }
            }

            Log::info('Data parsing completed successfully');
            return $data;

        } catch (\Exception $e) {
            Log::error('Error in generateLaporanData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Group data by COA
     */
    private function groupDataByCOA($data)
    {
        $grouped = [];
        
        foreach ($data as $item) {
            $coa = is_object($item) ? $item->coa : ($item['coa'] ?? 'N/A');
            
            if (!isset($grouped[$coa])) {
                $grouped[$coa] = [
                    'items' => [],
                    'subtotal_volume' => 0,
                    'subtotal_rkap' => 0,
                    'subtotal_target' => 0,
                    'subtotal_pembayaran' => 0,
                ];
            }
            
            $grouped[$coa]['items'][] = $item;
            $grouped[$coa]['subtotal_volume'] += is_object($item) ? ($item->total_volume ?? 0) : ($item['total_volume'] ?? 0);
            $grouped[$coa]['subtotal_rkap'] += is_object($item) ? ($item->nilai_rkap ?? 0) : ($item['nilai_rkap'] ?? 0);
            $grouped[$coa]['subtotal_target'] += is_object($item) ? ($item->target_sd_bulan ?? 0) : ($item['target_sd_bulan'] ?? 0);
            $grouped[$coa]['subtotal_pembayaran'] += is_object($item) ? ($item->realisasi_pembayaran ?? 0) : ($item['realisasi_pembayaran'] ?? 0);
        }
        
        return $grouped;
    }

    /**
     * Create approval records - IMPROVED WITH LOGGING
     */
    private function createApprovalRecords($laporanId)
    {
        try {
            Log::info('Creating approval records for laporan ID: ' . $laporanId);

            $approvalSettings = LaporanApprovalSetting::active()
                ->ordered()
                ->get();
            
            Log::info('Found ' . $approvalSettings->count() . ' active approval settings');

            if ($approvalSettings->isEmpty()) {
                Log::warning('No approval settings found, using fallback logic');
                
                // Fallback: gunakan user yang login sebagai approver
                $currentUser = Auth::user();
                
                if (!$currentUser) {
                    Log::error('No authenticated user found');
                    throw new \Exception('User tidak terautentikasi');
                }
                
                LaporanApproval::create([
                    'laporan_id' => $laporanId,
                    'user_id' => $currentUser->id,
                    'role_approval' => 'manager_teknik',
                    'nama_approver' => strtoupper($currentUser->name),
                    'status' => 'pending',
                    'urutan' => 1,
                ]);
                
                Log::info('Created fallback approval record for user: ' . $currentUser->name);
                return;
            }

            // Create from settings
            foreach ($approvalSettings as $setting) {
                LaporanApproval::create([
                    'laporan_id' => $laporanId,
                    'user_id' => $setting->user_id,
                    'role_approval' => $setting->role_approval,
                    'nama_approver' => $setting->nama_approver,
                    'status' => 'pending',
                    'urutan' => $setting->urutan,
                ]);
                
                Log::info("Created approval record for: {$setting->nama_approver} (urutan: {$setting->urutan})");
            }

            Log::info('All approval records created successfully');

        } catch (\Exception $e) {
            Log::error('Error in createApprovalRecords: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            throw $e; // Re-throw agar rollback terjadi
        }
    }
}