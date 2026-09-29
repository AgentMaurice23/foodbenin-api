<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\Order;
use App\Models\Restaurant;

class ActivityCards extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.activity-cards',
            [

                'todayOrders' =>
                    Order::whereDate(
                        'created_at',
                        today()
                    )->count(),

                'pendingOrders' =>
                    Order::where(
                        'status',
                        'pending'
                    )->count(),

                'preparingOrders' =>
                    Order::where(
                        'status',
                        'preparing'
                    )->count(),

                'activeRestaurants' =>
                    Restaurant::where(
                        'status',
                        'approved'
                    )->count(),
            ]
        );
    }
}