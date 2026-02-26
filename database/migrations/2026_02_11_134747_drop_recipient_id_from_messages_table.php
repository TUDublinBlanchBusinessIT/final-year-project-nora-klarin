<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            // Drop foreign key first
            $table->dropForeign(['recipient_id']);

            // Then drop column
            $table->dropColumn('recipient_id');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            $table->unsignedBigInteger('recipient_id')->nullable();

            $table->foreign('recipient_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }
};
