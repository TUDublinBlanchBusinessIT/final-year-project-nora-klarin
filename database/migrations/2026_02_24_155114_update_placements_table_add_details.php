<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->foreignId('carer_id')->nullable()->after('case_file_id')->constrained('users')->nullOnDelete();
            $table->integer('capacity')->default(1)->after('location');
            $table->integer('current_occupancy')->default(0)->after('capacity');
            $table->enum('status', ['active','inactive','under_review'])->default('active')->after('current_occupancy');
            $table->decimal('latitude', 10, 7)->nullable()->after('status');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->text('notes')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->dropColumn(['carer_id','capacity','current_occupancy','status','latitude','longitude','notes']);
        });
    }
};