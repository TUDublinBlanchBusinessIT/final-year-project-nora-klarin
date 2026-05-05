<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_people', function (Blueprint $table) {
            $table->foreignId('child_id')->constrained('users')->cascadeOnDelete()->after('id');
            $table->string('name');
            $table->string('relationship');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trusted_people', function (Blueprint $table) {
            $table->dropForeign(['child_id']);
            $table->dropColumn(['child_id', 'name', 'relationship', 'phone', 'email']);
        });
    }
};
