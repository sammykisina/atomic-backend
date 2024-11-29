<?php

declare(strict_types=1);

namespace Domains\PrincipleEngineer\Models;

use Domains\SeniorTrackInspector\Models\InspectionSchedule;
use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Region;
use Domains\SuperAdmin\Models\Station;
use Domains\TrackAttendant\Models\Inspection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    /** @return  HasMany<InspectionSchedule> */
    public function inspectionSchedules(): HasMany
    {
        return $this->hasMany(
            related: InspectionSchedule::class,
            foreignKey: 'owner_id',
        );
    }

    /**  @return  HasManyThrough */
    public function inspections(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: Inspection::class,
            through: InspectionSchedule::class,
            firstKey: 'owner_id',
            secondKey: 'inspection_schedule_id',
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
