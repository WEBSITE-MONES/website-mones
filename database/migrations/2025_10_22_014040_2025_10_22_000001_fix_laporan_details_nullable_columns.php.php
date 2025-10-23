<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration untuk fix kolom yang tidak boleh NULL
 * Filename: 2025_10_22_014040_fix_laporan_details_nullable_columns.php
 * 
 * Jalankan dengan: php artisan migrate
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update NULL values dulu sebelum alter table
        DB::statement("UPDATE laporan_details SET total_volume = 0 WHERE total_volume IS NULL");
        DB::statement("UPDATE laporan_details SET nilai_rkap = 0 WHERE nilai_rkap IS NULL");
        DB::statement("UPDATE laporan_details SET target_sd_bulan = 0 WHERE target_sd_bulan IS NULL");
        DB::statement("UPDATE laporan_details SET realisasi_fisik = 0 WHERE realisasi_fisik IS NULL");
        DB::statement("UPDATE laporan_details SET realisasi_pembayaran = 0 WHERE realisasi_pembayaran IS NULL");
        
        DB::statement("UPDATE laporan_details SET coa = '-' WHERE coa IS NULL OR coa = ''");
        DB::statement("UPDATE laporan_details SET nomor_prodef_sap = '-' WHERE nomor_prodef_sap IS NULL OR nomor_prodef_sap = ''");
        DB::statement("UPDATE laporan_details SET nama_investasi = '-' WHERE nama_investasi IS NULL OR nama_investasi = ''");
        DB::statement("UPDATE laporan_details SET uraian_pekerjaan = '-' WHERE uraian_pekerjaan IS NULL OR uraian_pekerjaan = ''");
        DB::statement("UPDATE laporan_details SET nomor_po = '-' WHERE nomor_po IS NULL OR nomor_po = ''");
        DB::statement("UPDATE laporan_details SET pelaksana = '-' WHERE pelaksana IS NULL OR pelaksana = ''");

        // Alter table - TEXT column tidak bisa punya default value di MySQL
        // Jadi kita ubah jadi VARCHAR atau biarkan nullable
        Schema::table('laporan_details', function (Blueprint $table) {
            // Ubah TEXT jadi VARCHAR(500) agar bisa punya default
            $table->string('uraian_pekerjaan', 500)->default('-')->change();
            
            // Set default untuk kolom lain
            $table->string('coa', 50)->default('-')->change();
            $table->string('nomor_prodef_sap', 100)->default('-')->change();
            $table->string('nama_investasi', 255)->default('-')->change();
            $table->string('nomor_po', 255)->default('-')->change();
            $table->string('pelaksana', 255)->default('-')->change();
        });
        
        // Untuk kolom date, kita biarkan nullable karena memang optional
        // mulai_kontrak dan selesai_kontrak tetap nullable
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_details', function (Blueprint $table) {
            $table->text('uraian_pekerjaan')->nullable()->change();
            $table->string('coa', 50)->nullable()->change();
            $table->string('nomor_prodef_sap', 100)->nullable()->change();
            $table->string('nama_investasi', 255)->nullable()->change();
            $table->string('nomor_po', 255)->nullable()->change();
            $table->string('pelaksana', 255)->nullable()->change();
        });
    }
};