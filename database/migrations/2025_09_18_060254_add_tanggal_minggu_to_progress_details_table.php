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
       Schema::table('progress_details', function (Blueprint $table) {
            if (!Schema::hasColumn('progress_details', 'tanggal_awal_minggu')) {
                $table->date('tanggal_awal_minggu')->nullable()->after('minggu');
            }

            if (!Schema::hasColumn('progress_details', 'tanggal_akhir_minggu')) {
                $table->date('tanggal_akhir_minggu')->nullable()->after('tanggal_awal_minggu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_details', function (Blueprint $table) {
            if (Schema::hasColumn('progress_details', 'tanggal_awal_minggu')) {
                $table->dropColumn('tanggal_awal_minggu');
            }
            if (Schema::hasColumn('progress_details', 'tanggal_akhir_minggu')) {
                $table->dropColumn('tanggal_akhir_minggu');
            }
        });
    }
};