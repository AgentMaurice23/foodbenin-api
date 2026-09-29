<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\User;
use App\Models\Order;
use App\Models\Restaurant;

class PlatformHealth extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.platform-health',
            [

                'users' =>
                    User::count(),

                'restaurants' =>
                    Restaurant::count(),

                'orders' =>
                    Order::count(),
            ]
        );
    }
}