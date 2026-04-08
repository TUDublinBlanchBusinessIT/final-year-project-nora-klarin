<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['child_id', 'carer_id']); // only one thread per pair
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
