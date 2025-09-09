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
        Schema::create('progress_fisik', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pekerjaan_id');  // relasi ke pekerjaan
            $table->date('bulan');                      
            $table->decimal('rencana', 5, 2)->nullable();
            $table->decimal('realisasi', 5, 2)->nullable();
            $table->decimal('defiasi', 5, 2)->nullable(); //  realisasi - rencana
            $table->timestamps();

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
        Schema::dropIfExists('progress_fisik');
    }
};