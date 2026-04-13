<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void

{

    Schema::create('wellbeing_answers', function (Blueprint $table) {

        $table->id();



        $table->foreignId('wellbeing_check_id')->constrained()->cascadeOnDelete();

        $table->foreignId('question_id')->constrained()->cascadeOnDelete();



        $table->integer('raw_value')->nullable();

        $table->float('normalized_score')->nullable();

        $table->float('risk_score')->nullable();

        $table->string('tag')->nullable();



        $table->timestamps();

    });

}



public function down(): void

{

    Schema::dropIfExists('wellbeing_answers');

}
};
