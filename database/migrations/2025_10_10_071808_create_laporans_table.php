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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pekerjaan_id'); 
            $table->string('keterangan'); 
            $table->string('file_laporan'); 
            $table->date('tanggal_upload')->nullable(); 
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu'); // status laporan
            $table->timestamps();

            // relasi ke tabel pekerjaan
            $table->foreign('pekerjaan_id')->references('id')->on('pekerjaan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};