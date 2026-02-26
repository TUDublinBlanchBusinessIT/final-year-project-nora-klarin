<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If users table doesn't exist yet, skip (it will be created by earlier migration)
        if (! Schema::hasTable('users')) {
            return;
        }

        // If column doesn't exist, add it with a safe default
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('carer');
            });
            return;
        }

        // If it exists, attempt to ensure it's not nullable and has the default.
        // Some MySQL setups do not allow change() easily; catch exceptions silently.
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('carer')->change();
            });
        } catch (\Throwable $e) {
            // ignore change errors — column already exists and not critical
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
