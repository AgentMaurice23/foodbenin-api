<?php

namespace App\Enums;

enum ProductStockEnum:string
{
    case UNLIMITED = 'unlimited';

    case LIMITED = 'limited';

    case OUT_OF_STOCK = 'out_of_stock';
}
