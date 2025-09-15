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
            // Ubah nama kolom kebutuhan_dana_2025 menjadi kebutuhan_dana
            $table->renameColumn('kebutuhan_dana_2025', 'kebutuhan_dana');

            // Ubah nama kolom rkap_2025 menjadi rkap
            $table->renameColumn('rkap_2025', 'rkap');

            // Tambahkan kolom Tahun Usulan (tipe year)
            $table->year('tahun_usulan')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            // Rollback perubahan
            $table->renameColumn('kebutuhan_dana', 'kebutuhan_dana_2025');
            $table->renameColumn('rkap', 'rkap_2025');
            $table->dropColumn('tahun_usulan');
        });
    }
};