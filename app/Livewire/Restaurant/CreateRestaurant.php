<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Models\City;
use App\Models\Zone;
use App\Models\Restaurant;

use Illuminate\Support\Str;

use App\Enums\RestaurantStatusEnum;

class CreateRestaurant extends Component
{
    use WithFileUploads;

    public $name;

    public $description;

    public $phone;

    public $email;

    public $address;

    public $city_id;

    public $zone_id;

    public $delivery_fee;

    public $minimum_order;

    public $logo;

    public $cover;

    protected function rules()
    {
        return [

            'name' => 'required',

            'description' => 'required',

            'phone' => 'required',

            'email' => 'required|email',

            'address' => 'required',

            'city_id' => 'required',

            'zone_id' => 'required',

            'delivery_fee' => 'required',

            'minimum_order' => 'required',

            'logo' => 'nullable|image',

            'cover' => 'nullable|image',
        ];
    }

    public function save()
    {
        $this->validate();

        $restaurant =
            Restaurant::create([

                'owner_id' =>
                    auth()->id(),

                'city_id' =>
                    $this->city_id,

                'zone_id' =>
                    $this->zone_id,

                'name' =>
                    $this->name,

                'slug' =>
                    Str::slug(
                        $this->name
                    ),

                'description' =>
                    $this->description,

                'phone' =>
                    $this->phone,

                'email' =>
                    $this->email,

                'address' =>
                    $this->address,

                'delivery_fee' =>
                    $this->delivery_fee,

                'minimum_order' =>
                    $this->minimum_order,

                'status' =>
                    RestaurantStatusEnum
                        ::PENDING
                        ->value,
            ]);

        if ($this->logo) {

            $restaurant
                ->addMedia(
                    $this->logo
                )
                ->toMediaCollection(
                    'logo'
                );
        }

        if ($this->cover) {

            $restaurant
                ->addMedia(
                    $this->cover
                )
                ->toMediaCollection(
                    'cover'
                );
        }

        session()->flash(
            'success',
            'Restaurant créé.'
        );

        return redirect()
            ->route(
                'restaurant.dashboard'
            );
    }

    public function render()
    {
        return view(
            'livewire.restaurant.create-restaurant',
            [

                'cities' =>
                    City::all(),

                'zones' =>
                    Zone::all(),
            ]
        );
    }
}