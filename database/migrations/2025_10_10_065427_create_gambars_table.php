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
        Schema::create('gambars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->onDelete('cascade');
        $table->string('keterangan'); // DED, Shop Drawing, As Built
        $table->string('file');
        $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
        $table->date('tanggal_upload')->nullable(); // ✅ tambah ini
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambars');
    }
};