<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public Restaurant $restaurant;

    public string $search = '';

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

    public function toggleAvailability(
        $productId
    )
    {
        $product =
            $this->restaurant
                ->products()
                ->findOrFail(
                    $productId
                );

        $product->update([

            'is_available' =>
                !$product
                    ->is_available
        ]);
    }

    public function toggleFeatured(
        $productId
    )
    {
        $product =
            $this->restaurant
                ->products()
                ->findOrFail(
                    $productId
                );

        $product->update([

            'is_featured' =>
                !$product
                    ->is_featured
        ]);
    }

    public function render()
    {
        return view(
            'livewire.restaurant.product-list',
            [

                'products' =>
                    $this
                        ->restaurant
                        ->products()
                        ->with('category')
                        ->when(
                            $this->search,
                            fn($q) =>
                                $q->where(
                                    'name',
                                    'like',
                                    '%'.$this->search.'%'
                                )
                        )
                        ->latest()
                        ->paginate(15)
            ]
        );
    }
}