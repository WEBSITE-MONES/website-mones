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
        Schema::create('rkap_pekerjaan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->onDelete('cascade');
    $table->year('tahun');
    $table->bigInteger('nilai')->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rkap_pekerjaan');
    }
};