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
        Schema::create('grs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('prs')->onDelete('cascade');
            $table->date('tanggal_gr');
            $table->string('nomor_gr')->unique();
            $table->decimal('nilai_gr', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grs');
    }
};