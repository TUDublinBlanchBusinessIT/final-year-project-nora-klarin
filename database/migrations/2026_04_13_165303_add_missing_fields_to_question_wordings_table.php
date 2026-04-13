<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('question_wordings', function (Blueprint $table) {

            if (!Schema::hasColumn('question_wordings', 'age_min')) {

                $table->integer('age_min')->nullable()->after('question_id');

            }



            if (!Schema::hasColumn('question_wordings', 'age_max')) {

                $table->integer('age_max')->nullable()->after('age_min');

            }



            if (!Schema::hasColumn('question_wordings', 'created_at') &&

                !Schema::hasColumn('question_wordings', 'updated_at')) {

                $table->timestamps();

            }

        });

    }



    public function down(): void

    {

        Schema::table('question_wordings', function (Blueprint $table) {

            $drops = [];



            if (Schema::hasColumn('question_wordings', 'age_min')) {

                $drops[] = 'age_min';

            }



            if (Schema::hasColumn('question_wordings', 'age_max')) {

                $drops[] = 'age_max';

            }



            if (!empty($drops)) {

                $table->dropColumn($drops);

            }

        });

    }

};