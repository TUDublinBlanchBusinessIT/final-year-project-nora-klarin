<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weekly_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('goal_key');      // sleep, talk, fun, water
            $table->date('week_start');      // Monday of the week

            $table->timestamps();

            $table->unique(['user_id', 'week_start']); // one per user per week
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_goals');
    }
};
