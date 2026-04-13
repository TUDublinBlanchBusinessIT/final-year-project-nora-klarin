<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('tags', function (Blueprint $table) {

            $table->string('category')->nullable()->after('name');

            $table->boolean('alert_override')->default(false)->after('category');

            $table->integer('alert_threshold')->nullable()->after('alert_override');

        });

    }



    public function down(): void

    {

        Schema::table('tags', function (Blueprint $table) {

            $table->dropColumn([

                'category',

                'alert_override',

                'alert_threshold'

            ]);

        });

    }

};

