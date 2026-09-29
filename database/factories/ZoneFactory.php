<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Zone>
 */
class ZoneFactory extends Factory
{
    /**
     * Le modèle associé au factory.
     */
    protected $model = Zone::class;

    /**
     * Définit les données par défaut d'une zone.
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [

            /*
            |--------------------------------------------------------------------------
            | Ville
            |--------------------------------------------------------------------------
            |
            | Une zone appartient obligatoirement à une ville.
            |
            */

            'city_id' => City::factory(),

            /*
            |--------------------------------------------------------------------------
            | Nom
            |--------------------------------------------------------------------------
            */

            'name' => $name,

            /*
            |--------------------------------------------------------------------------
            | Slug
            |--------------------------------------------------------------------------
            */

            'slug' => Str::slug($name),
        ];
    }
}