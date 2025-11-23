<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pekerjaan;
use App\Models\SubPekerjaan;
use App\Models\Po;  
use App\Models\PekerjaanItem;
use App\Models\MasterMinggu;
use App\Models\DailyProgress;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;


class ProgresController extends Controller
{
    // ==================== VIEW ROUTES ====================
    
    public function index()
    {
        $user = Auth::user();
        return view('LandingPage.index', compact('user'));
    }
    
    public function pelaporan()
    {
        $user = Auth::user();
        return view('LandingPage.pelaporan', compact('user'));
    }
    
        public function pelaporanform()
    {
        $user = Auth::user();
        
        // // Load wilayah yang memiliki pekerjaan
        // $wilayah = \App\Models\Wilayah::whereHas('pekerjaans')
        //     ->orderBy('nama', 'asc')
        //     ->get();
        
        // // Load semua pekerjaan dengan relasi wilayah (untuk fallback)
        // $pekerjaan = Pekerjaan::with('wilayah')
        //     ->orderBy('nama_investasi', 'asc')
        //     ->get();
        
        return view('LandingPage.pelaporan-form', compact('user'));
    }
        
    public function pelaporanformedit()
    {
        $user = Auth::user();
        return view('LandingPage.pelaporan-form_edit', compact('user'));
    }
    
    // DOKUMENTASI
    public function dokumentasi()
    {
        try {
            $user = Auth::user();
            
            // Ambil semua pekerjaan untuk filter dropdown
            $pekerjaans = Pekerjaan::with('wilayah')
                ->whereHas('subPekerjaan.pr.po.dailyProgresses')
                ->orderBy('nama_investasi', 'asc')
                ->get();
            
            return view('LandingPage.dokumentasi', compact('user', 'pekerjaans'));
            
        } catch (\Exception $e) {
            Log::error('Error dokumentasi page', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('landingpage.index')
                ->with('error', 'Gagal memuat halaman dokumentasi');
        }
    }

    public function debugDokumentasi()
{
    try {
        // 1. Cek total records
        $totalRecords = DailyProgress::count();
        
        // 2. Cek records dengan foto
        $recordsWithFoto = DailyProgress::whereNotNull('foto')->count();
        
        // 3. Cek records dengan foto valid (JSON array)
        $recordsWithValidFoto = DailyProgress::whereNotNull('foto')
            ->whereRaw("JSON_LENGTH(foto) > 0")
            ->count();
        
        // 4. Sample data 5 terakhir
        $samples = DailyProgress::with(['pelapor:id,name'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($report) {
                return [
                    'id' => $report->id,
                    'tanggal' => [
                        'raw' => $report->getAttributes()['tanggal'], // Raw dari DB
                        'formatted' => $report->tanggal ? $report->tanggal->format('Y-m-d H:i:s') : 'NULL',
                        'type' => gettype($report->tanggal),
                        'is_carbon' => $report->tanggal instanceof \Carbon\Carbon,
                    ],
                    'foto' => [
                        'is_null' => is_null($report->foto),
                        'is_array' => is_array($report->foto),
                        'count' => is_array($report->foto) ? count($report->foto) : 0,
                        'structure' => is_array($report->foto) && count($report->foto) > 0 
                            ? array_keys($report->foto[0]) 
                            : null,
                        'sample_url' => is_array($report->foto) && count($report->foto) > 0 
                            ? ($report->foto[0]['url'] ?? 'NO_URL') 
                            : null,
                    ],
                    'po_id' => $report->po_id,
                    'pelapor' => $report->pelapor ? $report->pelapor->name : 'NULL',
                    'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                ];
            });
        
        // 5. Cek struktur database
        $tableInfo = DB::select("DESCRIBE daily_progress");
        
        // 6. Test query yang digunakan di apiGetDokumentasi
        $testQuery = DailyProgress::with([
            'po:id,nomor_po,pelaksana,pr_id',
            'pelapor:id,name'
        ])
        ->where('status_approval', '!=', 'rejected')
        ->whereNotNull('foto')
        ->whereRaw("JSON_LENGTH(foto) > 0")
        ->orderBy('tanggal', 'desc')
        ->take(3)
        ->get();
        
        $queryResult = $testQuery->map(function($report) {
            return [
                'id' => $report->id,
                'has_po' => !is_null($report->po),
                'has_pelapor' => !is_null($report->pelapor),
                'foto_count' => is_array($report->foto) ? count($report->foto) : 0,
                'tanggal_ok' => $report->tanggal instanceof \Carbon\Carbon,
            ];
        });
        
        return response()->json([
            'database' => [
                'total_records' => $totalRecords,
                'records_with_foto' => $recordsWithFoto,
                'records_with_valid_foto' => $recordsWithValidFoto,
                'foto_percentage' => $totalRecords > 0 
                    ? round(($recordsWithFoto / $totalRecords) * 100, 2) . '%' 
                    : '0%',
            ],
            'sample_data' => $samples,
            'table_structure' => $tableInfo,
            'test_query' => [
                'count' => $testQuery->count(),
                'results' => $queryResult,
            ],
            'model_casts' => DailyProgress::make()->getCasts(),
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ], 500, [], JSON_PRETTY_PRINT);
    }
}

    public function apiGetDokumentasi(Request $request)
{
    try {
        Log::info('API Get Dokumentasi called', [
            'filters' => $request->all()
        ]);
        
        $query = DailyProgress::with([
            'po:id,nomor_po,pelaksana,pr_id',
            'po.pr.pekerjaan:id,nama_investasi',
            'pekerjaanItem:id,kode_pekerjaan,jenis_pekerjaan_utama,sub_pekerjaan,sub_sub_pekerjaan',
            'pelapor:id,name'
        ])
        ->where('status_approval', '!=', 'rejected')
        // ✅ PENTING: Hanya ambil yang PUNYA foto
        ->whereNotNull('foto')
        ->whereRaw("JSON_LENGTH(foto) > 0") // Pastikan array foto tidak kosong
        ->orderBy('tanggal', 'desc')
        ->orderBy('created_at', 'desc');
        
        // Filter by pekerjaan
        if ($request->has('pekerjaan_id') && $request->pekerjaan_id) {
            $prIds = SubPekerjaan::where('pekerjaan_id', $request->pekerjaan_id)
                ->pluck('pr_id');
            $poIds = Po::whereIn('pr_id', $prIds)->pluck('id');
            $query->whereIn('po_id', $poIds);
        }
        
        // Filter by date range
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        
        if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $query->where('tanggal', '<=', $request->tanggal_akhir);
        }
        
        $reports = $query->get();
        
        Log::info('Reports found', ['count' => $reports->count()]);
        
        // Extract all photos from reports
        $allPhotos = [];
        $photoId = 0;
        
        foreach ($reports as $report) {
            // ✅ Double check foto array
            if (empty($report->foto) || !is_array($report->foto)) {
                Log::warning('Report has invalid foto', ['report_id' => $report->id]);
                continue;
            }
            
            $namaProyek = 'Unknown';
            $projectSlug = 'unknown';
            
            if ($report->po && $report->po->pr && $report->po->pr->pekerjaan) {
                $namaProyek = $report->po->pr->pekerjaan->nama_investasi;
                $projectSlug = strtolower(str_replace(' ', '_', $namaProyek));
            }
            
            foreach ($report->foto as $index => $foto) {
                // ✅ Validasi struktur foto
                if (!is_array($foto) || empty($foto['url'])) {
                    Log::warning('Invalid photo structure', [
                        'report_id' => $report->id,
                        'photo_index' => $index
                    ]);
                    continue;
                }
                
                $allPhotos[] = [
                    'id' => $photoId++,
                    'url' => $foto['url'] ?? '',
                    'thumbnail' => $foto['url'] ?? '',
                    'title' => $report->jenis_pekerjaan ?? 'Dokumentasi',
                    'description' => $report->deskripsi ?? '',
                    'date' => $report->tanggal->format('Y-m-d'),
                    'time' => $report->tanggal->format('H:i'),
                    'gps' => [
                        'lat' => (float) ($foto['gps_lat'] ?? $report->gps_latitude ?? 0),
                        'lon' => (float) ($foto['gps_lon'] ?? $report->gps_longitude ?? 0),
                        'accuracy' => (int) ($foto['gps_accuracy'] ?? 10)
                    ],
                    'weather' => [
                        'temp' => (int) ($report->cuaca_suhu ?? 28),
                        'desc' => $report->cuaca_deskripsi ?? 'Cerah',
                        'icon' => $this->getWeatherIcon($report->cuaca_deskripsi),
                        'humidity' => (int) ($report->cuaca_kelembaban ?? 70)
                    ],
                    'project' => $projectSlug,
                    'projectName' => $namaProyek,
                    'pelapor' => $report->pelapor->name ?? 'Unknown',
                    'status' => $report->status_approval,
                    'location_name' => $foto['location_name'] ?? $report->lokasi_nama ?? 'Unknown'
                ];
            }
        }
        
        Log::info('Photos extracted', ['total' => count($allPhotos)]);
        
        // Calculate stats
        $uniqueGPS = array_unique(array_map(function($p) {
            return $p['gps']['lat'] . ',' . $p['gps']['lon'];
        }, $allPhotos));
        
        $uniqueProjects = array_unique(array_column($allPhotos, 'project'));
        
        $stats = [
            'total_photos' => count($allPhotos),
            'unique_locations' => count($uniqueGPS),
            'active_projects' => count($uniqueProjects),
            'last_update' => $reports->isNotEmpty() 
                ? $reports->first()->tanggal->diffForHumans() 
                : 'Belum ada data'
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'photos' => $allPhotos,
                'stats' => $stats
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error apiGetDokumentasi', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat dokumentasi: ' . $e->getMessage()
        ], 500);
    }
}

    private function getWeatherIcon($description)
    {
        if (!$description) return '☀️';
        
        $desc = strtolower($description);
        
        if (strpos($desc, 'cerah') !== false) return '☀️';
        if (strpos($desc, 'berawan') !== false) return '⛅';
        if (strpos($desc, 'hujan') !== false) return '🌧️';
        if (strpos($desc, 'mendung') !== false) return '☁️';
        if (strpos($desc, 'badai') !== false) return '⛈️';
        
        return '🌤️';
    }

    // DOKUMENTASI
    
    // ==================== API: GET PO BY PEKERJAAN ====================
    
    public function getPoByPekerjaan($pekerjaanId)
    {
        try {
            // Ambil PR yang terkait dengan pekerjaan
            $prIds = SubPekerjaan::where('pekerjaan_id', $pekerjaanId)
                ->pluck('pr_id')
                ->toArray();
            
            if (empty($prIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'count' => 0,
                    'message' => 'Tidak ada PR untuk pekerjaan ini'
                ]);
            }
            
            // Ambil PO berdasarkan PR
            $pos = Po::whereIn('pr_id', $prIds)
                ->select('id', 'pr_id', 'nomor_po', 'tanggal_po', 'pelaksana', 'nilai_po')
                ->orderBy('tanggal_po', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $pos,
                'count' => $pos->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getPoByPekerjaan', [
                'pekerjaan_id' => $pekerjaanId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== API: GET PEKERJAAN ITEMS BY PO ====================
    
    public function getPekerjaanItemsByPo($poId)
    {
        try {
            // Ambil items dengan struktur hierarki
            $items = PekerjaanItem::where('po_id', $poId)
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->with('children')->orderBy('kode_pekerjaan', 'asc');
                }])
                ->orderBy('kode_pekerjaan', 'asc')
                ->get();
            
            // Format ke tree structure
            $formattedItems = $this->formatItemsForDropdown($items);
            
            return response()->json([
                'success' => true,
                'data' => $formattedItems,
                'count' => count($formattedItems)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getPekerjaanItemsByPo', [
                'po_id' => $poId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // Helper: Format items ke dropdown hierarki
    private function formatItemsForDropdown($items, $level = 0)
    {
        $result = [];
        
        foreach ($items as $item) {
            // Tentukan prefix berdasarkan level
            $prefix = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
            if ($level > 0) {
                $prefix .= '└─ ';
            }
            
            // Ambil nama dari kolom yang sesuai
            $nama = $item->jenis_pekerjaan_utama ?: 
                    ($item->sub_pekerjaan ?: 
                    ($item->sub_sub_pekerjaan ?: 'Item Pekerjaan'));

            $result[] = [
                'id' => $item->id,
                'kode' => $item->kode_pekerjaan,
                'nama' => $nama,
                'display' => $prefix . $item->kode_pekerjaan . ' - ' . $nama,
                'volume' => $item->volume,
                'satuan' => $item->sat,
                'bobot' => $item->bobot,
                'level' => $level,
                'has_children' => $item->children->count() > 0
            ];
            
            // Rekursif untuk children
            if ($item->children->count() > 0) {
                $childItems = $this->formatItemsForDropdown($item->children, $level + 1);
                $result = array_merge($result, $childItems);
            }
        }
        
        return $result;
    }
    
    // ==================== API: STORE DAILY PROGRESS ====================
    
    public function apiStoreReport(Request $request)
    {
        try {
            Log::info('Store Daily Progress Request', [
                'user_id' => Auth::id(),
                'has_files' => $request->hasFile('foto_0'),
                'all_keys' => array_keys($request->all())
            ]);
            
            // Validasi data
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'pekerjaan' => 'required|exists:pekerjaan,id', 
                'po_id' => 'required|exists:pos,id',
                'pekerjaan_item_id' => 'required|exists:pekerjaan_items,id',
                'jenis_pekerjaan' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'rencana_besok' => 'required|string'
            ]);
            
            // Process foto uploads
            $fotoData = [];
            $photoIndex = 0;
            
            while ($request->hasFile("foto_{$photoIndex}")) {
                $file = $request->file("foto_{$photoIndex}");
                
                Log::info("Processing photo {$photoIndex}", [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ]);
                
                // Upload file ke storage/public/daily-progress-photos
                $path = $file->store('daily-progress-photos', 'public');
                
                $fotoData[] = [
                    'url' => asset('storage/' . $path),
                    'path' => $path,
                    'gps_lat' => $request->input("foto_{$photoIndex}_gps_lat"),
                    'gps_lon' => $request->input("foto_{$photoIndex}_gps_lon"),
                    'gps_accuracy' => $request->input("foto_{$photoIndex}_gps_accuracy"),
                    'gps_timestamp' => $request->input("foto_{$photoIndex}_gps_timestamp"),
                    'location_name' => $request->input("foto_{$photoIndex}_location_name"),
                    'weather' => $request->input("foto_{$photoIndex}_weather")
                ];
                
                $photoIndex++;
            }
            
            Log::info("Total photos processed: {$photoIndex}");
            
            // Validasi minimal 2 foto
            if (count($fotoData) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal upload 2 foto dokumentasi'
                ], 422);
            }
            
            // Get location name from coordinates
            $locationName = $request->input('location_name') 
                ?? $this->getLocationNameFromGPS(
                    $request->input('latitude'), 
                    $request->input('longitude')
                );
            
            // Create daily progress record
            $dailyProgress = DailyProgress::create([
                'tanggal' => $request->input('tanggal'),
                'po_id' => $request->input('po_id'),
                'pekerjaan_item_id' => $request->input('pekerjaan_item_id'),
                'pelapor_id' => Auth::id(),
                'jenis_pekerjaan' => $request->input('jenis_pekerjaan'),
                'volume_realisasi' => $request->input('volume', 0),
                'satuan' => $request->input('satuan'),
                'deskripsi' => $request->input('deskripsi'),
                'gps_latitude' => $request->input('latitude'),
                'gps_longitude' => $request->input('longitude'),
                'lokasi_nama' => $locationName,
                'cuaca_suhu' => $request->input('cuaca_suhu'),
                'cuaca_deskripsi' => $request->input('cuaca_deskripsi'),
                'cuaca_kelembaban' => $request->input('cuaca_kelembaban'),
                'jam_kerja' => $request->input('jam_kerja', 0),
                'kondisi_lapangan' => $request->input('kondisi_lapangan', 'normal'),
                'kendala' => $request->input('kendala'),
                'solusi' => $request->input('solusi'),
                'rencana_besok' => $request->input('rencana_besok'),
                'jumlah_pekerja' => $request->input('jumlah_pekerja', 0),
                'alat_berat' => $request->input('alat_berat'),
                'material' => $request->input('material'),
                'foto' => $fotoData,
                'status_approval' => 'pending'
            ]);
            
            Log::info('Daily Progress Created Successfully', [
                'id' => $dailyProgress->id,
                'lokasi' => $locationName,
                'photos_count' => count($fotoData)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Laporan progress harian berhasil disimpan',
                'data' => [
                    'id' => $dailyProgress->id,
                    'lokasi_nama' => $locationName,
                    'photos_count' => count($fotoData)
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error storing daily progress', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== API: GET ALL REPORTS ====================
    
    public function apiGetReports(Request $request)
    {
        try {
            $query = DailyProgress::with([
                'po:id,nomor_po,pelaksana,pr_id',
                'po.pr.pekerjaan:id,nama_investasi',
                'pekerjaanItem:id,kode_pekerjaan,jenis_pekerjaan_utama,sub_pekerjaan,sub_sub_pekerjaan',
                'pelapor:id,name',
                'approver:id,name'
            ])
            ->orderBy('tanggal', 'desc');
            
            // Filter by pekerjaan (via PO)
            if ($request->has('pekerjaan') && $request->pekerjaan) {
                $prIds = SubPekerjaan::where('pekerjaan_id', $request->pekerjaan)
                    ->pluck('pr_id');
                $poIds = Po::whereIn('pr_id', $prIds)->pluck('id');
                $query->whereIn('po_id', $poIds);
            }
            
            // Filter by date range
            if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
                $query->where('tanggal', '>=', $request->tanggal_mulai);
            }
            
            if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
                $query->where('tanggal', '<=', $request->tanggal_akhir);
            }
            
            // Filter by pelapor (current user if not admin)
            if (Auth::user()->role === 'user') {
                $query->where('pelapor_id', Auth::id());
            }
            
            $reports = $query->get();
            
            // Format data untuk frontend
            $formattedReports = $reports->map(function($report) {
                $namaPekerjaan = 'Item Tidak Ditemukan';
                $kodePekerjaan = '-';
                
                if ($report->pekerjaanItem) {
                    $item = $report->pekerjaanItem;
                    $kodePekerjaan = $item->kode_pekerjaan;
                    
                    if (!empty($item->jenis_pekerjaan_utama)) {
                        $namaPekerjaan = $item->jenis_pekerjaan_utama;
                    } elseif (!empty($item->sub_pekerjaan)) {
                        $namaPekerjaan = $item->sub_pekerjaan;
                    } elseif (!empty($item->sub_sub_pekerjaan)) {
                        $namaPekerjaan = $item->sub_sub_pekerjaan;
                    } else {
                        $namaPekerjaan = $report->jenis_pekerjaan ?? 'Pekerjaan Tidak Diketahui';
                    }
                }
                
                $pekerjaan = 'unknown';
                $namaProyek = 'Unknown';
                
                if ($report->po && $report->po->pr && $report->po->pr->pekerjaan) {
                    $namaProyek = $report->po->pr->pekerjaan->nama_investasi;
                    $pekerjaan = strtolower(str_replace(' ', '_', $namaProyek));
                }
                
                // ✅ Gunakan lokasi_nama dari database jika ada, kalau tidak ambil dari GPS
                $lokasiNama = $report->lokasi_nama 
                    ?? $this->getLocationNameFromGPS($report->gps_latitude, $report->gps_longitude);
                
                return [
                    'id' => $report->id,
                    'tanggal' => $report->tanggal->format('Y-m-d'),
                    'pelapor' => $report->pelapor->name ?? 'Unknown',
                    'pekerjaan' => $pekerjaan,
                    'nama_proyek' => $namaProyek,
                    'kode_pekerjaan' => $kodePekerjaan,
                    'nama_pekerjaan' => $namaPekerjaan,
                    'jenis_pekerjaan' => $report->jenis_pekerjaan,
                    'volume' => (float) $report->volume_realisasi,
                    'satuan' => $report->satuan,
                    'deskripsi' => $report->deskripsi,
                    'latitude' => (float) $report->gps_latitude,
                    'longitude' => (float) $report->gps_longitude,
                    'lokasi_nama' => $lokasiNama,
                    'cuaca_suhu' => (float) $report->cuaca_suhu,
                    'cuaca_deskripsi' => $report->cuaca_deskripsi,
                    'cuaca_kelembaban' => (int) $report->cuaca_kelembaban,
                    'jam_kerja' => (float) $report->jam_kerja,
                    'kondisi_lapangan' => $report->kondisi_lapangan,
                    'kendala' => $report->kendala,
                    'solusi' => $report->solusi,
                    'rencana_besok' => $report->rencana_besok,
                    'jumlah_pekerja' => (int) $report->jumlah_pekerja,
                    'alat_berat' => $report->alat_berat,
                    'material' => $report->material,
                    'status_approval' => $report->status_approval,
                    'approver_nama' => $report->approver->name ?? null,
                    'fotos' => $this->formatFotos($report->foto),
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedReports,
                'count' => $formattedReports->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error apiGetReports', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data laporan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== HELPER: REVERSE GEOCODING (GET LOCATION NAME) ====================
    
    private function getLocationNameFromGPS($lat, $lon)
    {
        $cacheKey = 'geocode_' . round($lat, 3) . '_' . round($lon, 3);
        
        return cache()->remember($cacheKey, now()->addDays(30), function () use ($lat, $lon) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'User-Agent' => 'P-Mones-App/1.0 (monitoring@pelindo.co.id)'
                    ])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'json',
                        'lat' => $lat,
                        'lon' => $lon,
                        'zoom' => 18,
                        'addressdetails' => 1,
                        'accept-language' => 'id'
                    ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $address = $data['address'] ?? [];
                    
                    $parts = [];
                    
                    if (!empty($address['road']) || !empty($address['street'])) {
                        $parts[] = $address['road'] ?? $address['street'];
                    }
                    
                    if (!empty($address['suburb']) || !empty($address['village'])) {
                        $parts[] = $address['suburb'] ?? $address['village'];
                    }
                    
                    if (!empty($address['city']) || !empty($address['town'])) {
                        $parts[] = $address['city'] ?? $address['town'];
                    }
                    
                    if (!empty($address['state'])) {
                        $parts[] = $address['state'];
                    }
                    
                    if (!empty($parts)) {
                        return implode(', ', array_filter($parts));
                    }
                    
                    if (!empty($data['display_name'])) {
                        return strlen($data['display_name']) > 80 
                            ? substr($data['display_name'], 0, 77) . '...' 
                            : $data['display_name'];
                    }
                }
                
            } catch (\Exception $e) {
                Log::warning('Reverse geocoding failed', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'error' => $e->getMessage()
                ]);
            }
            
            return null;
        }) ?: sprintf("%.4f, %.4f", $lat, $lon);
    }
    
    // ==================== HELPER FUNCTIONS ====================
    
    private function formatFotos($fotoArray)
    {
        if (empty($fotoArray)) {
            return [];
        }
        
        return collect($fotoArray)->map(function($foto, $index) {
            return [
                'id' => $index,
                'url' => $foto['url'] ?? '#',
                'gps_lat' => $foto['gps_lat'] ?? null,
                'gps_lon' => $foto['gps_lon'] ?? null,
            ];
        })->values()->toArray();
    }
    
    // ==================== API: SHOW SINGLE REPORT ====================
    
    public function apiShowReport($id)
    {
        try {
            $report = DailyProgress::with([
                'po:id,nomor_po,pelaksana,pr_id',
                'po.pr.pekerjaan:id,nama_investasi',
                'pekerjaanItem:id,kode_pekerjaan,jenis_pekerjaan_utama,sub_pekerjaan,sub_sub_pekerjaan',
                'pelapor:id,name',
                'approver:id,name'
            ])->findOrFail($id);
            
            // Check authorization
            if (Auth::user()->role === 'user' && $report->pelapor_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => $report
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing report', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }
    }
    
    // ==================== API: UPDATE REPORT ====================
    
    public function apiUpdateReport(Request $request, $id)
    {
        try {
            $report = DailyProgress::findOrFail($id);
            
            // Check authorization
            if (Auth::user()->role === 'user' && $report->pelapor_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            // Validasi data
            $validated = $request->validate([
                'tanggal' => 'sometimes|date',
                'jenis_pekerjaan' => 'sometimes|string|max:255',
                'deskripsi' => 'sometimes|string',
                'latitude' => 'sometimes|numeric',
                'longitude' => 'sometimes|numeric',
                'rencana_besok' => 'sometimes|string'
            ]);
            
            // Update location name if coordinates changed
            if ($request->has('latitude') && $request->has('longitude')) {
                $validated['lokasi_nama'] = $request->input('location_name') 
                    ?? $this->getLocationNameFromGPS(
                        $request->input('latitude'), 
                        $request->input('longitude')
                    );
            }
            
            // Process new photos if uploaded
            if ($request->hasFile('foto_0')) {
                $fotoData = [];
                $photoIndex = 0;
                
                while ($request->hasFile("foto_{$photoIndex}")) {
                    $file = $request->file("foto_{$photoIndex}");
                    $path = $file->store('daily-progress-photos', 'public');
                    
                    $fotoData[] = [
                        'url' => asset('storage/' . $path),
                        'path' => $path,
                        'gps_lat' => $request->input("foto_{$photoIndex}_gps_lat"),
                        'gps_lon' => $request->input("foto_{$photoIndex}_gps_lon"),
                        'location_name' => $request->input("foto_{$photoIndex}_location_name")
                    ];
                    
                    $photoIndex++;
                }
                
                // Delete old photos
                if (!empty($report->foto)) {
                    foreach ($report->foto as $oldFoto) {
                        if (isset($oldFoto['path'])) {
                            Storage::disk('public')->delete($oldFoto['path']);
                        }
                    }
                }
                
                $validated['foto'] = $fotoData;
            }
            
            $report->update($validated);
            
            Log::info('Report Updated', ['id' => $report->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diupdate',
                'data' => $report
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating report', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate laporan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==================== API: DELETE REPORT ====================
    
    public function apiDeleteReport($id)
    {
        try {
            $report = DailyProgress::findOrFail($id);
            
            // Check authorization
            if (Auth::user()->role === 'user' && $report->pelapor_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            // Delete photos from storage
            if (!empty($report->foto)) {
                foreach ($report->foto as $foto) {
                    if (isset($foto['path'])) {
                        Storage::disk('public')->delete($foto['path']);
                    }
                }
            }
            
            $report->delete();
            
            Log::info('Report Deleted', ['id' => $id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting report', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus laporan: ' . $e->getMessage()
            ], 500);
        }
    }


    // untuk curva s
public function monitoringProgress()
{
    try {
        $user = Auth::user();
        
        // Ambil semua pekerjaan yang punya PO (sederhanakan dulu untuk testing)
        $pekerjaans = Pekerjaan::with('wilayah')
            ->whereHas('subPekerjaan.pr.po') // Pastikan ada PO
            ->orderBy('nama_investasi', 'asc')
            ->get();
        
        Log::info('Monitoring Progress Accessed', [
            'user_id' => $user->id,
            'pekerjaan_count' => $pekerjaans->count()
        ]);
        
        return view('LandingPage.monitoring-progress', compact('user', 'pekerjaans'));
        
    } catch (\Exception $e) {
        Log::error('Error monitoringProgress', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('landingpage.index')
            ->with('error', 'Gagal memuat halaman monitoring: ' . $e->getMessage());
    }
}
public function apiGetProgressData($pekerjaanId)
{
    try {
        // Ambil PO dari pekerjaan
        $prIds = SubPekerjaan::where('pekerjaan_id', $pekerjaanId)->pluck('pr_id');
        $po = Po::whereIn('pr_id', $prIds)
            ->with([
                'progresses.details.minggu',
                'progresses.pekerjaanItem'
            ])
            ->first();
        
        if (!$po) {
            return response()->json([
                'success' => false,
                'message' => 'Data PO tidak ditemukan'
            ], 404);
        }
        
        // Ambil master minggu
        $progressUtama = $po->progresses()->whereNull('pekerjaan_item_id')->first();
        $masterMinggu = MasterMinggu::where('progress_id', $progressUtama->id ?? 0)
            ->orderBy('tanggal_awal')
            ->get();
        
        // Hitung kumulatif rencana & realisasi per minggu
        $chartData = [];
        $cumulativeRencana = 0;
        $cumulativeRealisasi = 0;
        
        foreach ($masterMinggu as $minggu) {
            $rencanaWeek = 0;
            $realisasiWeek = 0;
            
            foreach ($po->progresses as $progress) {
                if (!$progress->pekerjaan_item_id) continue;
                
                $detail = $progress->details->firstWhere('minggu_id', $minggu->id);
                if ($detail) {
                    $rencanaWeek += (float) $detail->bobot_rencana;
                    $realisasiWeek += (float) $detail->bobot_realisasi;
                }
            }
            
            $cumulativeRencana += $rencanaWeek;
            $cumulativeRealisasi += $realisasiWeek;
            
            $chartData[] = [
                'week' => $minggu->kode_minggu,
                'week_label' => $minggu->tanggal_awal->format('d M') . ' - ' . $minggu->tanggal_akhir->format('d M'),
                'rencana' => round($cumulativeRencana, 2),
                'realisasi' => round($cumulativeRealisasi, 2),
                'deviasi' => round($cumulativeRealisasi - $cumulativeRencana, 2)
            ];
        }
        
        // Data untuk progress bars
        $rencanaPct = round($cumulativeRencana, 2);
        $realisasiPct = round($cumulativeRealisasi, 2);
        $deviasiPct = round($realisasiPct - $rencanaPct, 2);
        
        // Ambil item pekerjaan hierarki untuk tabel WBS
        $items = PekerjaanItem::where('po_id', $po->id)
            ->with(['children.children'])
            ->whereNull('parent_id')
            ->orderBy('kode_pekerjaan')
            ->get();
        
        // Map detail progress per item
        $progressDetailsMap = [];
        foreach ($po->progresses as $progress) {
            if (!$progress->pekerjaan_item_id) continue;
            
            foreach ($progress->details as $detail) {
                $progressDetailsMap[$progress->pekerjaan_item_id][$detail->minggu_id] = [
                    'bobot_rencana' => (float) $detail->bobot_rencana,
                    'bobot_realisasi' => (float) $detail->bobot_realisasi
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'chart_data' => $chartData,
                'summary' => [
                    'rencana_pct' => $rencanaPct,
                    'realisasi_pct' => $realisasiPct,
                    'deviasi_pct' => $deviasiPct
                ],
                'master_minggu' => $masterMinggu->map(fn($m) => [
                    'id' => $m->id,
                    'kode' => $m->kode_minggu,
                    'tanggal' => $m->tanggal_awal->format('d M') . ' - ' . $m->tanggal_akhir->format('d M')
                ]),
                'items' => $this->formatItemsProgress($items, $progressDetailsMap),
                'po_info' => [
                    'nomor_po' => $po->nomor_po,
                    'pelaksana' => $po->pelaksana,
                    'nilai_po' => $po->nilai_po
                ]
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error apiGetProgressData', [
            'pekerjaan_id' => $pekerjaanId,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data progress: ' . $e->getMessage()
        ], 500);
    }
}
private function formatItemsProgress($items, $progressDetailsMap, $level = 0)
{
    $result = [];
    
    foreach ($items as $item) {
        $nama = $item->jenis_pekerjaan_utama ?: 
                ($item->sub_pekerjaan ?: 
                ($item->sub_sub_pekerjaan ?: 'Item Pekerjaan'));
        
        $progressData = $progressDetailsMap[$item->id] ?? [];
        
        $result[] = [
            'id' => $item->id,
            'kode' => $item->kode_pekerjaan,
            'nama' => $nama,
            'volume' => $item->volume,
            'satuan' => $item->sat,
            'bobot' => $item->bobot,
            'level' => $level,
            'has_children' => $item->children->count() > 0,
            'progress_data' => $progressData // { minggu_id: { rencana, realisasi } }
        ];
        
        if ($item->children->count() > 0) {
            $childItems = $this->formatItemsProgress($item->children, $progressDetailsMap, $level + 1);
            $result = array_merge($result, $childItems);
        }
    }
    
    return $result;
}


// PROFILE
/**
 * VENDOR PROFILE - View Profile
 */
public function vendorProfile()
{
    try {
        $user = Auth::user();
        $profile = $user->profile;
        
        Log::info('Vendor Profile Accessed', [
            'user_id' => $user->id,
            'name' => $user->name
        ]);
        
        return view('LandingPage.vendor-profile', compact('user', 'profile'));
        
    } catch (\Exception $e) {
        Log::error('Error vendorProfile', [
            'error' => $e->getMessage()
        ]);
        
        return redirect()->route('landingpage.index')
            ->with('error', 'Gagal memuat profil');
    }
}

/**
 * VENDOR PROFILE - Edit Profile Form
 */
public function vendorProfileEdit()
{
    try {
        $user = Auth::user();
        $profile = $user->profile;
        
        return view('LandingPage.vendor-profile-edit', compact('user', 'profile'));
        
    } catch (\Exception $e) {
        Log::error('Error vendorProfileEdit', [
            'error' => $e->getMessage()
        ]);
        
        return redirect()->route('landingpage.profile')
            ->with('error', 'Gagal memuat form edit profil');
    }
}

public function vendorProfileUpdate(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string'
        ]);
        
        $userId = Auth::id();
        
        // ✅ Update user name menggunakan DB query
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'name' => $validated['name'],
                'updated_at' => now()
            ]);
        
        // ✅ Cek apakah profile sudah ada
        $profileExists = DB::table('profiles')
            ->where('user_id', $userId)
            ->exists();
        
        if ($profileExists) {
            // Update existing profile
            DB::table('profiles')
                ->where('user_id', $userId)
                ->update([
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'agama' => $validated['agama'],
                    'jabatan' => $validated['jabatan'],
                    'nomor_telepon' => $validated['nomor_telepon'],
                    'alamat' => $validated['alamat'],
                    'updated_at' => now()
                ]);
        } else {
            // Create new profile
            DB::table('profiles')->insert([
                'user_id' => $userId,
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'agama' => $validated['agama'],
                'jabatan' => $validated['jabatan'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'alamat' => $validated['alamat'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        Log::info('Vendor Profile Updated', [
            'user_id' => $userId,
            'name' => $validated['name']
        ]);
        
        return redirect()->route('landingpage.profile')
            ->with('success', 'Profil berhasil diperbarui');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
        
    } catch (\Exception $e) {
        Log::error('Error vendorProfileUpdate', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * VENDOR PROFILE - Edit Password Form
 */
public function vendorPasswordEdit()
{
    try {
        $user = Auth::user();
        
        return view('LandingPage.vendor-password', compact('user'));
        
    } catch (\Exception $e) {
        Log::error('Error vendorPasswordEdit', [
            'error' => $e->getMessage()
        ]);
        
        return redirect()->route('landingpage.profile')
            ->with('error', 'Gagal memuat form ubah password');
    }
}

/**
 * VENDOR PROFILE - Update Password
 */
public function vendorPasswordUpdate(Request $request)
{
    try {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);
        
        $userId = Auth::id();
        
        // ✅ Get current password from database
        $currentPassword = DB::table('users')
            ->where('id', $userId)
            ->value('password');
        
        // Cek password lama
        if (!Hash::check($validated['current_password'], $currentPassword)) {
            return redirect()->back()
                ->with('error', 'Password lama tidak sesuai')
                ->withInput();
        }
        
        // ✅ Update password menggunakan DB query
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'password' => Hash::make($validated['new_password']),
                'updated_at' => now()
            ]);
        
        Log::info('Vendor Password Updated', [
            'user_id' => $userId
        ]);
        
        return redirect()->route('landingpage.profile')
            ->with('success', 'Password berhasil diubah');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
        
    } catch (\Exception $e) {
        Log::error('Error vendorPasswordUpdate', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', 'Gagal mengubah password: ' . $e->getMessage());
    }
}
// END PROFILE

// wilayah api
public function apiGetWilayah()
{
    try {
        $wilayah = \App\Models\Wilayah::whereHas('pekerjaans')
            ->withCount('pekerjaans') 
            ->orderBy('nama', 'asc') 
            ->get(['id', 'nama']); 
        
        Log::info('API Get Wilayah', [
            'count' => $wilayah->count(),
            'data' => $wilayah->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $wilayah->map(function($w) {
                return [
                    'id' => $w->id,
                    'nama' => $w->nama, 
                    'jumlah_pekerjaan' => $w->pekerjaans_count
                ];
            })
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error apiGetWilayah', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data wilayah: ' . $e->getMessage()
        ], 500);
    }
}
public function apiGetPekerjaanByWilayah($wilayahId)
{
    try {
        Log::info('API Get Pekerjaan by Wilayah', [
            'wilayah_id' => $wilayahId
        ]);
        
        $pekerjaans = \App\Models\Pekerjaan::where('wilayah_id', $wilayahId)
            ->with('wilayah:id,nama')
            ->orderBy('nama_investasi', 'asc')
            ->get(['id', 'nama_investasi', 'wilayah_id']);
        
        Log::info('Pekerjaan found', [
            'count' => $pekerjaans->count()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $pekerjaans
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error apiGetPekerjaanByWilayah', [
            'wilayah_id' => $wilayahId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data pekerjaan: ' . $e->getMessage()
        ], 500);
    }
}
// end wilayah api
}