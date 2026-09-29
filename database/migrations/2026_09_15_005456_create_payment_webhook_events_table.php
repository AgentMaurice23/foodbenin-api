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
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();

            /*
             * Payment concerned by this webhook event.
             */
            $table->foreignId('payment_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * Payment provider:
             * mtn, moov, celtis, etc.
             */
            $table->string('provider', 50);

            /*
             * Unique identifier supplied by the payment provider.
             *
             * This is the main idempotency key.
             */
            $table->string('event_id', 255);

            /*
             * Type of webhook event.
             *
             * Examples:
             * payment.success
             * payment.failed
             * payment.refunded
             */
            $table->string('event_type', 100);

            /*
             * Provider transaction reference.
             */
            $table->string('transaction_reference', 255)
                ->nullable();

            /*
             * Complete webhook payload.
             *
             * Keeping the original payload gives us:
             * - traceability
             * - debugging capability
             * - audit history
             */
            $table->json('payload');

            /*
             * NULL = event received but not processed yet.
             */
            $table->timestamp('processed_at')
                ->nullable();

            $table->timestamps();

            /*
             * The same provider must never process
             * the same event twice.
             */
            $table->unique(
                ['provider', 'event_id'],
                'payment_webhook_events_provider_event_unique'
            );

            /*
             * Useful indexes for lookups and auditing.
             */
            $table->index(
                ['payment_id', 'event_type'],
                'payment_webhook_events_payment_type_index'
            );

            $table->index(
                ['provider', 'transaction_reference'],
                'payment_webhook_events_provider_transaction_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};