<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;

class AdminDashboardService
{
    public function statistics(): array
    {
        return [

            'restaurants' =>
                Restaurant::count(),

            'active_restaurants' =>
                Restaurant::where(
                    'status',
                    'approved'
                )->count(),

            'clients' =>
                User::role('client')->count(),

            'drivers' =>
                User::role('driver')->count(),

            'orders' =>
                Order::count(),

            'today_orders' =>
                Order::whereDate(
                    'created_at',
                    today()
                )->count(),

            'revenue' =>
                Payment::where(
                    'status',
                    'paid'
                )->sum('amount'),

            'pending_orders' =>
                Order::where(
                    'status',
                    'pending'
                )->count(),

            'preparing_orders' =>
                Order::where(
                    'status',
                    'preparing'
                )->count(),

            'delivery_orders' =>
                Order::where(
                    'status',
                    'on_the_way'
                )->count(),
        ];
    }
}