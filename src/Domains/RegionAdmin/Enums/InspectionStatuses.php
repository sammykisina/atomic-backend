<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Enums;

enum InspectionStatuses: string
{
    case AWAITING_CONFIRMATION =  'AWAITING_CONFIRMATION';

    case COMPLETED = 'COMPLETED';
}
