<?php

namespace Database\Factories;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'restaurant_id' => Restaurant::factory(),
            'amount' => fake()->randomFloat(2, 500, 100000),
            'payment_method' => fake()->randomElement(
                PaymentMethodEnum::cases()
            ),
            'uuid' => (string) Str::uuid(),
            'transaction_reference' => null,
            'status' => PaymentStatusEnum::PENDING,
            'paid_at' => null,
        ];
    }

    /**
     * Create a paid payment.
     */
    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::PAID,
            'transaction_reference' => fake()->uuid(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Create a processing payment.
     */
    public function processing(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::PROCESSING,
            'transaction_reference' => fake()->uuid(),
            'paid_at' => null,
        ]);
    }

    /**
     * Create a failed payment.
     */
    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::FAILED,
            'transaction_reference' => fake()->uuid(),
            'paid_at' => null,
        ]);
    }

    /**
     * Create a refunded payment.
     */
    public function refunded(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatusEnum::REFUNDED,
            'transaction_reference' => fake()->uuid(),
            'paid_at' => now(),
        ]);
    }
}