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

        Schema::table('wellbeing_checks', function (Blueprint $table) {

            $table->date('week_start')->nullable()->after('case_file_id');

        });

    }



    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        Schema::table('wellbeing_checks', function (Blueprint $table) {

            $table->dropColumn('week_start');

        });

    }

};