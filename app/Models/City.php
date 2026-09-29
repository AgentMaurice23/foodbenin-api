<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'slug',

    ];

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }
}