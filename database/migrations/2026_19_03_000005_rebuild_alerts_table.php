<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old alerts table — it only had id, message, timestamps
        // and had no foreign keys or type system
        Schema::dropIfExists('alerts');

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();

            // What triggered this alert
            $table->unsignedBigInteger('wellbeing_check_id');
            $table->unsignedBigInteger('young_person_id');

            // The specific response that fired the alert (nullable for domain-level alerts)
            $table->unsignedBigInteger('response_id')->nullable();

            // Tag that triggered the alert (null if domain-drop triggered)
            $table->unsignedBigInteger('tag_id')->nullable();

            // Domain that triggered the alert (null if tag-override triggered)
            $table->unsignedBigInteger('domain_id')->nullable();

            // What kind of alert this is — determines UI priority and response workflow
            $table->enum('alert_type', [
                'domain_drop',        // domain average fell below threshold
                'domain_decline',     // domain dropped N+ points vs previous check
                'critical_response',  // single question scored critically low
                'tag_override',       // safeguarding/clinical tag fired on a response
            ]);

            $table->enum('severity', ['low', 'medium', 'high', 'critical']);

            // Human-readable summary shown to social worker
            $table->string('message', 255);

            // Acknowledgement tracking
            $table->timestamp('acknowledged_at')->nullable();
            $table->unsignedBigInteger('acknowledged_by')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('wellbeing_check_id')
                  ->references('id')->on('wellbeing_checks')
                  ->onDelete('cascade');

            $table->foreign('young_person_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('response_id')
                  ->references('id')->on('wellbeing_responses')
                  ->onDelete('set null');

            $table->foreign('tag_id')
                  ->references('id')->on('tags')
                  ->onDelete('set null');

            $table->foreign('domain_id')
                  ->references('id')->on('domains')
                  ->onDelete('set null');

            $table->foreign('acknowledged_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            // Index for common query: unacknowledged alerts for a young person
            $table->index(['young_person_id', 'acknowledged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('message', 255);
            $table->timestamps();
        });
    }
};
