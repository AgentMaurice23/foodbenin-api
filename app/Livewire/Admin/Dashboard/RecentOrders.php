<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\Order;

class RecentOrders extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.recent-orders',
            [
                'orders' =>
                    Order::latest()
                        ->take(10)
                        ->get()
            ]
        );
    }
}