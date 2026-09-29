<?php

namespace App\Livewire\Restaurant;

use App\Models\Product;
use App\Models\Restaurant;

use Livewire\Component;
use Livewire\WithFileUploads;

class EditProduct extends Component
{
    use WithFileUploads;

    public Restaurant $restaurant;

    public Product $product;

    public $name;

    public $description;

    public $category_id;

    public $price;

    public $sale_price;

    public $stock_type;

    public $stock_quantity;

    public $is_available;

    public $is_featured;

    public $preparation_time;

    public $selectedExtras = [];

    public function mount(
    Restaurant $restaurant,
    Product $product
)
{
    abort_if(
        $restaurant->owner_id
        !== auth()->id(),
        403
    );

    $this->restaurant =
        $restaurant;

    $this->product =
        $product;

    $this->name =
        $product->name;

    $this->description =
        $product->description;

    $this->category_id =
        $product->category_id;

    $this->price =
        $product->price;

    $this->sale_price =
        $product->sale_price;

    $this->stock_type =
        $product->stock_type
            ->value;

    $this->stock_quantity =
        $product->stock_quantity;

    $this->is_available =
        $product->is_available;

    $this->is_featured =
        $product->is_featured;

    $this->preparation_time =
        $product->preparation_time;

    $this->selectedExtras =
        $product
            ->extras()
            ->pluck('id')
            ->toArray();
}
public function save()
{
    $this->product->update([

        'category_id' =>
            $this->category_id,

        'name' =>
            $this->name,

        'description' =>
            $this->description,

        'price' =>
            $this->price,

        'sale_price' =>
            $this->sale_price,

        'stock_type' =>
            $this->stock_type,

        'stock_quantity' =>
            $this->stock_quantity,

        'is_available' =>
            $this->is_available,

        'is_featured' =>
            $this->is_featured,

        'preparation_time' =>
            $this->preparation_time,
    ]);

    $this->product
        ->extras()
        ->sync(
            $this->selectedExtras
        );

    session()->flash(
            'success',
            'Produit modifié.'
        );
    }
    public function render()
    {
        return view(
            'livewire.restaurant.edit-product',
            [

                'categories' =>
                    $this
                        ->restaurant
                        ->categories
                        ,

                'extras' =>
                    $this
                        ->restaurant
                        ->extras
            ]
        );
    }
}