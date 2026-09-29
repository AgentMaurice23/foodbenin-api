<?php

namespace App\Services\Restaurant;

use App\Models\Restaurant;
use Illuminate\Support\Str;

class RestaurantService
{
    public function create(
        array $data
    ): Restaurant {

        $data['slug'] =
            Str::slug(
                $data['name']
            );

        return Restaurant::create(
            $data
        );
    }

    public function update(
        Restaurant $restaurant,
        array $data
    ) {
        return $restaurant
            ->update($data);
    }
}