<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::create('tag_goal_templates', function (Blueprint $table) {

            $table->id();



            $table->foreignId('tag_id')

                ->constrained('tags')

                ->cascadeOnDelete();



            $table->foreignId('goal_template_id')

                ->constrained('goal_templates')

                ->cascadeOnDelete();



            $table->timestamps();



            $table->unique(['tag_id', 'goal_template_id']);

        });

    }



    public function down(): void

    {

        Schema::dropIfExists('tag_goal_templates');

    }

};

