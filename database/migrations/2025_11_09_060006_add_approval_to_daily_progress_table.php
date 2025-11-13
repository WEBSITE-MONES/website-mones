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
        Schema::table('daily_progress', function (Blueprint $table) {
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('pelapor_id');
            
            $table->foreignId('approved_by')
                  ->nullable()
                  ->after('status_approval')
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->timestamp('approved_at')
                  ->nullable()
                  ->after('approved_by');
            
            $table->text('rejection_reason')
                  ->nullable()
                  ->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_progress', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status_approval',
                'approved_by',
                'approved_at',
                'rejection_reason'
            ]);
        });
    }
};