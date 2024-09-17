<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum NotificationTypes: string
{
    case JOURNEY_CREATED = 'JOURNEY_CREATED';
    case JOURNEY_EDITED = 'JOURNEY_EDITED';
    case CURRENT_DRIVER_LOCATION = 'CURRENT_DRIVER_LOCATION';

    case JOURNEY_ACCEPTED = 'JOURNEY_ACCEPTED';

    case LICENSE_CONFIRMED = 'LICENSE_CONFIRMED';
}
