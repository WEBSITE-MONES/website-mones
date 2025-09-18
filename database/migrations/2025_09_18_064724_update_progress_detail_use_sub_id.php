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
            // hapus relasi lama
            if (Schema::hasColumn('progress_detail', 'progress_id')) {
                $table->dropForeign(['progress_id']);
                $table->dropColumn('progress_id');
            }

            // tambah relasi ke progress_sub
            $table->foreignId('sub_id')
                  ->after('id')
                  ->constrained('progress_sub')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_details', function (Blueprint $table) {
            $table->dropForeign(['sub_id']);
            $table->dropColumn('sub_id');

            $table->foreignId('progress_id')
                  ->after('id')
                  ->constrained('progress')
                  ->onDelete('cascade');
        });
    }
};