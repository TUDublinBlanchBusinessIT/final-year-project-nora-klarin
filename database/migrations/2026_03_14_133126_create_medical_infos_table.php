<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::create('medical_infos', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('case_file_id');

            $table->string('condition')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

        });

    }



    public function down(): void

    {

        Schema::dropIfExists('medical_infos');

    }

};

