<?php

declare(strict_types=1);

namespace Domains\Driver\Enums;

enum LicenseRouteStatuses: string
{
    case PENDING = 'PENDING';

    case OCCUPIED = 'OCCUPIED';

    case NEXT = 'NEXT';

    case COMPLETED = 'COMPLETED';

    case REVOKED = 'REVOKED';

    case RESCUED = 'RESCUED';
}
