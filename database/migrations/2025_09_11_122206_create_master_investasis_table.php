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
        Schema::create('master_investasis', function (Blueprint $table) {
            $table->id();
            $table->string('tipe');          
            $table->string('coa_sub');       
            $table->string('kategori');     
            $table->string('manfaat');       
            $table->string('jenis');         
            $table->string('sifat');         
            $table->string('urgensi');       
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_investasis');
    }
};