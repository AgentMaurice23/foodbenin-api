<?php

namespace App\Livewire\Admin\Restaurants;

use Livewire\Component;

use Livewire\WithPagination;
use App\Models\Restaurant;
use App\Services\Restaurant\RestaurantAdminService;

class RestaurantTable extends Component
{
    use WithPagination;

    public $search='';

    public $status='';

    public function render()
    {
        $restaurants =
            Restaurant::query()

                ->with([
                    'owner',
                    'city',
                    'zone'
                ])

                ->when(
                    $this->search,
                    fn($q)=>
                        $q->where(
                            'name',
                            'like',
                            '%'.$this->search.'%'
                        )
                )

                ->when(
                    $this->status,
                    fn($q)=>
                        $q->where(
                            'status',
                            $this->status
                        )
                )

                ->latest()

                ->paginate(10);

        return view(
            'livewire.admin.restaurants.restaurant-table',
            compact(
                'restaurants'
            )
        );
    }

    
    public function approve(
        RestaurantAdminService $service,
        $id
    ){
        $service->approve(
            Restaurant::findOrFail($id)
        );
    }

    public function suspend(
        RestaurantAdminService $service,
        $id
    ){
        $service->suspend(
            Restaurant::findOrFail($id)
        );
    }
}