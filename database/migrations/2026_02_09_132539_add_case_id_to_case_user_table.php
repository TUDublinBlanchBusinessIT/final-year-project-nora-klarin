<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_user', function (Blueprint $table) {
            $table->unsignedBigInteger('case_id')->after('id'); // add case_id
            $table->foreign('case_id')->references('id')->on('case_files')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('case_user', function (Blueprint $table) {
            $table->dropForeign(['case_id']);
            $table->dropColumn('case_id');
        });
    }
};
