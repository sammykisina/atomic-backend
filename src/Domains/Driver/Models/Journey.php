<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Journey extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        "driver_id",
        "train",
        "service_order",
        "number_of_coaches",
        "origin_station_id",
        "destination_station_id",
        'status',

    ];


    /**  @return HasMany<License>*/
    public function licenses(): HasMany
    {
        return $this->hasMany(
            related: License::class,
            foreignKey: 'journey_id',
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

    /** @return HasOne<Location> */
    public function activeLocation(): HasOne
    {
        return $this->hasOne(
            related: Location::class,
            foreignKey: 'journey_id',
        )->where('status', true)
            ->with([
                'loop',
                'section',
                'station',
            ]);
    }


    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
}
