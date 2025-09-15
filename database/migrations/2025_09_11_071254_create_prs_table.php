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
        Schema::create('prs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_pekerjaan', ['Konsultan Perencana', 'Pelaksanaan Fisik', 'Konsultan Pengawas']);
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->onDelete('cascade');
            $table->decimal('nilai_pr', 20, 2)->default(0);
            $table->string('nomor_pr')->unique();
            $table->date('tanggal_pr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prs');
    }
};