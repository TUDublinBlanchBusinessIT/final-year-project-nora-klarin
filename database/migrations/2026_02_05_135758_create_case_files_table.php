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

    Schema::create('case_files', function (Blueprint $table) {

        $table->id();

        $table->unsignedBigInteger('youngpersonid')->nullable();

        $table->string('risklevel')->nullable();

        $table->timestamp('openedat')->nullable();

        $table->string('status')->nullable();

        $table->timestamps();

    });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_files');
    }
};
