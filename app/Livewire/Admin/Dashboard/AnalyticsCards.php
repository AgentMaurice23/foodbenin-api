<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;

class AnalyticsCards extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.analytics-cards',
            [

                'todayRevenue' =>
                    Payment::where(
                        'status',
                        'paid'
                    )
                    ->whereDate(
                        'created_at',
                        today()
                    )
                    ->sum('amount'),

                'monthRevenue' =>
                    Payment::where(
                        'status',
                        'paid'
                    )
                    ->whereMonth(
                        'created_at',
                        now()->month
                    )
                    ->sum('amount'),

                'newUsers' =>
                    User::whereMonth(
                        'created_at',
                        now()->month
                    )->count(),

                'newOrders' =>
                    Order::whereMonth(
                        'created_at',
                        now()->month
                    )->count(),
            ]
        );
    }
}