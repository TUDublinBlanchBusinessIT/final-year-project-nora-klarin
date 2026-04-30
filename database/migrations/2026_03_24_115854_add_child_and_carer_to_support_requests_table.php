<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->foreignId('child_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carer_id')->nullable()->after('child_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('support_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('child_id');
            $table->dropConstrainedForeignId('carer_id');
        });
    }
};