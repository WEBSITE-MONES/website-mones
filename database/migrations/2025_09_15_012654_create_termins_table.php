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
        Schema::create('termins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('pos')->onDelete('cascade');
            $table->string('nama'); // Nama termin
            $table->decimal('persentase', 5, 2); // Misal 50.00 untuk 50%
            $table->date('tanggal'); // Tanggal termin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('termins');
    }
};