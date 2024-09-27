<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum UserTypes: string
{
    case SYSTEM_ADMIN = 'SYSTEM_ADMIN';

    case SUPER_ADMIN  = 'SUPER_ADMIN';

    case CHIEF_CIVIL_ENGINEER = 'CHIEF_CIVIL_ENGINEER';

    case REGINAL_CIVIL_ENGINEER = 'REGINAL_CIVIL_ENGINEER';

    case REGINAL_PERMANENT_WAY_INSPECTOR = 'REGINAL_PERMANENT_WAY_INSPECTOR'; // region

    case PERMANENT_WAY_INSPECTOR =  'PERMANENT_WAY_INSPECTOR'; // section block

    case OPERATOR_CONTROLLER = 'OPERATOR_CONTROLLER';
    case DRIVER = 'DRIVER';
    case INSPECTOR = 'INSPECTOR';
    case GANG_MAN = 'GANG_MAN';
}
