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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pr_id'); // hubungkan ke PR
            $table->date('tanggal_payment');
            $table->string('nomor_payment')->unique();
            $table->decimal('nilai_payment', 20, 2)->default(0);
            
            // Attachment files
            $table->string('invoice')->nullable();
            $table->string('receipt')->nullable();
            $table->string('nodin_payment')->nullable();
            $table->string('bill')->nullable();

            $table->timestamps();

            $table->foreign('pr_id')->references('id')->on('prs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};