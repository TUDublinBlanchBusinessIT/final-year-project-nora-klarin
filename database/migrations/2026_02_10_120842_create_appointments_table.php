<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create the table if it doesn't already exist
        if (Schema::hasTable('appointments')) {
            return;
        }

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_file_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(false);
            $table->unsignedBigInteger('young_person_id')->nullable(false);
            $table->dateTime('start_time')->nullable(false);
            $table->dateTime('end_time')->nullable();
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add foreign keys where appropriate, but only if referenced tables exist
        try {
            if (Schema::hasTable('case_files')) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreign('case_file_id')->references('id')->on('case_files')->nullOnDelete();
                });
            } elseif (Schema::hasTable('casefile') || Schema::hasTable('case_files')) {
                // handled above; left for clarity
            }

            if (Schema::hasTable('users')) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
                });
            }

            if (Schema::hasTable('youngperson') || Schema::hasTable('young_people') || Schema::hasTable('young_person')) {
                // add appropriate FK only if table exists and column names match in your project
                // Example (uncomment & adapt if your young person table is named youngperson):
                // Schema::table('appointments', function (Blueprint $table) {
                //     $table->foreign('young_person_id')->references('id')->on('youngperson')->cascadeOnDelete();
                // });
            }
        } catch (\Throwable $e) {
            // ignore FK creation errors in local dev to keep migrations resilient
        }
    }

    public function down(): void
    {
        // drop the table only if it exists
        if (Schema::hasTable('appointments')) {
            // attempt to drop foreign keys if present (silently ignore errors)
            try {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->dropForeign(['case_file_id']);
                });
            } catch (\Throwable $e) {}

            try {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->dropForeign(['created_by']);
                });
            } catch (\Throwable $e) {}

            try {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->dropForeign(['young_person_id']);
                });
            } catch (\Throwable $e) {}

            Schema::dropIfExists('appointments');
        }
    }
};
