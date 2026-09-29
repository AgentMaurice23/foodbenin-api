<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentWebhookEvent>
 */
class PaymentWebhookEventFactory extends Factory
{
    /**
     * The name of the model that this factory corresponds to.
     *
     * @var class-string<PaymentWebhookEvent>
     */
    protected $model = PaymentWebhookEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'provider' => fake()->randomElement([
                'mtn',
                'moov',
                'celtis',
            ]),
            'event_id' => (string) Str::uuid(),
            'event_type' => fake()->randomElement([
                'payment.success',
                'payment.failed',
                'payment.refunded',
            ]),
            'transaction_reference' => 'TXN-' . strtoupper(
                Str::random(12)
            ),
            'payload' => [
                'id' => fake()->uuid(),
                'status' => 'success',
                'amount' => fake()->randomFloat(
                    2,
                    100,
                    100000
                ),
            ],
            'processed_at' => null,
        ];
    }

    /**
     * Mark the webhook event as processed.
     */
    public function processed(): static
    {
        return $this->state(fn () => [
            'processed_at' => now(),
        ]);
    }
}