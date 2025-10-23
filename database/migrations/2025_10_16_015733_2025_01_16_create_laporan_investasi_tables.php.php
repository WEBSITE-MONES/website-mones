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
        // Tabel Laporan Investasi
        Schema::create('laporan_investasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan')->unique(); // Contoh: LI-2025-01
            $table->enum('jenis_laporan', ['rekap_activa', 'rekap_rincian'])->default('rekap_rincian');
            $table->integer('tahun');
            $table->integer('bulan'); // 1-12
            $table->string('periode_label'); // "Laporan s.d Januari"
            $table->enum('status_approval', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
            $table->timestamp('tanggal_disubmit')->nullable();
            $table->timestamp('tanggal_approved')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tahun', 'bulan', 'jenis_laporan']);
        });

        // Tabel Approval Laporan
        Schema::create('laporan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_investasi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role_approval', ['manager_teknik', 'assisten_manager']); // Sesuaikan dengan role Anda
            $table->string('nama_approver');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('komentar')->nullable();
            $table->timestamp('tanggal_approval')->nullable();
            $table->integer('urutan')->default(1); // Urutan approval (1: Manager, 2: Assisten)
            $table->timestamps();

            $table->index(['laporan_id', 'urutan']);
        });

        // Tabel Detail Laporan (optional - untuk menyimpan snapshot data)
        Schema::create('laporan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_investasi')->cascadeOnDelete();
            $table->string('coa');
            $table->string('nomor_prodef_sap')->nullable();
            $table->text('nama_investasi')->nullable();
            $table->text('uraian_pekerjaan')->nullable();
            $table->decimal('total_volume', 20, 2)->default(0);
            $table->decimal('nilai_rkap', 20, 2)->default(0);
            $table->decimal('target_sd_bulan', 20, 2)->default(0);
            $table->string('nomor_po')->nullable();
            $table->date('tanggal_po')->nullable();
            $table->string('pelaksana')->nullable();
            $table->date('mulai_kontrak')->nullable();
            $table->date('selesai_kontrak')->nullable();
            $table->decimal('realisasi_fisik', 5, 2)->default(0);
            $table->decimal('realisasi_pembayaran', 20, 2)->default(0);
            $table->timestamps();

            $table->index(['laporan_id', 'coa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_details');
        Schema::dropIfExists('laporan_approvals');
        Schema::dropIfExists('laporan_investasi');
    }
};