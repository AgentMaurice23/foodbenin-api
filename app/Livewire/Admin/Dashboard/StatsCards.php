<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;

class StatsCards extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.stats-cards',
            [

                'restaurants' =>
                    Restaurant::count(),

                'clients' =>
                    User::role('client')->count(),

                'orders' =>
                    Order::count(),

                'revenue' =>
                    Payment::where(
                        'status',
                        'paid'
                    )->sum('amount'),
            ]
        );
    }
}