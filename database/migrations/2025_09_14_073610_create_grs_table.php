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
            $table->unsignedBigInteger('po_id'); // relasi ke PO
            $table->string('nomor_ba_mulai_kerja')->nullable();
            $table->date('tanggal_ba_mulai_kerja')->nullable();
            $table->integer('progress')->default(0); // progres dalam persen
            $table->string('bulan')->nullable(); // Januari, Februari, dst
            $table->string('file_ba')->nullable(); // simpan nama file upload
            $table->timestamps();

            $table->foreign('po_id')->references('id')->on('pos')->onDelete('cascade');
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