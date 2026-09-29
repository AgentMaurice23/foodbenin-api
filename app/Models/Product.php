<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ProductStockEnum;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [

        'restaurant_id',
        'category_id',

        'name',
        'slug',

        'description',

        'price',

        'image',

        'is_available',
        'is_featured'
    ];

    protected $casts = [

        'price' => 'decimal:2',

        'sale_price' => 'decimal:2',

        'is_available' => 'boolean',

        'is_featured' => 'boolean',

        'has_variants' => 'boolean',

        'has_extras' => 'boolean',

        'stock_type' => ProductStockEnum::class,
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('thumbnail')
            ->singleFile();

        $this
            ->addMediaCollection('gallery');
    }


    public function scopeAvailable($query)
    {
        return $query
            ->where(
                'is_available',
                true
            );
    }

    public function scopeFeatured($query)
    {
        return $query
            ->where(
                'is_featured',
                true
            );
    }

    public function scopeInStock($query)
    {
        return $query
            ->where(
                'stock_type',
                '!=',
                ProductStockEnum::OUT_OF_STOCK
            );
    }

    public function hasDiscount()
    {
        return !is_null(
            $this->sale_price
        );
    }

    public function currentPrice()
    {
        return $this->sale_price
            ?? $this->price;
    }

    public function isOutOfStock()
    {
        return $this->stock_type
            === ProductStockEnum::OUT_OF_STOCK;
    }

    public function isUnlimited()
    {
        return $this->stock_type
            === ProductStockEnum::UNLIMITED;
    }

    public function hasStock()
    {
        if ($this->isUnlimited()) {
            return true;
        }

        return
            $this->stock_quantity > 0;
    }
    public function getThumbnailAttribute()
    {
        return $this
            ->getFirstMediaUrl(
                'thumbnail'
            );
    }

    public function getGalleryAttribute()
    {
        return $this
            ->getMedia(
                'gallery'
            );
    }
    public function calculatePrice(
        ?ProductVariant $variant = null,
        array $extras = []
    )
    {
        $price =
            $variant
                ? $variant->price
                : $this->currentPrice();

        foreach ($extras as $extra) {

            $price += $extra->price;
        }

        return $price;
    }

    public function decrementStock(
        int $quantity = 1
    )
    {
        if (
            $this->stock_type
            ->value
            === 'limited'
        ) {

            $this->decrement(
                'stock_quantity',
                $quantity
            );

            if (
                $this->stock_quantity <= 0
            ) {

                $this->update([

                    'stock_type' =>
                        'out_of_stock',

                    'stock_quantity' =>
                        0
                ]);
            }
        }
    }

    public function incrementSales(
        int $quantity = 1
    )
    {
        $this->increment(
            'sold_count',
            $quantity
        );
    }
    
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function extras()
    {
        return $this->belongsToMany(
            Extra::class
        );
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function variants()
    {
        return $this->hasMany(
            ProductVariant::class
        );
    }

}