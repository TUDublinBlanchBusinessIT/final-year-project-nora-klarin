<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('case_files', 'case_code')) {
            Schema::table('case_files', function (Blueprint $table) {
                $table->renameColumn('case_code', 'case_reference');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('case_files', 'case_reference')) {
            Schema::table('case_files', function (Blueprint $table) {
                $table->renameColumn('case_reference', 'case_code');
            });
        }
    }
};