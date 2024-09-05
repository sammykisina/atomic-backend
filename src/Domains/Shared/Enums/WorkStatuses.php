<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum WorkStatuses: string
{
    case ON_THE_JOB = 'on_the_job';
    case ON_LEAVE = 'on_leave';
}
