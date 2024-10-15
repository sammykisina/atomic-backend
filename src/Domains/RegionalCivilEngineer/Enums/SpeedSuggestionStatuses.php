<?php

declare(strict_types=1);

namespace Domains\RegionalCivilEngineer\Enums;

enum SpeedSuggestionStatuses: string
{
    case PENDING = 'PENDING';

    case APPROVED = 'APPROVED';

    case CHANGED = 'CHANGED';
}
