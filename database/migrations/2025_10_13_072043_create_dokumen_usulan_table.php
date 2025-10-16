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
        Schema::create('dokumen_usulan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pekerjaan_id'); 
            $table->string('keterangan');       
            $table->string('kategori');                 
            $table->date('tanggal_upload');                    
            $table->timestamps();

            // relasi foreign key
            $table->foreign('pekerjaan_id')
                  ->references('id')
                  ->on('pekerjaan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_usulan');
    }
};