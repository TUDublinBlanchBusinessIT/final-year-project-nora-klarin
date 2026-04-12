<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            // Which template generated this goal suggestion (null = manually created)
            $table->unsignedBigInteger('template_id')->nullable()->after('description');

            // Which wellbeing domain this goal is primarily addressing
            $table->unsignedBigInteger('source_domain_id')->nullable()->after('template_id');

            // Audit trail: when was this goal surfaced by the system
            $table->timestamp('suggested_at')->nullable()->after('source_domain_id');

            // Which social worker reviewed and approved this goal
            $table->unsignedBigInteger('approved_by')->nullable()->after('suggested_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Foreign keys — template_id and source_domain_id added after those tables exist
            // They are added in later migrations once goal_templates is created
            $table->foreign('source_domain_id')
                  ->references('id')->on('domains')
                  ->onDelete('set null');

            $table->foreign('approved_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['source_domain_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'template_id',
                'source_domain_id',
                'suggested_at',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};
