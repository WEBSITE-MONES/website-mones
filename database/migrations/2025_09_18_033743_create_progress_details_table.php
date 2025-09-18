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
        Schema::create('progress_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_id')->constrained('progress')->onDelete('cascade');
            $table->integer('minggu'); // M1, M2, dst
            $table->decimal('rencana', 8, 2)->default(0);   // rencana %
            $table->decimal('realisasi', 8, 2)->default(0); // realisasi %
            $table->timestamps();

            $table->unique(['progress_id', 'minggu']); // biar nggak dobel minggu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_details');
    }
};