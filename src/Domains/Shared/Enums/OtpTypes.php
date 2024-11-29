<?php

declare(strict_types=1);

namespace Domains\Shared\Enums;

enum OtpTypes: string
{
    case VERIFICATION = 'verification';
    case PASSWORD_RESET = 'password-reset';
}
