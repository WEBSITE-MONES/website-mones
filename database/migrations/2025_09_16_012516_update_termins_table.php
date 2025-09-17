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
        Schema::table('termins', function (Blueprint $table) {
        $table->renameColumn('nama', 'uraian');
        $table->decimal('nilai_pembayaran', 15, 2)->nullable()->after('persentase'); // hasil perhitungan persentase x nilai PO
        $table->dropColumn('tanggal'); // karena tanggal sudah nggak dipakai
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termins', function (Blueprint $table) {
            $table->renameColumn('uraian', 'nama');
            $table->date('tanggal')->nullable();
        });
    }
};