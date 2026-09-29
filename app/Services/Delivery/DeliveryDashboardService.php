<?php

namespace App\Services\Delivery;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Models\Delivery;
use App\Models\Driver;

class DeliveryDashboardService
{
    /**
     * Retourne l'ensemble des statistiques
     * nécessaires au dashboard Delivery.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        return [
            'deliveries' => $this->getDeliveryStatistics(),
            'drivers' => $this->getDriverStatistics(),
            'revenue' => $this->getRevenueStatistics(),
            'today' => $this->getTodayStatistics(),
            'month' => $this->getMonthStatistics(),
        ];
    }

    /**
     * Retourne les statistiques globales
     * concernant les livraisons.
     *
     * @return array<string, int>
     */
    protected function getDeliveryStatistics(): array
    {
        return [
            'total' => Delivery::query()->count(),

            'pending' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::PENDING
            ),

            'assigned' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::ASSIGNED
            ),

            'accepted' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::ACCEPTED
            ),

            'going_to_restaurant' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::GOING_TO_RESTAURANT
            ),

            'at_restaurant' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::AT_RESTAURANT
            ),

            'picked_up' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::PICKED_UP
            ),

            'on_the_way' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::ON_THE_WAY
            ),

            'arrived' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::ARRIVED
            ),

            'delivered' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::DELIVERED
            ),

            'failed' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::FAILED
            ),

            'cancelled' => $this->countDeliveryByStatus(
                DeliveryStatusEnum::CANCELLED
            ),
        ];
    }

    /**
     * Retourne les statistiques concernant
     * les livreurs.
     *
     * @return array<string, int>
     */
    protected function getDriverStatistics(): array
    {
        return [
            'total' => Driver::query()->count(),

            'online' => $this->countDriverByStatus(
                DriverStatusEnum::ONLINE
            ),

            'busy' => $this->countDriverByStatus(
                DriverStatusEnum::BUSY
            ),

            'offline' => $this->countDriverByStatus(
                DriverStatusEnum::OFFLINE
            ),

            'suspended' => $this->countDriverByStatus(
                DriverStatusEnum::SUSPENDED
            ),

            'verified' => Driver::query()
                ->where('is_verified', true)
                ->count(),

            'unverified' => Driver::query()
                ->where('is_verified', false)
                ->count(),
        ];
    }

    /**
     * Retourne les statistiques financières
     * de toutes les livraisons terminées.
     *
     * @return array<string, float>
     */
    protected function getRevenueStatistics(): array
    {
        $query = Delivery::query()
            ->where(
                'status',
                DeliveryStatusEnum::DELIVERED
            );

        return [
            'delivery_fees' => (float) $query->sum(
                'delivery_fee'
            ),

            'driver_earnings' => (float) $query->sum(
                'driver_earning'
            ),

            'platform_commission' => (float) $query->sum(
                'platform_commission'
            ),
        ];
    }

    /**
     * Retourne les statistiques des livraisons
     * effectuées aujourd'hui.
     *
     * @return array<string, mixed>
     */
    protected function getTodayStatistics(): array
    {
        $query = Delivery::query()
            ->whereDate(
                'created_at',
                today()
            );

        return [
            'total' => $query->count(),

            'delivered' => (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->count(),

            'failed' => (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::FAILED
                )
                ->count(),

            'cancelled' => (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::CANCELLED
                )
                ->count(),

            'revenue' => (float) (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->sum('delivery_fee'),

            'platform_commission' => (float) (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->sum('platform_commission'),
        ];
    }

    /**
     * Retourne les statistiques des livraisons
     * du mois en cours.
     *
     * @return array<string, mixed>
     */
    protected function getMonthStatistics(): array
    {
        $query = Delivery::query()
            ->whereMonth(
                'created_at',
                now()->month
            )
            ->whereYear(
                'created_at',
                now()->year
            );

        return [
            'total' => $query->count(),

            'delivered' => (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->count(),

            'failed' => (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::FAILED
                )
                ->count(),

            'cancelled' => (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::CANCELLED
                )
                ->count(),

            'revenue' => (float) (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->sum('delivery_fee'),

            'driver_earnings' => (float) (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->sum('driver_earning'),

            'platform_commission' => (float) (clone $query)
                ->where(
                    'status',
                    DeliveryStatusEnum::DELIVERED
                )
                ->sum('platform_commission'),
        ];
    }

    /**
     * Compte les livraisons correspondant
     * à un statut donné.
     */
    protected function countDeliveryByStatus(
        DeliveryStatusEnum $status
    ): int {
        return Delivery::query()
            ->where('status', $status)
            ->count();
    }

    /**
     * Compte les livreurs correspondant
     * à un statut donné.
     */
    protected function countDriverByStatus(
        DriverStatusEnum $status
    ): int {
        return Driver::query()
            ->where('status', $status)
            ->count();
    }
}
