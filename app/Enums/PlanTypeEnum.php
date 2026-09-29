<?php

namespace App\Enums;

enum PlanTypeEnum:string
{
    case BASIC = 'basic';

    case PRO = 'pro';

    case PREMIUM = 'premium';
}