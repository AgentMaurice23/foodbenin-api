<?php

namespace App\Enums;

enum RoleEnum:string
{
    case SUPER_ADMIN = 'super_admin';

    case ADMIN = 'admin';

    case RESTAURANT = 'restaurant';

    case DRIVER = 'driver';

    case CLIENT = 'client';
}
