<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [

        'restaurant_id',

        'name',

        'slug',

        'description',

        'position',

        'is_active'
    ];

    protected $casts = [

        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(
            Restaurant::class
        );
    }

    public function products()
    {
        return $this->hasMany(
            Product::class
        );
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile();
    }
}