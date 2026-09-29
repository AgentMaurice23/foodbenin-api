<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Models\Restaurant;
use App\Models\Category;

use Illuminate\Support\Str;

class CreateCategory extends Component
{
    use WithFileUploads;

    public Restaurant $restaurant;

    public $name;

    public $description;

    public $image;

    public function save()
    {
        $category =
            Category::create([

                'restaurant_id' =>
                    $this
                        ->restaurant
                        ->id,

                'name' =>
                    $this->name,

                'slug' =>
                    Str::slug(
                        $this->name
                    ),

                'description' =>
                    $this
                        ->description,
            ]);

        if ($this->image) {

            $category
                ->addMedia(
                    $this->image
                )
                ->toMediaCollection(
                    'image'
                );
        }

        session()->flash(
            'success',
            'Catégorie créée.'
        );

        return redirect()
            ->route(
                'restaurant.categories',
                $this->restaurant
            );
    }

    public function render()
    {
        return view(
            'livewire.restaurant.create-category'
        );
    }
}