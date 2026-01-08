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
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->uuid('transaction_id');
            $table->string('type');
            $table->timestamp('date_created');
            $table->string('payload_hash', 64);
            $table->json('payload');
            $table->timestamps();

            $table->unique(['provider', 'transaction_id', 'date_created']);
            $table->index(['provider', 'transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
