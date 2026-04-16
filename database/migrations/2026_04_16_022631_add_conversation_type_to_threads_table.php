<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('threads', function (Blueprint $table) {

            $table->string('conversation_type')

                ->default('young_person')

                ->after('social_worker_id');

        });

    }



    public function down(): void

    {

        Schema::table('threads', function (Blueprint $table) {

            $table->dropColumn('conversation_type');

        });

    }

};

