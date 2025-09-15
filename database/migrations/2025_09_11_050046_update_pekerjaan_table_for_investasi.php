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
        Schema::table('pekerjaan', function (Blueprint $table) {
        // rename kolom lama
        $table->renameColumn('nama_pekerjaan', 'nama_investasi');
        $table->renameColumn('nilai_kontrak', 'kebutuhan_dana_2025');

        // tambah kolom baru
        $table->string('coa')->nullable();
        $table->string('program_investasi')->nullable();
        $table->string('tipe_investasi')->nullable();
        $table->string('nomor_prodef_sap')->nullable();
        $table->decimal('rkap_2025', 20, 2)->nullable();

        // kalau status & tanggal sudah tidak dipakai
        $table->dropColumn(['status', 'tanggal']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};