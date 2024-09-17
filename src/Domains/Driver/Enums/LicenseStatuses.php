<?php

declare(strict_types=1);

namespace Domains\Driver\Enums;

enum LicenseStatuses: string
{
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
    case CANCELLED = 'CANCELLED';

    case USED = 'USED';

    case CONFIRMED = 'CONFIRMED';
}
