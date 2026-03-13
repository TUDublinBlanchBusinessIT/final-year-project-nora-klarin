<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_people', function (Blueprint $table) {

            $table->string('name')->after('id');
            $table->string('relationship')->after('name');
            $table->string('phone')->nullable()->after('relationship');
            $table->string('email')->nullable()->after('phone');
            $table->unsignedBigInteger('user_id')->after('email');

        });
    }

    public function down(): void
    {
        Schema::table('trusted_people', function (Blueprint $table) {

            $table->dropColumn([
                'name',
                'relationship',
                'phone',
                'email',
                'user_id'
            ]);

        });
    }
};