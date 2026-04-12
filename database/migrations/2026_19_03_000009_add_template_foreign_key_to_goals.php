<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the foreign key from goals.template_id -> goal_templates.id.
     *
     * This is done in a separate migration (after goal_templates is created)
     * because goals was altered in migration 000004, which runs before
     * goal_templates exists in 000007. MySQL requires the referenced table
     * to exist before the FK constraint can be added.
     */
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->foreign('template_id')
                  ->references('id')->on('goal_templates')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
        });
    }
};
