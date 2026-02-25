<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the pivot table core columns first if not exists
        if (! Schema::hasTable('case_user')) {
            Schema::create('case_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('assigned_at')->nullable();
                $table->timestamps();
            });
        }

        // Attempt to add foreign key to cases (plural then singular). Use try/catch to avoid environment-specific errors.
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
            // ignore: FK couldn't be created (missing table, already exists, permissions, etc.)
        }

        // Attempt to add foreign key to users
        try {
            if (Schema::hasTable('users')) {
                Schema::table('case_user', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                });
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('case_user')) {
            // try to drop foreign keys (silently ignore errors), then drop the table
            try {
                Schema::table('case_user', function (Blueprint $table) {
                    $table->dropForeign(['case_id']);
                });
            } catch (\Throwable $e) { /* ignore */ }

            try {
                Schema::table('case_user', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (\Throwable $e) { /* ignore */ }

            Schema::dropIfExists('case_user');
        }
    }
};
