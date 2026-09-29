<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RevenueChart extends Component
{
   public array $categories = [];

    public array $series = [];

    public function mount()
    {
        $revenues = Payment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->categories =
            $revenues
                ->pluck('date')
                ->toArray();

        $this->series =
            $revenues
                ->pluck('total')
                ->toArray();
    }

    public function render()
    {
        return view(
            'livewire.admin.dashboard.revenue-chart'
        );
    }
}