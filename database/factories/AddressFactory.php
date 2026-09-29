<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * Définition des données par défaut.
     */
    public function definition(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Utilisateur
            |--------------------------------------------------------------------------
            */

            'user_id' => User::factory(),

            /*
            |--------------------------------------------------------------------------
            | Titre
            |--------------------------------------------------------------------------
            */

            'title' => fake()->randomElement([
                'Maison',
                'Bureau',
                'Domicile',
            ]),

            /*
            |--------------------------------------------------------------------------
            | Destinataire
            |--------------------------------------------------------------------------
            */

            'recipient_name' => fake()->name(),

            'recipient_phone' => '97' . fake()->numerify('######'),

            /*
            |--------------------------------------------------------------------------
            | Adresse
            |--------------------------------------------------------------------------
            */

            'address' => fake()->address(),

            /*
            |--------------------------------------------------------------------------
            | Coordonnées GPS
            |--------------------------------------------------------------------------
            |
            | Coordonnées approximatives autour de Cotonou.
            |
            */

            'latitude' => fake()->latitude(
                6.30,
                6.45
            ),

            'longitude' => fake()->longitude(
                2.30,
                2.50
            ),

            /*
            |--------------------------------------------------------------------------
            | Adresse par défaut
            |--------------------------------------------------------------------------
            */

            'is_default' => false,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Adresse par défaut
    |--------------------------------------------------------------------------
    */

    public function default(): static
    {
        return $this->state(fn () => [

            'is_default' => true,

        ]);
    }
}