<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('threads', function (Blueprint $table) {
        $table->unsignedBigInteger('recipient_id')->nullable()->after('child_id');
    });

    DB::statement('UPDATE threads SET recipient_id = carer_id');
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
