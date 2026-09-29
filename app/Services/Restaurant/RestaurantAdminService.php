<?php

namespace App\Services\Restaurant;

use App\Models\Restaurant;

class RestaurantAdminService
{
    public function approve(Restaurant $restaurant)
    {
        $restaurant->update([
            'status' => 'approved'
        ]);
    }

    public function reject(Restaurant $restaurant)
    {
        $restaurant->update([
            'status' => 'rejected'
        ]);
    }

    public function suspend(Restaurant $restaurant)
    {
        $restaurant->update([
            'status' => 'suspended'
        ]);
    }

    public function activate(Restaurant $restaurant)
    {
        $restaurant->update([
            'status' => 'approved'
        ]);
    }
}