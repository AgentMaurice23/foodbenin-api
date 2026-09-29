<?php

namespace App\Livewire\Admin\Restaurants;


use Livewire\Component;
use App\Models\Restaurant;

class RestaurantStats extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.restaurants.restaurant-stats',
            [

                'total'=>
                    Restaurant::count(),

                'approved'=>
                    Restaurant::where(
                        'status',
                        'approved'
                    )->count(),

                'pending'=>
                    Restaurant::where(
                        'status',
                        'pending'
                    )->count(),

                'suspended'=>
                    Restaurant::where(
                        'status',
                        'suspended'
                    )->count(),
            ]
        );
    }
}