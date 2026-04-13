<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('questions', function (Blueprint $table) {

            if (!Schema::hasColumn('questions', 'response_type')) {

                $table->string('response_type')->nullable()->after('text');

            }



            if (!Schema::hasColumn('questions', 'source_framework')) {

                $table->string('source_framework')->nullable()->after('response_type');

            }



            if (!Schema::hasColumn('questions', 'is_positive')) {

                $table->boolean('is_positive')->default(true)->after('max_value');

            }



            if (!Schema::hasColumn('questions', 'risk_weight')) {

                $table->integer('risk_weight')->nullable()->after('is_positive');

            }



            if (!Schema::hasColumn('questions', 'age_band_min')) {

                $table->integer('age_band_min')->nullable()->after('risk_weight');

            }



            if (!Schema::hasColumn('questions', 'age_band_max')) {

                $table->integer('age_band_max')->nullable()->after('age_band_min');

            }



            if (!Schema::hasColumn('questions', 'option_labels')) {

                $table->json('option_labels')->nullable()->after('age_band_max');

            }



            if (!Schema::hasColumn('questions', 'version')) {

                $table->integer('version')->default(1)->after('option_labels');

            }

        });

    }



    public function down(): void

    {

        Schema::table('questions', function (Blueprint $table) {

            if (Schema::hasColumn('questions', 'response_type')) {

                $table->dropColumn('response_type');

            }

            if (Schema::hasColumn('questions', 'source_framework')) {

                $table->dropColumn('source_framework');

            }

            if (Schema::hasColumn('questions', 'is_positive')) {

                $table->dropColumn('is_positive');

            }

            if (Schema::hasColumn('questions', 'risk_weight')) {

                $table->dropColumn('risk_weight');

            }

            if (Schema::hasColumn('questions', 'age_band_min')) {

                $table->dropColumn('age_band_min');

            }

            if (Schema::hasColumn('questions', 'age_band_max')) {

                $table->dropColumn('age_band_max');

            }

            if (Schema::hasColumn('questions', 'option_labels')) {

                $table->dropColumn('option_labels');

            }

            if (Schema::hasColumn('questions', 'version')) {

                $table->dropColumn('version');

            }

        });

    }

};