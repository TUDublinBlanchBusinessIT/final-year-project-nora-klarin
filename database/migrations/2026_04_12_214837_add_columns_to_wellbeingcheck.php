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
        Schema::table('wellbeing_checks', function (Blueprint $table) {
    $table->timestamp('completed_at')->nullable()->after('overall_score');
    $table->string('check_type')->default('scheduled')->after('completed_at');
    $table->string('game_mode')->default('slider')->after('check_type');
});
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            //
        });        
    }
};
