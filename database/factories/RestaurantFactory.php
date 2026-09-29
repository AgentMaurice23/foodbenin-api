<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Le modèle associé au factory.
     */
    protected $model = Restaurant::class;

    /**
     * Définit les données par défaut d'un restaurant.
     */
    public function definition(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Propriétaire
            |--------------------------------------------------------------------------
            |
            | Création automatique d'un utilisateur propriétaire.
            |
            */

            'owner_id' => User::factory(),

            /*
            |--------------------------------------------------------------------------
            | Ville
            |--------------------------------------------------------------------------
            |
            | Création automatique d'une ville compatible avec
            | la structure réelle de la table cities.
            |
            */

            'city_id' => City::factory(),

            /*
            |--------------------------------------------------------------------------
            | Zone
            |--------------------------------------------------------------------------
            |
            | La zone doit appartenir à la même ville.
            |
            | Pour éviter une incohérence entre city_id et zone_id,
            | nous créons ici explicitement la ville puis sa zone.
            |
            */

            'zone_id' => function (array $attributes) {

                return Zone::factory()->create([
                    'city_id' => $attributes['city_id'],
                ])->id;
            },

            /*
            |--------------------------------------------------------------------------
            | Identité
            |--------------------------------------------------------------------------
            */

            'name' => 'Restaurant Test',

            'slug' => 'restaurant-test-' . Str::lower(
                Str::random(8)
            ),

            'uuid' => (string) Str::uuid(),

            /*
            |--------------------------------------------------------------------------
            | Informations
            |--------------------------------------------------------------------------
            */

            'description' => 'Restaurant utilisé pour les tests.',

            'phone' => '97000000',

            'email' => fake()->unique()->safeEmail(),

            'address' => 'Gbegamey',

            /*
            |--------------------------------------------------------------------------
            | Géolocalisation
            |--------------------------------------------------------------------------
            */

            'latitude' => 6.3703,

            'longitude' => 2.3912,

            /*
            |--------------------------------------------------------------------------
            | Statistiques
            |--------------------------------------------------------------------------
            */

            'rating' => 0,

            'reviews_count' => 0,

            /*
            |--------------------------------------------------------------------------
            | Tarification
            |--------------------------------------------------------------------------
            */

            'delivery_fee' => 1000,

            'minimum_order' => 0,

            /*
            |--------------------------------------------------------------------------
            | État
            |--------------------------------------------------------------------------
            */

            'is_open' => true,

            'is_verified' => true,

            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            |
            | Si RestaurantStatusEnum est utilisé dans ton projet,
            | adapte cette valeur à la valeur de ton Enum.
            |
            */

            'status' => 'approved',
        ];
    }
}