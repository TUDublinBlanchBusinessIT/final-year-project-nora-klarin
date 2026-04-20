<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wellbeing_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wellbeing_check_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('raw_value');

            $table->decimal('normalised_score', 5,2);
            $table->decimal('risk_contribution', 8,2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellbeing_responses');
    }
};
