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
         Schema::table('master_minggu', function (Blueprint $table) {
            // Hapus foreign key kalau ada
            try {
                $table->dropForeign('fk_masterminggu_po');
            } catch (\Exception $e) {
                // kalau FK sudah nggak ada, skip
            }

            // Baru hapus kolom po_id
            if (Schema::hasColumn('master_minggu', 'po_id')) {
                $table->dropColumn('po_id');
            }

            // Tambah progress_id
            if (!Schema::hasColumn('master_minggu', 'progress_id')) {
                $table->unsignedBigInteger('progress_id')->after('id');
                $table->foreign('progress_id')
                      ->references('id')
                      ->on('progress')
                      ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_minggu', function (Blueprint $table) {
            if (Schema::hasColumn('master_minggu', 'progress_id')) {
                $table->dropForeign(['progress_id']);
                $table->dropColumn('progress_id');
            }

            // Balikin po_id + foreign key
            if (!Schema::hasColumn('master_minggu', 'po_id')) {
                $table->unsignedBigInteger('po_id')->nullable()->after('id');
                $table->foreign('po_id', 'fk_masterminggu_po')
                      ->references('id')
                      ->on('pos')
                      ->onDelete('cascade');
            }
        });
    }
};