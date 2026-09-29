<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Enums\RestaurantStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [

        'uuid',
        'owner_id',
        'city_id',
        'zone_id',

        'name',
        'slug',
        'description',

        'phone',
        'email',


        'address',

        'latitude',
        'longitude',

        'rating',
        'reviews_count',

        'delivery_fee',
        'minimum_order',

        'is_open',
        'is_verified',

        'status'
    ];

    protected $casts = [

        'status' => RestaurantStatusEnum::class,

        'is_open' => 'boolean',

        'is_verified' => 'boolean',

        'rating' => 'decimal:2',

        'delivery_fee' => 'decimal:2',

        'minimum_order' => 'decimal:2',

        'latitude' => 'decimal:7',

        'longitude' => 'decimal:7',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function extras()
    {
        return $this->hasMany(Extra::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(
            RestaurantPaymentMethod::class
        );
    }


    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function notifications()
    {
        return $this->morphMany(
            Notification::class,
            'notifiable'
        );
    }

    public function scopeApproved($query)
    {
        return $query->where(
            'status',
            RestaurantStatusEnum::APPROVED
        );
    }

    public function scopePending($query)
    {
        return $query->where(
            'status',
            RestaurantStatusEnum::PENDING
        );
    }

    public function scopeOpen($query)
    {
        return $query->where(
            'is_open',
            true
        );
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn() =>
                $this->logo
                    ? asset('storage/'.$this->logo)
                    : null
        );
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::make(
            get: fn() =>
                $this->cover
                    ? asset('storage/'.$this->cover)
                    : null
        );
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->singleFile();

        $this
            ->addMediaCollection('cover')
            ->singleFile();
    }

    public function registerMediaConversions($media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(300)
            ->height(300);

        $this
            ->addMediaConversion('banner')
            ->width(1200)
            ->height(600);
    }
}