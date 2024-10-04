<?php

declare(strict_types=1);

namespace Domains\Inspector\Enums;

enum IssueStatuses: string
{
    case PENDING = 'PENDING';

    case DRAFT = 'DRAFT';

    case RESOLVED = 'RESOLVED';
}
