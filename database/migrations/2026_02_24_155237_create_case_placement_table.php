<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->foreignId('placement_id')->constrained('placements')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_placements');
    }
};