<?php

namespace Database\Factories;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The model associated with this factory.
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $user = User::factory();

        return [
            'type' => fake()->randomElement(
                NotificationTypeEnum::cases()
            ),

            'channel' => NotificationChannelEnum::DATABASE,

            'title' => fake()->sentence(4),

            'message' => fake()->sentence(12),

            'data' => [
                'reference' => fake()->uuid(),
            ],

            'notifiable_type' => User::class,

            'notifiable_id' => $user,

            'read_at' => null,

            'sent_at' => null,

            'failed_at' => null,

            'error_message' => null,
        ];
    }
}