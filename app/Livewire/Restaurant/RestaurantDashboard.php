<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;

use App\Models\Restaurant;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;

class RestaurantDashboard extends Component
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

    public function render()
    {
        return view(
            'livewire.restaurant.restaurant-dashboard',
            [

                'totalOrders' =>
                    $this->restaurant
                        ->orders()
                        ->count(),

                'pendingOrders' =>
                    $this->restaurant
                        ->orders()
                        ->where(
                            'status',
                            OrderStatusEnum::PENDING
                        )
                        ->count(),

                'productsCount' =>
                    $this->restaurant
                        ->products()
                        ->count(),

                'reviewsCount' =>
                    $this->restaurant
                        ->reviews()
                        ->count(),

                'revenue' =>
                    $this->restaurant
                        ->orders()
                        ->whereHas(
                            'payment',
                            fn($q) =>
                                $q->where(
                                    'status',
                                    PaymentStatusEnum::PAID
                                )
                        )
                        ->sum(
                            'total'
                        )
            ]
        );
    }
}