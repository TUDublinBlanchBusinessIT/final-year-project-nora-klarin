<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        // If users table doesn't exist yet, skip

        if (!Schema::hasTable('users')) {

            return;

        }



        // If role column doesn't exist, add it safely

        if (!Schema::hasColumn('users', 'role')) {

            Schema::table('users', function (Blueprint $table) {

                $table->string('role')->default('carer');

            });

            return;

        }



        // If it exists, try to ensure default is set

        try {

            Schema::table('users', function (Blueprint $table) {

                $table->string('role')->default('carer')->change();

            });

        } catch (\Throwable $e) {

            // ignore change errors

        }

    }



    public function down(): void

    {

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {

            Schema::table('users', function (Blueprint $table) {

                $table->dropColumn('role');

            });

        }

    }

};

