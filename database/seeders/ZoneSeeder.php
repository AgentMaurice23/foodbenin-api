<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Zone;
use Illuminate\Support\Str;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
     {
        $zones = [

            'Cotonou' => [

                'Fidjrossè',

                'Akpakpa',

                'Cadjèhoun',

                'Agla',

                'Zogbo',

                'Gbégamey',

                'Vèdoko',

                'Ganhi',

                'Jonquet',

                'Houéyiho'
            ],

            'Abomey-Calavi' => [

                'Godomey',

                'Tankpè',

                'Cococodji',

                'Akassato',

                'Zinvié',

                'Hêvié'
            ],

            'Porto-Novo' => [

                'Ouando',

                'Djassin',

                'Avakpa',

                'Tokpota'
            ],

            'Parakou' => [

                'Banikanni',

                'Zongo',

                'Albarika',

                'Titirou'
            ],

            'Bohicon' => [

                'Sodohomè',

                'Lissèzoun',

                'Passagon'
            ],

            'Lokossa' => [

                'Agnivèdji',

                'Houin',

                'Koudo'
            ],

            'Ouidah' => [

                'Pahou',

                'Savi',

                'Djègbadji'
            ],

            'Natitingou' => [

                'Kotopounga',

                'Perma',

                'Tchoumi-Tchoumi'
            ],

            'Djougou' => [

                'Kolokondé',

                'Bariénou',

                'Partago'
            ],

            'Kandi' => [

                'Angaradébou',

                'Sonsoro',

                'Donwari'
            ]
        ];

        foreach ($zones as $cityName => $cityZones) {

            $city = City::where('name', $cityName)->first();

            if (!$city) {
                continue;
            }

            foreach ($cityZones as $zoneName) {

                Zone::firstOrCreate([
                    'city_id' => $city->id,
                    'name' => $zoneName,
                    'slug' => Str::slug($zoneName),

                ]);
            }
        }
    }
}
