<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {

            // Child in care
            $table->unsignedBigInteger('young_person_id')->nullable()->after('id');

            // Case state
            $table->string('status')->default('open')->after('young_person_id');
            $table->string('risk_level')->default('low')->after('status');

            // Placement context
            $table->string('placement_type')->nullable()->after('risk_level');
            $table->string('placement_location')->nullable()->after('placement_type');

            // Key dates
            $table->timestamp('opened_at')->nullable()->after('placement_location');
            $table->timestamp('closed_at')->nullable()->after('opened_at');
            $table->timestamp('last_reviewed_at')->nullable()->after('closed_at');

            // High-level notes
            $table->text('summary')->nullable()->after('last_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn([
                'young_person_id',
                'status',
                'risk_level',
                'placement_type',
                'placement_location',
                'opened_at',
                'closed_at',
                'last_reviewed_at',
                'summary',
            ]);
        });
    }
};
