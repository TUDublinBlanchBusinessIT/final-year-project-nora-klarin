<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->string('case_code', 20)->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn('case_code');
        });
    }
};