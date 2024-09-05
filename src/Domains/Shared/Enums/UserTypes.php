<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum UserTypes: string
{
    case SYSTEM_ADMIN = 'SYSTEM_ADMIN';
    case SUPER_ADMIN  = 'SUPER_ADMIN';
    case MAINTAINER_ADMIN = 'MAINTAINER_ADMIN';
    case REGION_ADMIN =  'REGION_ADMIN';
    case OPERATOR_CONTROLLER = 'OPERATOR_CONTROLLER';
    case DRIVER = 'DRIVER';
    case INSPECTOR = 'INSPECTOR';
    case ENGINEER = 'ENGINEER';
}
