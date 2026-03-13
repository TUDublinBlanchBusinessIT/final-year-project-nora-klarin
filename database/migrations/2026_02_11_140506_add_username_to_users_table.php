<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Fill blank usernames for existing users
        DB::table('users')
            ->whereNull('username')
            ->orWhere('username', '')
            ->update([
                'username' => DB::raw("CONCAT('user_', id)")
            ]);

        // 2) Add UNIQUE index only if it doesn't exist yet
        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_unique'");

        if (count($indexes) === 0) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }
    }

    public function down(): void
    {
        // Drop unique index if it exists (keep the column)
        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_unique'");

        if (count($indexes) > 0) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_username_unique');
            });
        }
    }
};
