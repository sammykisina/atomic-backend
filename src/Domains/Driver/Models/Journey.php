<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeDriver(Builder $query): Builder
    {
        return $query->where(column: 'driver_id', operator: '===', value: Auth::id());
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
}
