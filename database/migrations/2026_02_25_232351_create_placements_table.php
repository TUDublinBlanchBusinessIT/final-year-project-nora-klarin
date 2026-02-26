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
        Schema::create('placements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('carer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('child_id')->constrained()->cascadeOnDelete();
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->string('status')->default('active');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
