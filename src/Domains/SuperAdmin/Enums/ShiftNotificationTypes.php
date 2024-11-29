<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Enums;

enum ShiftNotificationTypes: string
{
    case SHIFT_CREATED = 'SHIFT_CREATED';

    case SHIFT_CONFIRMED = 'SHIFT_CONFIRMED';

    case SHIFT_REJECTED = 'SHIFT_REJECTED';

    case SHIFT_CANCELLED = 'SHIFT_CANCELLED';
}
