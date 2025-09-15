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
        
            $table->renameColumn('kebutuhan_dana', 'nilai_kontrak');

            // kalau misalnya 'nilai' kolom lama yang redundant, bisa drop
            $table->dropColumn('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->renameColumn('nilai_kontrak', 'kebutuhan_dana');
            $table->bigInteger('nilai')->nullable(); // kalau sebelumnya ada
        });
    }
};