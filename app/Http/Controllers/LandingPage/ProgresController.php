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
use App\Models\DailyProgress;
use Illuminate\Support\Facades\Http; 

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
        
        // Load pekerjaan dengan relasi
        $pekerjaan = Pekerjaan::with('wilayah')
            ->orderBy('nama_investasi', 'asc')
            ->get();
        
        return view('LandingPage.pelaporan-form', compact('user', 'pekerjaan'));
    }
    
    public function pelaporanformedit()
    {
        $user = Auth::user();
        return view('LandingPage.pelaporan-form_edit', compact('user'));
    }
    
    public function dokumentasi()
    {
        $user = Auth::user();
        return view('LandingPage.dokumentasi', compact('user'));
    }
    
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
                'pekerjaan' => 'required|exists:pekerjaan,id',  // ✅ Fixed: pekerjaan (bukan pekerjaans)
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
}