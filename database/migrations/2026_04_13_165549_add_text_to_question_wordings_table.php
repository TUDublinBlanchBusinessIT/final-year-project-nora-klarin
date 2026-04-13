<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('question_wordings', function (Blueprint $table) {

            if (!Schema::hasColumn('question_wordings', 'text')) {

                $table->text('text')->after('question_id');

            }

        });

    }



    public function down(): void

    {

        Schema::table('question_wordings', function (Blueprint $table) {

            if (Schema::hasColumn('question_wordings', 'text')) {

                $table->dropColumn('text');

            }

        });

    }

};

