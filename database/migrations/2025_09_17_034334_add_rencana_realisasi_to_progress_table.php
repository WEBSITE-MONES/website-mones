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
        $table->decimal('rencana', 5, 2)->default(0)->after('po_id');
        $table->decimal('realisasi', 5, 2)->default(0)->after('rencana');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
        $table->dropColumn(['rencana', 'realisasi']);
    });
    }
};