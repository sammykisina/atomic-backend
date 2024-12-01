<?php

declare(strict_types=1);

namespace Domains\Shared\Services;

use Domains\Shared\Models\AtomikLog;

final class AtomikLogService
{
    /**
     * CREATE ATIMIK LOG
     * @param array $atomikLogData
     * @return AtomikLog
     */
    public static function createAtomicLog(array $atomikLogData): AtomikLog
    {
        return AtomikLog::query()->create(attributes: $atomikLogData);
    }


}
