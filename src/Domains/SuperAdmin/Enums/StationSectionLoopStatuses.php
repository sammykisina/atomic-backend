<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Enums;

enum StationSectionLoopStatuses: string
{
    case GOOD = 'GOOD';

    case INTERDICTION = 'INTERDICTION';

    case LICENSE_ISSUED = 'LICENSE_ISSUED';
}
