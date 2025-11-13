<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom di tabel laporan_approval_settings
        Schema::table('laporan_approval_settings', function (Blueprint $table) {
            $table->string('signature_id')->unique()->nullable()->after('tanda_tangan');
            $table->string('qr_code_path')->nullable()->after('signature_id');
            $table->string('signature_hash')->nullable()->after('qr_code_path');
            $table->enum('signature_type', ['manual', 'qr', 'hybrid'])->default('manual')->after('signature_hash');
        });
        
        // Tabel untuk tracking digital signature
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->uuid('signature_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role_approval');
            $table->string('nama_approver');
            $table->string('jabatan')->nullable();
            $table->string('signature_hash');
            $table->string('algorithm')->default('SHA-256');
            $table->string('qr_code_path')->nullable();
            $table->string('original_signature_path')->nullable();
            $table->string('hybrid_signature_path')->nullable();
            $table->json('metadata')->nullable(); // Store additional verification data
            $table->boolean('is_revoked')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'role_approval']);
            $table->index('signature_hash');
        });
        
        // Tabel untuk log verifikasi signature
        Schema::create('signature_verifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('signature_id');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('verification_result', ['valid', 'invalid', 'revoked', 'expired']);
            $table->json('verification_details')->nullable();
            $table->timestamps();
            
            $table->index('signature_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_verifications');
        Schema::dropIfExists('digital_signatures');
        
        Schema::table('laporan_approval_settings', function (Blueprint $table) {
            $table->dropColumn(['signature_id', 'qr_code_path', 'signature_hash', 'signature_type']);
        });
    }
};