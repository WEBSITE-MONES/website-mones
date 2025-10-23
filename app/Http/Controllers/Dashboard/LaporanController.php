<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\LaporanInvestasi;
use App\Models\LaporanApproval;
use App\Models\LaporanDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon; 

class LaporanController extends Controller
{
    /**
     * Display listing of laporan with filters
     */
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'rekap_rincian');
        $tahun = $request->get('tahun', date('Y'));

        $laporan = LaporanInvestasi::where('jenis_laporan', $jenis)
            ->where('tahun', $tahun)
            ->with(['approvals', 'pembuatLaporan'])
            ->orderBy('bulan', 'desc')
            ->get();

        return view('Dashboard.Pekerjaan.Realisasi.Laporan.investasi', compact('laporan', 'jenis', 'tahun'));
    }

    /**
     * Show detail laporan
     */
    public function show($id)
    {
        $laporan = LaporanInvestasi::with(['approvals.user', 'details', 'pembuatLaporan'])->findOrFail($id);
        
        // Generate data laporan jika belum ada details
        if ($laporan->details->isEmpty()) {
            $data = $this->generateLaporanData($laporan->tahun, $laporan->bulan);
        } else {
            $data = $laporan->details;
        }

        // Group data by COA untuk tampilan nested
        $groupedData = $this->groupDataByCOA($data);

        return view('Dashboard.Pekerjaan.Realisasi.Laporan.detail_laporan_investasi', compact('laporan', 'groupedData'));
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
     * Store new laporan
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
            // Generate kode laporan
            $kode = $this->generateKodeLaporan($request->tahun, $request->bulan);
            
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            // Create laporan
            $laporan = LaporanInvestasi::create([
                'kode_laporan' => $kode,
                'jenis_laporan' => $request->jenis_laporan,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'periode_label' => "Laporan s.d {$namaBulan[$request->bulan]}",
                'status_approval' => 'draft',
                'dibuat_oleh' => Auth::id(),
            ]);

            // Generate dan simpan data laporan
            $data = $this->generateLaporanData($request->tahun, $request->bulan);
            
            if (empty($data)) {
                DB::rollBack();
                return back()->with('error', 'Tidak ada data untuk periode yang dipilih!')->withInput();
            }

            foreach ($data as $item) {
                // Sanitize data untuk mencegah NULL values
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
                    'mulai_kontrak' => $item->mulai_kontrak ?? '-',
                    'selesai_kontrak' => $item->selesai_kontrak ?? '-',
                    'realisasi_fisik' => $item->realisasi_fisik ?? 0,
                    'realisasi_pembayaran' => $item->realisasi_pembayaran ?? 0,
                ];
                
                LaporanDetail::create($detailData);
            }

            // Create approval records
            $this->createApprovalRecords($laporan->id);

            DB::commit();

            return redirect()->route('laporan.show', $laporan->id)
                ->with('success', 'Laporan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating laporan: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Submit laporan for approval
     */
    public function submitForApproval($id)
    {
        $laporan = LaporanInvestasi::findOrFail($id);

        if ($laporan->status_approval !== 'draft') {
            return back()->with('error', 'Laporan sudah disubmit sebelumnya!');
        }

        $laporan->update([
            'status_approval' => 'pending',
            'tanggal_disubmit' => now(),
        ]);

        // TODO: Send notification to approvers
        // Mail::to($approvers)->send(new LaporanSubmitted($laporan));

        return back()->with('success', 'Laporan berhasil disubmit untuk approval!');
    }

    /**
     * Approve laporan
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

            $approval->update([
                'status' => 'approved',
                'komentar' => $request->komentar,
                'tanggal_approval' => now(),
            ]);

            // Check if all approvals are approved
            $laporan = LaporanInvestasi::findOrFail($id);
            $allApproved = $laporan->approvals()->where('status', '!=', 'approved')->count() === 0;

            if ($allApproved) {
                $laporan->update([
                    'status_approval' => 'approved',
                    'tanggal_approved' => now(),
                ]);
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
     * Reject laporan
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
     * Export to PDF
     */
    public function exportPdf($id)
    {
        try {
            $laporan = LaporanInvestasi::with(['approvals', 'details'])->findOrFail($id);
            
            // Validasi data
            if ($laporan->details->isEmpty()) {
                return back()->with('error', 'Laporan tidak memiliki data detail untuk di-export!');
            }

            $groupedData = $this->groupDataByCOA($laporan->details);

            // Generate PDF
            $pdf = Pdf::loadView('Dashboard.Pekerjaan.Realisasi.Laporan.pdf', compact('laporan', 'groupedData'))
                ->setPaper('a4', 'landscape')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans');

            // Download PDF
            return $pdf->download("Laporan-Investasi-{$laporan->kode_laporan}.pdf");
            
            // Atau gunakan stream() untuk preview di browser:
            // return $pdf->stream("Laporan-Investasi-{$laporan->kode_laporan}.pdf");
            
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel($id)
    {
        try {
            // Cek apakah package Excel sudah terinstall
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
     * Generate laporan data from database
     */
    private function generateLaporanData($tahun, $bulan)
{
    try {
        $data = DB::select("
            SELECT 
                pk.coa AS coa,
                pk.nomor_prodef_sap AS nomor_prodef_sap,
                pk.nama_investasi AS nama_investasi,
                COALESCE(sp.nama_sub, '-') AS uraian_pekerjaan,
                COALESCE(SUM(pi.volume), 0) AS total_volume,
                COALESCE(rkap.nilai, 0) AS nilai_rkap,
                COALESCE(SUM(pr.nilai_pr), 0) AS target_sd_bulan,
                COALESCE(po.nomor_po, '-') AS nomor_po,
                po.tanggal_po,
                COALESCE(po.pelaksana, '-') AS pelaksana,
                COALESCE(po.waktu_pelaksanaan, '-') AS waktu_pelaksanaan,
                COALESCE(po.estimated, '-') AS estimated,
                ROUND(COALESCE(AVG(pd.bobot_realisasi), 0), 2) AS realisasi_fisik,
                COALESCE(SUM(pay.nilai_payment), 0) AS realisasi_pembayaran
            FROM pekerjaan pk
            LEFT JOIN sub_pekerjaan sp ON sp.pekerjaan_id = pk.id
            LEFT JOIN rkap_pekerjaan rkap ON rkap.pekerjaan_id = pk.id
            LEFT JOIN prs pr ON pr.pekerjaan_id = pk.id
            LEFT JOIN pos po ON po.pr_id = pr.id
            LEFT JOIN pekerjaan_items pi ON pi.po_id = po.id
            LEFT JOIN progress pgs ON pgs.pekerjaan_item_id = pk.id
            LEFT JOIN progress_details pd ON pd.progress_id = pgs.id
            LEFT JOIN payments pay ON pay.pr_id = pr.id
            WHERE YEAR(pr.tanggal_pr) = ?
              AND MONTH(pr.tanggal_pr) <= ?
            GROUP BY 
                pk.id,
                pk.coa,
                pk.nomor_prodef_sap,
                pk.nama_investasi,
                sp.nama_sub,
                rkap.nilai,
                po.nomor_po,
                po.tanggal_po,
                po.pelaksana,
                po.waktu_pelaksanaan,
                po.estimated
            ORDER BY pk.coa, pk.nomor_prodef_sap
        ", [$tahun, $bulan]);

        $tryParse = function ($dateStr) {
            $dateStr = trim($dateStr);
            if ($dateStr === '' || $dateStr === '-' || strtolower($dateStr) === 'null') {
                return null;
            }

            $formats = [
                DATE_ATOM, // ISO
                'Y-m-d',
                'Y/m/d',
                'd-m-Y',
                'd/m/Y',
                'd M Y',  
                'Y.m.d',
            ];

            try {
                $c = Carbon::parse($dateStr);
                return $c->format('Y-m-d');
            } catch (\Exception $e) {
            }

            foreach ($formats as $fmt) {
                try {
                    $c = Carbon::createFromFormat($fmt, $dateStr);
                    if ($c) return $c->format('Y-m-d');
                } catch (\Exception $e) {
                    // lanjutkan
                }
            }

            return $dateStr;
        };
        $splitPattern = '/\s*s[.\-\/\s]?d\s*/i';

        foreach ($data as $item) {
            $estimated = isset($item->estimated) ? trim($item->estimated) : '';

            if ($estimated === '' || $estimated === '-' ) {
                $item->mulai_kontrak = null;
                $item->selesai_kontrak = null;
            } else {
                $parts = preg_split($splitPattern, $estimated, 2, PREG_SPLIT_NO_EMPTY);

                if (count($parts) === 1) {
                    $parts = [trim($parts[0])];
                }

                $rawMulai = isset($parts[0]) ? trim($parts[0]) : null;
                $rawSelesai = isset($parts[1]) ? trim($parts[1]) : null;
                $parsedMulai = $rawMulai ? $tryParse($rawMulai) : null;
                $parsedSelesai = $rawSelesai ? $tryParse($rawSelesai) : null;

                $item->mulai_kontrak = $parsedMulai ?? $rawMulai ?? null;
                $item->selesai_kontrak = $parsedSelesai ?? $rawSelesai ?? null;
            }
        }

        return $data;

    } catch (\Exception $e) {
        Log::error('Error generating laporan data: ' . $e->getMessage());
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
     * Create approval records
     */
    private function createApprovalRecords($laporanId)
    {
        // Ambil 2 user pertama yang ada di database untuk approval
        // Berdasarkan data Anda: ID 2 (Admin P-Mones) dan ID 4 (Asrini Muhsin)
        
        $approvers = [];
        
        // Manager Teknik - gunakan user pertama (ID 2: Admin P-Mones)
        $managerTeknik = User::find(2);
        if ($managerTeknik) {
            $approvers[] = [
                'user_id' => $managerTeknik->id,
                'role' => 'manager_teknik',
                'nama' => strtoupper($managerTeknik->name),
                'urutan' => 1
            ];
        }
        
        // Assisten Manager - gunakan user kedua (ID 4: Asrini Muhsin)
        $assistenManager = User::find(4);
        if ($assistenManager) {
            $approvers[] = [
                'user_id' => $assistenManager->id,
                'role' => 'assisten_manager',
                'nama' => strtoupper($assistenManager->name),
                'urutan' => 2
            ];
        }
        
        // Fallback: Jika user dengan ID tertentu tidak ada, ambil 2 user pertama
        if (empty($approvers)) {
            $users = User::orderBy('id')->limit(2)->get();
            
            if ($users->count() >= 2) {
                $approvers = [
                    [
                        'user_id' => $users[0]->id,
                        'role' => 'manager_teknik',
                        'nama' => strtoupper($users[0]->name),
                        'urutan' => 1
                    ],
                    [
                        'user_id' => $users[1]->id,
                        'role' => 'assisten_manager',
                        'nama' => strtoupper($users[1]->name),
                        'urutan' => 2
                    ],
                ];
            } else {
                // Jika hanya ada 1 user, gunakan user yang login
                $currentUser = Auth::user();
                $approvers = [
                    [
                        'user_id' => $currentUser->id,
                        'role' => 'manager_teknik',
                        'nama' => strtoupper($currentUser->name),
                        'urutan' => 1
                    ],
                    [
                        'user_id' => $currentUser->id,
                        'role' => 'assisten_manager',
                        'nama' => strtoupper($currentUser->name),
                        'urutan' => 2
                    ],
                ];
            }
        }

        // Create approval records
        foreach ($approvers as $approver) {
            LaporanApproval::create([
                'laporan_id' => $laporanId,
                'user_id' => $approver['user_id'],
                'role_approval' => $approver['role'],
                'nama_approver' => $approver['nama'],
                'status' => 'pending',
                'urutan' => $approver['urutan'],
            ]);
        }
    }
}