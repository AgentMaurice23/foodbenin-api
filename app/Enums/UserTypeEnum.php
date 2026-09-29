<?php

namespace App\Enums;

enum UserTypeEnum:string
{
    case CLIENT = 'client';

    case RESTAURANT = 'restaurant';

    case DRIVER = 'driver';

    case ADMIN = 'admin';
}