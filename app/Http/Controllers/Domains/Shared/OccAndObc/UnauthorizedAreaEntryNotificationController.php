<?php

declare(strict_types=1);

namespace App\Http\Controllers\Domains\Shared\OccAndObc;

use App\Http\Requests\UnauthorizedAreaEntryNotificationRequest;

final class UnauthorizedAreaEntryNotificationController
{
    public function __invoke(UnauthorizedAreaEntryNotificationRequest $request): void {}
}
