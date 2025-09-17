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
         Schema::table('prs', function (Blueprint $table) {
            // Ubah kolom jenis_pekerjaan dari JSON ke string (100 chars)
            $table->string('jenis_pekerjaan', 100)->change();

            // Tambah kolom tahun_anggaran (default tahun berjalan)
            $table->integer('tahun_anggaran')
                  ->default(date('Y'))
                  ->after('jenis_pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prs', function (Blueprint $table) {
            // Balikkan jenis_pekerjaan ke JSON kalau rollback
            $table->json('jenis_pekerjaan')->change();

            // Hapus kolom tahun_anggaran
            $table->dropColumn('tahun_anggaran');
        });
    }
};