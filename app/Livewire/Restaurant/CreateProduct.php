<?php

namespace App\Livewire\Restaurant;

use App\Models\Product;
use App\Models\Restaurant;

use Livewire\Component;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProduct extends Component
{
    use WithFileUploads;

    public Restaurant $restaurant;

    public $name;

    public $description;

    public $category_id;

    public $price;

    public $sale_price;

    public $stock_type = 'unlimited';

    public $stock_quantity;

    public $is_available = true;

    public $is_featured = false;

    public $has_variants = false;

    public $has_extras = false;

    public $preparation_time = 15;

    public $thumbnail;

    public $gallery = [];

    public $variants = [];

    public $selectedExtras = [];

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

    protected function rules()
    {
        return [

            'name' =>
                'required',

            'category_id' =>
                'required',

            'price' =>
                'required|numeric',

            'sale_price' =>
                'nullable|numeric',

            'stock_quantity' =>
                'nullable|integer',
        ];
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {

                $product =
                    Product::create([

                        'restaurant_id' =>
                            $this
                                ->restaurant
                                ->id,

                        'category_id' =>
                            $this
                                ->category_id,

                        'uuid' =>
                            Str::uuid(),

                        'name' =>
                            $this
                                ->name,

                        'slug' =>
                            Str::slug(
                                $this->name
                            ),

                        'description' =>
                            $this
                                ->description,

                        'price' =>
                            $this
                                ->price,

                        'sale_price' =>
                            $this
                                ->sale_price,

                        'is_available' =>
                            $this
                                ->is_available,

                        'is_featured' =>
                            $this
                                ->is_featured,

                        'has_variants' =>
                            count(
                                $this->variants
                            ) > 0,

                        'has_extras' =>
                            count(
                                $this->selectedExtras
                            ) > 0,

                        'stock_type' =>
                            $this
                                ->stock_type,

                        'stock_quantity' =>
                            $this
                                ->stock_quantity,

                        'preparation_time' =>
                            $this
                                ->preparation_time,
                    ]);

                    foreach (
                        $this->gallery
                        as $image
                    ) {

                        $product
                            ->addMedia(
                                $image
                            )
                            ->toMediaCollection(
                                'gallery'
                            );
                    }

                    foreach (
                        $this->variants
                        as $variant
                    ) {

                        $product
                            ->variants()
                            ->create([

                                'uuid' =>
                                    Str::uuid(),

                                'name' =>
                                    $variant['name'],

                                'price' =>
                                    $variant['price'],

                                'is_default' =>
                                    $variant[
                                        'is_default'
                                    ],
                            ]);
                    }

                $product
                    ->extras()
                    ->sync($this
                        ->selectedExtras);
                }
                );

                session()->flash(
                    'success',
                    'Produit créé.'
                );

        return redirect()->
            route(
                'restaurant.products',
                $this->restaurant
            );
    
    }

    public function render()
    {
        return view(
            'livewire.restaurant.create-product',
            [

                'categories' =>
                    $this
                        ->restaurant
                        ->categories()
                        ->get(),

                'extras' =>
                    $this
                        ->restaurant
                        ->extras()
                        ->available()
                        ->get()
            ]
        );
    }

    // ajouter un variant
    public function addVariant()
    {
        $this->variants[] = [

            'name' => '',

            'price' => 0,

            'is_default' => false,
        ];
    }

    // Supprimer un variant
    public function removeVariant($index)
    {
        unset(
            $this->variants[$index]
        );

        $this->variants =
            array_values(
                $this->variants
            );
    }

    // ajouter un extra
    public function addExtra()
    {
        $this->extras[] = [

            'name' => '',

            'price' => 0,
        ];
    }

    // Supprimer un extra
    public function removeExtra($index)
    {
        unset(
            $this->extras[$index]
        );

        $this->extras =
            array_values(
                $this->extras
            );
    }
}
