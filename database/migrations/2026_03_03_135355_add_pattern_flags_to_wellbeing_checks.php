<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            $table->json('tag_summary')->nullable();
            $table->boolean('safeguarding_flag')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            $table->dropColumn(['tag_summary','safeguarding_flag']);
        });
    }
};
