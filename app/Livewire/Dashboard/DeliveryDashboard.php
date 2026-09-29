<?php

namespace App\Livewire\Dashboard;

use App\Services\Delivery\DeliveryDashboardService;
use Livewire\Component;

class DeliveryDashboard extends Component
{
    /**
     * Statistiques du dashboard.
     *
     * @var array<string, mixed>
     */
    public array $statistics = [];

    /**
     * Initialise le dashboard.
     */
    public function mount(
        DeliveryDashboardService $dashboardService
    ): void {
        $this->statistics =
            $dashboardService->getStatistics();
    }

    /**
     * Rafraîchit les statistiques du dashboard.
     */
    public function refreshStatistics(
        DeliveryDashboardService $dashboardService
    ): void {
        $this->statistics =
            $dashboardService->getStatistics();
    }

    /**
     * Affiche le dashboard.
     */
    public function render()
    {
        return view(
            'livewire.dashboard.delivery-dashboard'
        );
    }
}
