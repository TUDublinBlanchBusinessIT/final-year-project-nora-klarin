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
<<<<<<< HEAD:database/migrations/2026_02_12_105434_create_alerts_table.php
Schema::create('alerts', function (Blueprint $table) {
    $table->id();
    $table->string('message');
    $table->timestamps();
=======
Schema::table('users', function (Blueprint $table) {
    $table->string('username')->nullable()->unique();
>>>>>>> main:database/migrations/2026_02_08_183810_add_username_to_users_table.php
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
