<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum AtomikLogsTypes: string
{
    case MACRO1  = 'MACRO1';  // REQUEST LINE ENTRY

    case MACRO2 = 'MACRO2'; // CONFIRM LICENSE

    case MACRO3 = 'MACRO3'; // AUTHORIZE LINE ENTRY

    case MACRO5 = 'MACRO5'; // LOCATION

    case MACRO10 = 'MACRO10'; // END JOURNEY

    case MACRO6 = 'MACRO6'; // LOCATION
}
