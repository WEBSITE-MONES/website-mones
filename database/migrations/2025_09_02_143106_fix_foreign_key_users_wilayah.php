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
        Schema::table('users', function (Blueprint $table) {
            // hapus foreign key lama (yang salah ke "wilayahs")
            $table->dropForeign(['wilayah_id']);

            // tambahkan foreign key yang benar ke tabel "wilayah"
            $table->foreign('wilayah_id')
                  ->references('id')
                  ->on('wilayah')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // rollback: hapus foreign key baru
            $table->dropForeign(['wilayah_id']);

            // balikin lagi ke foreign key lama (ke "wilayahs")
            $table->foreign('wilayah_id')
                  ->references('id')
                  ->on('wilayahs')
                  ->onDelete('set null');
        });
    }
};