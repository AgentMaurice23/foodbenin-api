<?php

namespace App\Enums;

enum CouponTypeEnum:string
{
    case PERCENTAGE = 'percentage';

    case FIXED = 'fixed';
}