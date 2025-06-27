<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Loop extends Model
{
    use HasFactory;

    /** @return BelongsTo<Station> */
    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => StationSectionLoopStatuses::class,
        ];
    }
}
