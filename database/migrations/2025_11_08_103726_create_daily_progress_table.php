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
        Schema::create('daily_progress', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('po_id')->constrained('pos')->onDelete('cascade');
            $table->foreignId('pekerjaan_item_id')->constrained('pekerjaan_items')->onDelete('cascade');
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade');
            
            // Basic Info
            $table->date('tanggal');
            $table->string('jenis_pekerjaan');
            
            // Volume & Progress
            $table->decimal('volume_realisasi', 15, 2)->default(0);
            $table->string('satuan', 50)->nullable();
            $table->text('deskripsi');
            
            // Resources
            $table->integer('jumlah_pekerja')->nullable();
            $table->string('alat_berat')->nullable();
            $table->text('material')->nullable();
            
            // Weather
            $table->decimal('cuaca_suhu', 5, 1)->nullable();
            $table->string('cuaca_deskripsi')->nullable();
            $table->integer('cuaca_kelembaban')->nullable();
            
            // Field Conditions
            $table->decimal('jam_kerja', 5, 1)->nullable();
            $table->enum('kondisi_lapangan', ['normal', 'becek', 'kering', 'licin'])->default('normal');
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            
            // Documentation
            $table->json('foto')->nullable();
            $table->decimal('gps_latitude', 10, 8)->nullable();
            $table->decimal('gps_longitude', 11, 8)->nullable();
            
            // Planning
            $table->text('rencana_besok');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['po_id', 'tanggal']);
            $table->index(['pekerjaan_item_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_progress');
    }
};