<?php

declare(strict_types=1);

namespace Domains\Driver\Enums;

enum AreaTypes: string
{
    case SECTION = 'SECTION';
    case STATION = 'STATION';
    case LOOP = 'LOOP';
}
