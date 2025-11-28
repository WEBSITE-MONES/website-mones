<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use App\Models\DigitalSignature;
use Imagick;

class DigitalSignatureService
{
    /**
     * Generate QR Code untuk tanda tangan digital
     * @param array $data - data metadata untuk dimasukkan ke QR
     * @param string|null $signatureId - gunakan signatureId yang sudah dibuat sebagai source-of-truth
     * @return string path (relative to disk 'public') of saved QR image (png or svg)
     * @throws \Exception
     */
    public function generateQrCodeSignature(array $data, ?string $signatureId = null): string
    {
        try {

            // gunakan signatureId yang diberikan jika ada; jika tidak, generate baru
            $uniqueId = $signatureId ?? Str::uuid()->toString();

            $qrData = [
                'signature_id' => $uniqueId,
                'nama_approver' => $data['nama_approver'] ?? 'N/A',
                'role' => $data['role_approval'] ?? 'N/A',
                'jabatan' => $data['jabatan'] ?? null,
                'timestamp' => now()->toIso8601String(),
                // gunakan parameter route tanpa nama key agar konsisten
                'verification_url' => route('verify.signature', $uniqueId)
            ];

            $qrContent = json_encode($qrData);

            // pastikan direktori ada
            if (!Storage::disk('public')->exists('signatures/qr')) {
                Storage::disk('public')->makeDirectory('signatures/qr', 0755, true);
            }

            // jika imagick tersedia, prefer PNG (bisa lebih universal & dipakai hybrid)
            $useImagick = extension_loaded('imagick');

            if ($useImagick) {
                $qrBinary = QrCode::format('png')
                    ->size(300)
                    ->margin(2)
                    ->errorCorrection('H')
                    ->generate($qrContent);

                $filename = 'qr_' . $uniqueId . '.png';
                $path = 'signatures/qr/' . $filename;

                $saved = Storage::disk('public')->put($path, $qrBinary);
                if (!$saved) {
                    throw new \Exception('Failed to save QR code to storage (png)');
                }
            } else {
                // fallback -> SVG (works without imagick)
                $qrSvg = QrCode::format('svg')
                    ->size(300)
                    ->margin(2)
                    ->generate($qrContent);

                $filename = 'qr_' . $uniqueId . '.svg';
                $path = 'signatures/qr/' . $filename;

                $saved = Storage::disk('public')->put($path, $qrSvg);
                if (!$saved) {
                    throw new \Exception('Failed to save QR code to storage (svg)');
                }
            }

            $fileExists = Storage::disk('public')->exists($path);

            if (!$fileExists) {
                throw new \Exception('QR code file not found after save');
            }

            return $path;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate tanda tangan hybrid (gambar + QR code).
     * Menggunakan $signatureId agar QR dan DB konsisten.
     *
     * @param string $signatureImagePath path relative di disk 'public' (mis. 'tanda_tangan/ttd_...')
     * @param array $data
     * @param string|null $signatureId
     * @return string path hybrid image di storage disk 'public'
     * @throws \Exception
     */
    public function generateHybridSignature(string $signatureImagePath, array $data, ?string $signatureId = null): string
    {
        try {

            // generate QR menggunakan signatureId yang sama (atau generate baru jika null)
            $qrPath = $this->generateQrCodeSignature($data, $signatureId);

            $signatureFullPath = Storage::disk('public')->path($signatureImagePath);
            $qrFullPath = Storage::disk('public')->path($qrPath);

            if (!file_exists($signatureFullPath)) {
                throw new \Exception('Signature image not found: ' . $signatureImagePath);
            }

            if (!file_exists($qrFullPath)) {
                throw new \Exception('QR code not found: ' . $qrPath);
            }

            // If QR is SVG and Imagick available, convert temporary SVG -> PNG for composition
            $qrIsSvg = strtolower(pathinfo($qrFullPath, PATHINFO_EXTENSION)) === 'svg';
            $tempPngQrFullPath = null;
            $usedQrFullPath = $qrFullPath;

            if ($qrIsSvg) {
                if (!extension_loaded('imagick')) {
                    // Hybrid needs a bitmap QR to compose with signature image
                    throw new \Exception('Hybrid signature requires Imagick (to convert SVG QR to PNG). Please install the imagick extension.');
                }

                // convert SVG -> PNG using Imagick
                $imagick = new Imagick();
                $svgContent = file_get_contents($qrFullPath);
                $imagick->readImageBlob($svgContent);
                $imagick->setImageFormat('png32');
                $imagick->setImageBackgroundColor('transparent');

                // Create temp png filename in same folder
                $tempPngFilename = 'qr_' . $signatureId . '_tmp.png';
                $tempPngRelative = 'signatures/qr/' . $tempPngFilename;
                $tempPngFullPath = Storage::disk('public')->path($tempPngRelative);

                // write image
                $imagick->writeImage($tempPngFullPath);
                $imagick->clear();
                $imagick->destroy();

                if (!file_exists($tempPngFullPath)) {
                    throw new \Exception('Failed to convert SVG QR to PNG for hybrid');
                }

                $tempPngQrFullPath = $tempPngFullPath;
                $usedQrFullPath = $tempPngFullPath;
            }

            // Detect dan load image signature
            $imageType = @exif_imagetype($signatureFullPath);
            switch ($imageType) {
                case IMAGETYPE_PNG:
                    $signature = imagecreatefrompng($signatureFullPath);
                    break;
                case IMAGETYPE_JPEG:
                    $signature = imagecreatefromjpeg($signatureFullPath);
                    break;
                default:
                    throw new \Exception('Unsupported signature image type. Use PNG or JPEG.');
            }

            if (!$signature) {
                throw new \Exception('Failed to load signature image');
            }

            // Load QR (now must be PNG)
            $qrCode = imagecreatefrompng($usedQrFullPath);
            if (!$qrCode) {
                if (isset($signature) && is_resource($signature)) {
                    imagedestroy($signature);
                }
                throw new \Exception('Failed to load QR code image for hybrid');
            }

            // Dapatkan dimensi
            $sigWidth = imagesx($signature);
            $sigHeight = imagesy($signature);
            $qrSize = 80;

            // Canvas
            $canvasWidth = $sigWidth + $qrSize + 20;
            $canvasHeight = max($sigHeight, $qrSize + 20);
            $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);

            // Transparansi
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
            imagefill($canvas, 0, 0, $transparent);
            imagealphablending($canvas, true);

            // Copy signature
            imagecopy($canvas, $signature, 10, 10, 0, 0, $sigWidth, $sigHeight);

            // Resize qr dan copy
            $qrResized = imagecreatetruecolor($qrSize, $qrSize);
            imagealphablending($qrResized, false);
            imagesavealpha($qrResized, true);
            imagefill($qrResized, 0, 0, $transparent);
            imagealphablending($qrResized, true);

            imagecopyresampled($qrResized, $qrCode, 0, 0, 0, 0, $qrSize, $qrSize, imagesx($qrCode), imagesy($qrCode));
            imagecopy($canvas, $qrResized, $sigWidth + 10, 10, 0, 0, $qrSize, $qrSize);

            // Save hybrid image
            $filename = 'hybrid_' . Str::uuid() . '.png';
            $path = 'signatures/hybrid/' . $filename;
            $fullPath = Storage::disk('public')->path($path);

            // buat directory jika perlu
            $dir = dirname($fullPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $saveSuccess = imagepng($canvas, $fullPath, 9);
            if (!$saveSuccess) {
                // cleanup temp png if created
                if ($tempPngQrFullPath && file_exists($tempPngQrFullPath)) {
                    @unlink($tempPngQrFullPath);
                }
                throw new \Exception('Failed to save hybrid signature');
            }

            // Cleanup resource
            imagedestroy($signature);
            imagedestroy($qrCode);
            imagedestroy($qrResized);
            imagedestroy($canvas);

            // Hapus temporary QR (opsional)
            try {
                // jika kita membuat temporary PNG dari SVG, hapus temp PNG
                if ($tempPngQrFullPath && file_exists($tempPngQrFullPath)) {
                    @unlink($tempPngQrFullPath);
                }
            } catch (\Throwable $t) {
            }

            return $path;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify signature dari QR Code
     */
    public function verifySignature(string $signatureId): ?array
    {
        try {

            $signature = DigitalSignature::where('signature_id', $signatureId)->first();

            if (!$signature) {

                return [
                    'valid' => false,
                    'reason' => 'Signature not found',
                ];
            }

            if ($signature->is_revoked) {
                $signature->logVerification('revoked', [
                    'revoked_at' => $signature->revoked_at,
                    'reason' => $signature->revoked_reason
                ]);

                return [
                    'valid' => false,
                    'reason' => 'Signature has been revoked',
                    'revoked_at' => $signature->revoked_at,
                    'revoked_reason' => $signature->revoked_reason
                ];
            }

            $signature->logVerification('valid', [
                'verified_by_ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return [
                'valid' => true,
                'signature_id' => $signature->signature_id,
                'nama_approver' => $signature->nama_approver,
                'role' => $signature->role_approval,
                'jabatan' => $signature->jabatan,
                'created_at' => $signature->created_at,
                'laporan_count' => $signature->laporanApprovals()->count(),
                'verification_count' => $signature->verifications()->count()
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate signature metadata
     */
    public function generateSignatureMetadata(array $data): array
    {
        $metadata = [
            'signature_id' => Str::uuid()->toString(),
            'hash' => hash('sha256', json_encode($data) . now()->timestamp),
            'algorithm' => 'SHA-256',
            'created_at' => now(),
        ];
        return $metadata;
    }
}