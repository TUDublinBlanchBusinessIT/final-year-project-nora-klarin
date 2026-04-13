<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('goal_templates', function (Blueprint $table) {

            $table->foreignId('source_domain_id')

                  ->nullable()

                  ->constrained('domains')

                  ->nullOnDelete();

        });

    }



    public function down(): void

    {

        Schema::table('goal_templates', function (Blueprint $table) {

            $table->dropForeign(['source_domain_id']);

            $table->dropColumn('source_domain_id');

        });

    }

};