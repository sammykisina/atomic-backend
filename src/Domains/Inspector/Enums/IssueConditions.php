<?php

declare(strict_types=1);

namespace Domains\Inspector\Enums;

enum IssueConditions: string
{
    case CRITICAL = 'CRITICAL';

    case NEGLIGIBLE = 'NEGLIGIBLE';

    case FIXABLE_BY_INSPECTOR = 'FIXABLE_BY_INSPECTOR';

    case GOOD = 'GOOD';
}
