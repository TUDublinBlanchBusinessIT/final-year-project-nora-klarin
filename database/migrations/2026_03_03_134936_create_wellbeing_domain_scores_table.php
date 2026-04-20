<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellbeing_domain_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wellbeing_check_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('domain_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('average_score', 5,2);
            $table->decimal('risk_score', 8,2);

            $table->timestamps();

            $table->unique(['wellbeing_check_id','domain_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellbeing_domain_scores');
    }
};

