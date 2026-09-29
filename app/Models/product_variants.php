<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product_variants extends Model
{
    //

    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
    }
}
