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
        Schema::table('prs', function (Blueprint $table) {
            $table->string('status_pekerjaan')->default('PR')->after('tanggal_pr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prs', function (Blueprint $table) {
            $table->dropColumn('status_pekerjaan');
        });
    }
};