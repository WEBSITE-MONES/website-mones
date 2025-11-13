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
        Schema::create('laporan_approval_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role_approval'); // manager_teknik, assisten_manager, dll
            $table->string('nama_approver');
            $table->string('jabatan')->nullable();
            $table->integer('urutan')->default(1); // Urutan approval
            $table->string('tanda_tangan')->nullable(); // Path file tanda tangan
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index
            $table->index('user_id');
            $table->index('role_approval');
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_approval_settings');
    }
};