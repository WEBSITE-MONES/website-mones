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
        Schema::create('pekerjaan_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->enum('assigned_by', ['auto', 'manual'])->default('auto');
            $table->timestamps();
            
            // Unique constraint: satu user tidak bisa di-assign ke pekerjaan yang sama 2x
            $table->unique(['user_id', 'pekerjaan_id']);
            
            // Index untuk performa query
            $table->index('user_id');
            $table->index('pekerjaan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pekerjaan_user');
    }
};