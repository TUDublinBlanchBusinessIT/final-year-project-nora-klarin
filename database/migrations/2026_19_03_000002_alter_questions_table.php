<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // What UI component to render for this question
            $table->enum('response_type', [
                'likert_3',
                'likert_5',
                'slider',
                'emoji_scale',
                'scenario',
            ])->default('likert_5')->after('text');

            // Academic traceability — which validated framework this question draws from
            $table->string('source_framework', 50)->nullable()->after('response_type');
            // e.g. 'OECD', 'SDQ', 'HBSC', 'CORS', 'WEMWBS', 'Me_and_My_Feelings'
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['response_type', 'source_framework']);
        });
    }
};
