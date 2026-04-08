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

        // Add the case_id column only if it doesn't already exist
        if (! Schema::hasColumn('case_user', 'case_id')) {
            Schema::table('case_user', function (Blueprint $table) {
                // do not use ->after('id') to avoid dependency on column order
                $table->unsignedBigInteger('case_id')->nullable(false);
            });
        }

        // Optionally add foreign key if the cases/case table exists
        try {
            if (Schema::hasTable('cases')) {
                Schema::table('case_user', function (Blueprint $table) {
                    $table->foreign('case_id')->references('id')->on('cases')->cascadeOnDelete();
                });
            } elseif (Schema::hasTable('case')) {
                Schema::table('case_user', function (Blueprint $table) {
                    $table->foreign('case_id')->references('id')->on('case')->cascadeOnDelete();
                });
            }
        } catch (\Throwable $e) {
            // ignore errors (FK may already exist or referenced table missing)
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('case_user')) {
            return;
        }

        // drop FK if exists (silently ignore errors) and drop column if present
        try {
            Schema::table('case_user', function (Blueprint $table) {
                $table->dropForeign(['case_id']);
            });
        } catch (\Throwable $e) { /* ignore */ }

        if (Schema::hasColumn('case_user', 'case_id')) {
            Schema::table('case_user', function (Blueprint $table) {
                $table->dropColumn('case_id');
            });
        }
    }
};
