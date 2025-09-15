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
        Schema::create('pos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pr_id')->constrained('prs')->onDelete('cascade');
    $table->string('nomor_po')->unique();
    $table->string('nomor_kontrak')->nullable();
    $table->decimal('nilai_po', 15, 2);
    $table->string('estimated')->nullable();
    $table->string('waktu_pelaksanaan')->nullable();
    $table->string('pelaksana')->nullable();
    $table->enum('mekanisme_pembayaran', ['Uang muka', 'Termin'])->nullable();
    $table->date('tanggal_po');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos');
    }
};