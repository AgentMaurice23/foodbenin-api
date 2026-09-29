<?php

namespace App\Enums;

enum PaymentMethodEnum:string
{
    case MTN = 'mtn';

    case MOOV = 'moov';

    case CELTIS = 'celtis';

    case CARD = 'card';

    case CASH = 'cash';
}