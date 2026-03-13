<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only run if the table exists
        if (! Schema::hasTable('case_user')) {
            return;
        }

        // Add the assigned_at column if it doesn't exist
        if (! Schema::hasColumn('case_user', 'assigned_at')) {
            Schema::table('case_user', function (Blueprint $table) {
                $table->timestamp('assigned_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Only drop if the table and column exist
        if (Schema::hasTable('case_user') && Schema::hasColumn('case_user', 'assigned_at')) {
            Schema::table('case_user', function (Blueprint $table) {
                $table->dropColumn('assigned_at');
            });
        }
    }
};
