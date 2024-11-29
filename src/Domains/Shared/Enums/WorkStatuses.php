<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum WorkStatuses: string
{
    case ON_THE_JOB = 'ON_THE_JOB';
    case ON_LEAVE = 'ON_LEAVE';
}
