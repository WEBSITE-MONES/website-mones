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
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'On Progress', 'Selesai'])
                  ->default('Pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->enum('status', ['Perencanaan', 'Sedang Berjalan', 'Selesai'])
                  ->default('Perencanaan')
                  ->change();
        });
    }
};