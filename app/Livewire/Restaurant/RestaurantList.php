<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;

class RestaurantList extends Component
{
    public function render()
    {
        return view(
            'livewire.restaurant.restaurant-list',
            [

                'restaurants' =>
                    auth()
                        ->user()
                        ->restaurants
                        ()
                        ->with([
                            'city',
                            'zone'
                        ])
                        ->get(),

                'total' =>
                    auth()
                        ->user()
                        ->restaurants()
                        ->count(),

                'approved' =>
                    auth()
                        ->user()
                        ->restaurants()
                        ->approved()
                        ->count(),

                'pending' =>
                    auth()
                        ->user()
                        ->restaurants()
                        ->pending()
                        ->count(),
            ]
        );
    }
}