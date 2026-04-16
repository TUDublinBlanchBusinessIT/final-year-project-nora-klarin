<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;



return new class extends Migration

{

    public function up(): void

    {

        Schema::table('threads', function (Blueprint $table) {

            $table->unsignedBigInteger('social_worker_id')->nullable()->after('child_id');

        });



        if (Schema::hasColumn('threads', 'carer_id')) {

            DB::table('threads')->update([

                'social_worker_id' => DB::raw('carer_id'),

            ]);

        }



        Schema::table('threads', function (Blueprint $table) {

            $table->foreign('social_worker_id')->references('id')->on('users')->nullOnDelete();

        });

    }



    public function down(): void

    {

        Schema::table('threads', function (Blueprint $table) {

            $table->dropForeign(['social_worker_id']);

            $table->dropColumn('social_worker_id');

        });

    }

};

