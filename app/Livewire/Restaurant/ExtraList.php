<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use Livewire\Component;

class ExtraList extends Component
{
    public Restaurant $restaurant;

    public function mount(
        Restaurant $restaurant
    )
    {
        abort_if(
            $restaurant->owner_id
            !== auth()->id(),
            403
        );

        $this->restaurant =
            $restaurant;
    }

    public function toggle(
        $id
    )
    {
        $extra =
            $this->restaurant
                ->extras()
                ->findOrFail(
                    $id
                );

        $extra->update([

            'is_available' =>
                !$extra
                    ->is_available
        ]);
    }

    public function render()
    {
        return view(
            'livewire.restaurant.extra-list',
            [

                'extras' =>
                    $this
                        ->restaurant
                        ->extras()
                        ->latest()
                        ->get()
            ]
        );
    }
}