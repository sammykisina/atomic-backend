<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Enums;

enum InspectionScheduleStatuses: string
{
    case AWAITING_CONFIRMATION =  'AWAITING_CONFIRMATION';

    case ACTIVE = 'ACTIVE';

    case INACTIVE = 'INACTIVE';
}
