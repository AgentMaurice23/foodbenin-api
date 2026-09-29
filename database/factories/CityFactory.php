<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Le modèle associé au factory.
     */
    protected $model = City::class;

    /**
     * Définit les données par défaut d'une ville.
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [

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