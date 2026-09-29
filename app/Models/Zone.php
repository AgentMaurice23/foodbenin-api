<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    
    use HasFactory;

    protected $fillable = [

        'city_id',

        'name',

        'slug',

    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }
}