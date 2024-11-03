<?php

namespace Domains\Driver\Enums;

enum LicenseRouteStatuses: string
{
    case PENDING = 'PENDING';

    case OCCUPIED = 'OCCUPIED';

    case NEXT = 'NEXT';
}
