<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Train extends Model
{
    use LogsActivity;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'driver_id',
        'service_order',
        'locomotive_number_id',
        'tail_number',
        'number_of_wagons',
        'line_id',
        'origin_station_id',
        'destination_station_id',
    ];

    /** @return BelongsTo<Line> */
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /** @return BelongsTo<Station> */
    public function origin(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'origin_station_id',
        );
    }

    /** @return BelongsTo<Station> */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'destination_station_id',
        );
    }

    /** @return BelongsTo<User> */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'driver_id',
        );
    }

    /** @return BelongsTo<LocomotiveNumber> */
    public function locomotiveNumber(): BelongsTo
    {
        return $this->belongsTo(
            related: LocomotiveNumber::class,
            foreignKey: 'locomotive_number_id',
        );
    }

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
