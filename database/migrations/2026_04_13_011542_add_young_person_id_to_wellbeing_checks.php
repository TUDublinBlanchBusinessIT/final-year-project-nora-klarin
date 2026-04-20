<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            $table->foreignId('young_person_id')
                  ->after('id')
                  ->constrained('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wellbeing_checks', function (Blueprint $table) {
            $table->dropForeign(['young_person_id']);
            $table->dropColumn('young_person_id');
        });
    }
};