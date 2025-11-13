<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanApprovalSetting;
use App\Models\DigitalSignature;
use App\Models\User;
use App\Services\DigitalSignatureService;

class LaporanApprovalSettingController extends Controller
{
    protected $signatureService;

    public function __construct(DigitalSignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    public function index()
    {
        $approvalSettings = LaporanApprovalSetting::with(['user', 'digitalSignature'])
            ->ordered()
            ->get();

        return view('Dashboard.Pekerjaan.Realisasi.Laporan.approval_settings', compact('approvalSettings'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'is_active' => $request->has('is_active') ? true : false
        ]);
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'role_approval' => 'required|string|max:100',
            'nama_approver' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1',
            'signature_type' => 'required|in:manual,qr,hybrid',
            'is_active' => 'nullable|boolean',
        ];

        if ($request->signature_type === 'manual' || $request->signature_type === 'hybrid') {
            $rules['tanda_tangan'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
        } else {
            $rules['tanda_tangan'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $data = [
                'user_id' => $request->user_id,
                'role_approval' => $request->role_approval,
                'nama_approver' => $request->nama_approver,
                'jabatan' => $request->jabatan,
                'urutan' => $request->urutan,
                'is_active' => $request->has('is_active') ? true : false,
                'signature_type' => $request->signature_type,
            ];

            if ($request->hasFile('tanda_tangan')) {
                $file = $request->file('tanda_tangan');
                $filename = 'ttd_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('tanda_tangan', $filename, 'public');
                $data['tanda_tangan'] = $path;
            }

            if (in_array($request->signature_type, ['qr', 'hybrid'])) {
                $signatureData = [
                    'user_id' => $request->user_id,
                    'role_approval' => $request->role_approval,
                    'nama_approver' => $request->nama_approver,
                    'jabatan' => $request->jabatan,
                ];

                $metadata = $this->signatureService->generateSignatureMetadata($signatureData);
                $data['signature_id'] = $metadata['signature_id'];
                $data['signature_hash'] = $metadata['hash'];

                if ($request->signature_type === 'qr') {
                    $qrPath = $this->signatureService->generateQrCodeSignature(
                        $signatureData, 
                        $metadata['signature_id']
                    );
                    $data['qr_code_path'] = $qrPath;
                }

                if ($request->signature_type === 'hybrid') {
                    if (!isset($data['tanda_tangan'])) {
                        throw new \Exception('Tanda tangan harus diupload untuk tipe Hybrid');
                    }

                    $hybridPath = $this->signatureService->generateHybridSignature(
                        $data['tanda_tangan'],
                        $signatureData,
                        $metadata['signature_id']
                    );
                    $data['qr_code_path'] = $hybridPath;
                }

                $digitalSigData = [
                    'signature_id' => $metadata['signature_id'],
                    'user_id' => $request->user_id,
                    'role_approval' => $request->role_approval,
                    'nama_approver' => $request->nama_approver,
                    'jabatan' => $request->jabatan,
                    'signature_hash' => $metadata['hash'],
                    'algorithm' => $metadata['algorithm'] ?? 'SHA-256',
                    'qr_code_path' => $data['qr_code_path'] ?? null,
                    'original_signature_path' => $data['tanda_tangan'] ?? null,
                    'hybrid_signature_path' => ($request->signature_type === 'hybrid') ? $data['qr_code_path'] : null,
                    'metadata' => [
                        'created_by' => auth()->user()->name ?? 'System',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ],
                ];
                
                DigitalSignature::create($digitalSigData);
            }

            LaporanApprovalSetting::create($data);

            DB::commit();

            return redirect()->route('laporan.approval-settings.index')
                ->with('success', 'Approval setting berhasil ditambahkan dengan digital signature!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Gagal menambahkan approval setting: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'is_active' => $request->has('is_active') ? true : false
        ]);
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'role_approval' => 'required|string|max:100',
            'nama_approver' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1',
            'signature_type' => 'required|in:manual,qr,hybrid',
            'is_active' => 'nullable|boolean',
        ];

        if ($request->signature_type === 'manual') {
            $rules['tanda_tangan'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
        } elseif ($request->signature_type === 'hybrid') {
            $setting = LaporanApprovalSetting::findOrFail($id);
            if (!$setting->tanda_tangan && !$request->hasFile('tanda_tangan')) {
                $rules['tanda_tangan'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
            } else {
                $rules['tanda_tangan'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
            }
        } else {
            $rules['tanda_tangan'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $setting = LaporanApprovalSetting::findOrFail($id);

            $data = $request->only([
                'user_id',
                'role_approval',
                'nama_approver',
                'jabatan',
                'urutan',
            ]);

            $data['is_active'] = $request->has('is_active') ? true : false;
            $data['signature_type'] = $request->signature_type;

            if ($request->hasFile('tanda_tangan')) {
                if ($setting->tanda_tangan && Storage::disk('public')->exists($setting->tanda_tangan)) {
                    Storage::disk('public')->delete($setting->tanda_tangan);
                }

                $file = $request->file('tanda_tangan');
                $filename = 'ttd_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('tanda_tangan', $filename, 'public');
                $data['tanda_tangan'] = $path;
            }

            if ($request->signature_type !== 'manual') {
                if ($setting->signature_id) {
                    $oldSignature = DigitalSignature::where('signature_id', $setting->signature_id)->first();
                    if ($oldSignature) {
                        $oldSignature->revoke('Updated signature configuration');
                    }
                }

                $signatureData = [
                    'user_id' => $request->user_id,
                    'role_approval' => $request->role_approval,
                    'nama_approver' => $request->nama_approver,
                    'jabatan' => $request->jabatan,
                ];

                $metadata = $this->signatureService->generateSignatureMetadata($signatureData);

                if ($request->signature_type === 'qr') {
                    $qrPath = $this->signatureService->generateQrCodeSignature($signatureData, $metadata['signature_id']);
                    $data['qr_code_path'] = $qrPath;
                } elseif ($request->signature_type === 'hybrid') {
                    $signaturePath = $data['tanda_tangan'] ?? $setting->tanda_tangan;

                    if (!$signaturePath) {
                        throw new \Exception('Tanda tangan harus ada untuk tipe Hybrid');
                    }

                    $hybridPath = $this->signatureService->generateHybridSignature($signaturePath, $signatureData, $metadata['signature_id']);
                    $data['qr_code_path'] = $hybridPath;
                }

                $data['signature_id'] = $metadata['signature_id'];
                $data['signature_hash'] = $metadata['hash'];

                DigitalSignature::create([
                    'signature_id' => $metadata['signature_id'],
                    'user_id' => $request->user_id,
                    'role_approval' => $request->role_approval,
                    'nama_approver' => $request->nama_approver,
                    'jabatan' => $request->jabatan,
                    'signature_hash' => $metadata['hash'],
                    'algorithm' => $metadata['algorithm'] ?? 'SHA-256',
                    'qr_code_path' => $data['qr_code_path'] ?? null,
                    'original_signature_path' => $data['tanda_tangan'] ?? $setting->tanda_tangan,
                    'hybrid_signature_path' => ($request->signature_type === 'hybrid') ? $data['qr_code_path'] : null,
                    'metadata' => [
                        'updated_by' => auth()->user()->name,
                        'ip_address' => request()->ip(),
                    ],
                ]);
            }

            $setting->update($data);

            DB::commit();

            return redirect()->route('laporan.approval-settings.index')
                ->with('success', 'Approval setting berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update approval setting: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $setting = LaporanApprovalSetting::findOrFail($id);

            if ($setting->signature_id) {
                $signature = DigitalSignature::where('signature_id', $setting->signature_id)->first();
                if ($signature) {
                    $signature->revoke('Approval setting deleted');
                }
            }

            if ($setting->tanda_tangan && Storage::disk('public')->exists($setting->tanda_tangan)) {
                Storage::disk('public')->delete($setting->tanda_tangan);
            }
            if ($setting->qr_code_path && Storage::disk('public')->exists($setting->qr_code_path)) {
                Storage::disk('public')->delete($setting->qr_code_path);
            }

            $setting->delete();

            DB::commit();

            return redirect()->route('laporan.approval-settings.index')
                ->with('success', 'Approval setting berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus approval setting: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        try {
            $setting = LaporanApprovalSetting::findOrFail($id);
            $setting->update(['is_active' => !$setting->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah!',
                'is_active' => $setting->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifySignature($signatureId)
    {
        $result = $this->signatureService->verifySignature($signatureId);

        if (!$result || !$result['valid']) {
            return view('verification.invalid', compact('result'));
        }

        return view('verification.valid', compact('result'));
    }
}