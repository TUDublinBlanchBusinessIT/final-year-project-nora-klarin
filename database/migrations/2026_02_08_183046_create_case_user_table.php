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

    $table->foreignId('case_file_id')
          ->constrained('case_files')
          ->cascadeOnDelete();

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->enum('role', ['social_worker', 'carer']);

    $table->timestamp('assigned_at')->nullable();

    $table->timestamps();

    $table->unique(['case_file_id', 'user_id']);


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
