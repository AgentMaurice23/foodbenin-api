<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
class RecentRestaurants extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.recent-restaurants',
            [
                'restaurants' => Restaurant::query()
                    ->with([
                        'city',
                        'zone',
                        'owner'
                    ])
                    ->latest()
                    ->take(10)
                    ->get()
            ]
        );
    }

}
