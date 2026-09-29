<?php

namespace App\Enums;

enum RestaurantStatusEnum:string
{
    case PENDING = 'pending';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    case SUSPENDED = 'suspended';

    case CLOSED = 'closed';
}