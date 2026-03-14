<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::create('placements', function (Blueprint $table) {

            $table->id();



            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();

            $table->foreignId('carer_id')->nullable()->constrained('users')->nullOnDelete();



            $table->string('type')->nullable();

            $table->string('address')->nullable();

            $table->text('notes')->nullable();



            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();



            $table->timestamps();

        });

    }



    public function down(): void

    {

        Schema::dropIfExists('placements');

    }

};

