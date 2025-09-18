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
        Schema::table('progress', function (Blueprint $table) {
            // hapus kolom lama
            $table->dropColumn(['progress', 'bulan']);

            // tambahkan kolom baru
            $table->string('jenis_pekerjaan')->after('po_id');
            $table->string('sub_pekerjaan')->after('jenis_pekerjaan');
            $table->decimal('volume', 12, 2)->nullable()->after('sub_pekerjaan');
            $table->string('satuan', 20)->nullable()->after('volume');
            $table->decimal('bobot', 8, 2)->default(0)->after('satuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progresses', function (Blueprint $table) {
            // rollback: hapus kolom baru
            $table->dropColumn(['jenis_pekerjaan', 'sub_pekerjaan', 'volume', 'satuan', 'bobot']);

            // balikin kolom lama
            $table->string('progress')->nullable();
            $table->string('bulan')->nullable();
        });
    }
};