<?php

declare(strict_types=1);

namespace Domains\Operator\Enums;

enum ShiftStatuses: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
}
