<?php

declare(strict_types=1);

namespace Domains\Operator\Enums;

enum ShiftActivities: string
{
    case CREATED = 'CREATED';

    case DELETED = 'DELETED';

    case FINISHED = 'FINISHED';

    case EDITED = 'EDITED';
}
