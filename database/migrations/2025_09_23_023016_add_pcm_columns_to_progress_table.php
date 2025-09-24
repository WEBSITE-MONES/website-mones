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
        Schema::table('progress', function (Blueprint $table) {
        $table->string('nomor_pcm_mulai_kerja')->nullable()->after('file_ba');
        $table->date('tanggal_pcm_mulai_kerja')->nullable()->after('nomor_pcm_mulai_kerja');
        $table->string('file_pcm')->nullable()->after('tanggal_pcm_mulai_kerja');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
        $table->dropColumn([
            'nomor_pcm_mulai_kerja',
            'tanggal_pcm_mulai_kerja',
            'file_pcm',
        ]);
    });
    }
};