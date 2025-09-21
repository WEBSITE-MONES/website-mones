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
        Schema::table('pekerjaan_items', function (Blueprint $table) {
            $table->decimal('jumlah_harga', 18, 2)->nullable()->after('harga_satuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('pekerjaan_items', function (Blueprint $table) {
            $table->dropColumn('jumlah_harga');
        });
    }
};