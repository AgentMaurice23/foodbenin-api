<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;

use App\Models\Restaurant;

class CategoryList extends Component
{
    public Restaurant $restaurant;

    public function mount(
        Restaurant $restaurant
    )
    {
        abort_if(
            $restaurant->owner_id
            != auth()->id(),
            403
        );

        $this->restaurant =
            $restaurant;
    }

    public function render()
    {
        return view(
            'livewire.restaurant.category-list',
            [

                'categories' =>
                    $this
                        ->restaurant
                        ->categories()
                        ->latest()
                        ->get()
            ]
        );
    }
}