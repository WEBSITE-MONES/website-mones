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
            if (Schema::hasColumn('progress', 'sub_pekerjaan')) {
                $table->dropColumn(['sub_pekerjaan','volume','satuan','bobot','rencana','realisasi']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->string('sub_pekerjaan')->nullable();
            $table->decimal('volume', 12, 2)->nullable();
            $table->string('satuan', 20)->nullable();
            $table->decimal('bobot', 8, 2)->default(0.00);
            $table->decimal('rencana', 5, 2)->default(0.00);
            $table->decimal('realisasi', 5, 2)->default(0.00);
        });
    }
};