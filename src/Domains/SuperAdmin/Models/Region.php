<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Region extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'creator_id',
        'updater_id',
    ];

    /** @return BelongsTo<Station> */
    public function startStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'start_station_id',
        );
    }
}
