<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_people', function (Blueprint $table) {

            // link trusted person to the child user
            $table->foreignId('child_id')
                  ->after('id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // trusted person details
            $table->string('name');
            $table->string('relationship')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trusted_people', function (Blueprint $table) {

            // remove foreign key + column
            $table->dropConstrainedForeignId('child_id');

            // remove other columns
            $table->dropColumn([
                'name',
                'relationship',
                'phone',
                'email'
            ]);
        });
    }
};