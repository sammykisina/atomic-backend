<?php

declare(strict_types=1);

namespace Domains\ChiefCivilEngineer\Models;

use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Region;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserRegion extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'region_id',
        'line_id',
        'start_station_id',
        'end_station_id',
        'type',
        'is_active',
        'start_kilometer',
        'end_kilometer',
        'owner_id',
    ];


    /** @return BelongsTo<User> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'user_id',
        );
    }

    /** @return BelongsTo<User> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'owner_id',
        );
    }

    /** @return BelongsTo<Line> */
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /** @return BelongsTo<Region> */
    public function region(): BelongsTo
    {
        return $this->belongsTo(
            related: Region::class,
            foreignKey: 'region_id',
        );
    }

    /** @return BelongsTo<Station> */
    public function startStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'start_station_id',
        );
    }

    /** @return BelongsTo<Station> */
    public function endStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'end_station_id',
        );
    }
}
