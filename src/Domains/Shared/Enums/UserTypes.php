<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum UserTypes: string
{
    case SYSTEM_ADMIN = 'SYSTEM_ADMIN';

    case SUPER_ADMIN  = 'SUPER_ADMIN';

    case CHIEF_CIVIL_ENGINEER = 'CHIEF_CIVIL_ENGINEER';

    case REGIONAL_CIVIL_ENGINEER = 'REGIONAL_CIVIL_ENGINEER';

    case REGIONAL_PERMANENT_WAY_INSPECTOR = 'REGIONAL_PERMANENT_WAY_INSPECTOR'; // region

    case PERMANENT_WAY_INSPECTOR =  'PERMANENT_WAY_INSPECTOR'; // section block

    case OPERATOR_CONTROLLER = 'OPERATOR_CONTROLLER';
    case DRIVER = 'DRIVER';
    case INSPECTOR = 'INSPECTOR';
    case GANG_MAN = 'GANG_MAN';
}
