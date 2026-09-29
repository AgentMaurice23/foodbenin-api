<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [

            'Cotonou',

            'Porto-Novo',

            'Abomey-Calavi',

            'Parakou',

            'Bohicon',

            'Lokossa',

            'Ouidah',

            'Natitingou',

            'Djougou',

            'Kandi',

            'Abomey',

            'Savalou',

            'Pobè',

            'Comè',

            'Grand-Popo'
        ];

        foreach ($cities as $city) {

            City::firstOrCreate([
                'name' => $city,
                'slug' => Str::slug($city),
            ]);
        }
    }
}
