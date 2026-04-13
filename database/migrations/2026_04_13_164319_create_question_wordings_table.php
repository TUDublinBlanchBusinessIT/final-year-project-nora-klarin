<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::create('question_wordings', function (Blueprint $table) {

            $table->id();



            $table->foreignId('question_id')

                ->constrained('questions')

                ->cascadeOnDelete();



            $table->string('audience')->nullable();

            $table->text('wording');



            $table->timestamps();

        });

    }



    public function down(): void

    {

        Schema::dropIfExists('question_wordings');

    }

};