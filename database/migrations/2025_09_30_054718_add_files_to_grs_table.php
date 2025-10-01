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
       Schema::table('grs', function (Blueprint $table) {
        $table->string('file_ba_pemeriksaan')->nullable();
        $table->string('file_ba_serah_terima')->nullable();
        $table->string('file_ba_pembayaran')->nullable();
        $table->string('file_laporan_dokumentasi')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grs', function (Blueprint $table) {
        $table->dropColumn([
            'file_ba_pemeriksaan',
            'file_ba_serah_terima',
            'file_ba_pembayaran',
            'file_laporan_dokumentasi',
        ]);
    });
    }
};