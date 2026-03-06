<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;



return new class extends Migration

{

    public function up(): void

    {

        // 1) Create column first if it doesn't exist

        if (!Schema::hasColumn('users', 'username')) {

            Schema::table('users', function (Blueprint $table) {

                $table->string('username')->nullable()->after('name');

            });

        }



        // 2) Fill blank usernames for existing users

        DB::table('users')

            ->whereNull('username')

            ->orWhere('username', '')

            ->update([

                'username' => DB::raw("CONCAT('user_', id)")

            ]);



        // 3) Add unique index only if it doesn't exist yet

        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_unique'");



        if (count($indexes) === 0) {

            Schema::table('users', function (Blueprint $table) {

                $table->unique('username');

            });

        }

    }



    public function down(): void

    {

        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_unique'");



        if (count($indexes) > 0) {

            Schema::table('users', function (Blueprint $table) {

                $table->dropUnique('users_username_unique');

            });

        }



        if (Schema::hasColumn('users', 'username')) {

            Schema::table('users', function (Blueprint $table) {

                $table->dropColumn('username');

            });

        }

    }

};