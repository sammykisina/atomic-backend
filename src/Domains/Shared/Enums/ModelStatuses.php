<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum ModelStatuses: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
