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
         Schema::table('grs', function (Blueprint $table) {
            $table->unsignedBigInteger('po_id')->nullable()->after('pr_id');
            $table->unsignedBigInteger('termin_id')->nullable()->after('po_id');

            $table->foreign('po_id')->references('id')->on('pos')->onDelete('set null');
            $table->foreign('termin_id')->references('id')->on('termins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grs', function (Blueprint $table) {
            $table->dropForeign(['po_id']);
            $table->dropForeign(['termin_id']);
            $table->dropColumn(['po_id', 'termin_id']);
        });
    }
};