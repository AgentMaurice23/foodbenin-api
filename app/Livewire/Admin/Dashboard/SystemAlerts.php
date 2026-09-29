<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Restaurant;

class SystemAlerts extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.dashboard.system-alerts',
            [

                'failedPayments' =>
                    Payment::where(
                        'status',
                        'failed'
                    )->count(),

                'expiredSubscriptions' =>
                    Subscription::where(
                        'end_date',
                        '<',
                        now()
                    )->count(),

                'pendingRestaurants' =>
                    Restaurant::where(
                        'status',
                        'pending'
                    )->count(),
            ]
        );
    }
}