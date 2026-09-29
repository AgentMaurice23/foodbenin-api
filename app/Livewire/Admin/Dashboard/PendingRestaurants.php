<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\Restaurant;

class PendingRestaurants extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.pending-restaurants',
            [

                'restaurants' =>
                    Restaurant::with([
                        'owner',
                        'city'
                    ])
                    ->where(
                        'status',
                        'pending'
                    )
                    ->latest()
                    ->take(10)
                    ->get()
            ]
        );
    }
}