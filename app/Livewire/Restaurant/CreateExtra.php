<?php

namespace App\Livewire\Restaurant;

use App\Models\Extra;
use App\Models\Restaurant;

use Livewire\Component;

use Illuminate\Support\Str;

class CreateExtra extends Component
{
    public Restaurant $restaurant;

    public $name;

    public $price;

    public function save()
    {
        $this->validate([

            'name' =>
                'required',

            'price' =>
                'required|numeric'
        ]);

        Extra::create([

            'restaurant_id' =>
                $this
                    ->restaurant
                    ->id,

            'uuid' =>
                Str::uuid(),

            'name' =>
                $this->name,

            'price' =>
                $this->price
        ]);

        return redirect()
            ->route(
                'restaurant.extras',
                $this->restaurant
            );
    }

    public function render()
    {
        return view(
            'livewire.restaurant.create-extra'
        );
    }
}