<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum NotificationTypes: string
{
    case JOURNEY = 'JOURNEY';
    case CURRENT_DRIVER_LOCATION = 'CURRENT_DRIVER_LOCATION';
}
