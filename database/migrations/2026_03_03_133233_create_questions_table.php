<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();

            $table->text('text');

            $table->integer('min_value')->default(0);
            $table->integer('max_value')->default(4);

            $table->boolean('is_positive')->default(true);

            $table->decimal('risk_weight', 5, 2)->default(1.00);
            $table->enum('risk_level', ['low','medium','high','critical'])->default('low');

            $table->integer('age_band_min')->nullable();
            $table->integer('age_band_max')->nullable();

            $table->boolean('is_active')->default(true);

            $table->integer('version')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};