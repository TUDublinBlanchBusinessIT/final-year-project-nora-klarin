<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellbeing_trends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->foreignId('wellbeing_check_id')->constrained('wellbeing_checks')->cascadeOnDelete();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('overall_risk_score', 8, 2)->nullable();
            $table->string('risk_level')->nullable();
            $table->json('trend_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellbeing_trends');
    }
};
