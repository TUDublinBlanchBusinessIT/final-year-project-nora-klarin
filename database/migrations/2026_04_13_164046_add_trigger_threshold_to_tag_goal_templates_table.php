<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('tag_goal_templates', function (Blueprint $table) {

            $table->integer('trigger_threshold')->nullable()->after('goal_template_id');

        });

    }



    public function down(): void

    {

        Schema::table('tag_goal_templates', function (Blueprint $table) {

            $table->dropColumn('trigger_threshold');

        });

    }

};