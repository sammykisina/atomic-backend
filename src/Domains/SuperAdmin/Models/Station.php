<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\SeniorEngineer\Models\Speed;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class Station extends Model
{
    use HasFactory;

    /** @return BelongsTo<Line>  */
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /** @return HasMany<Loop>  */
    public function loops(): HasMany
    {
        return $this->hasMany(
            related: Loop::class,
            foreignKey: 'station_id',
        );
    }

    /** @return HasMany<Section, $this> */
    public function triplines(): HasMany
    {
        return $this->hasMany(
            related: Section::class,
            foreignKey: 'triplinestation_id',
        );
    }

    /** @return HasOne<Section> */
    public function section(): HasOne
    {
        return $this->hasOne(
            related: Section::class,
            foreignKey: 'station_id',
            localKey: 'id',
        );
    }

    /** @return MorphMany */
    public function speeds(): MorphMany
    {
        return $this->morphMany(
            related: Speed::class,
            name: 'areable',
        );
    }

    /**
     * @return array{has_trip_line: string, is_yard: string, position_from_line: string, status: string}
     */
    protected function casts(): array
    {
        return [
            'position_from_line' => 'integer',
            'is_yard' => 'boolean',
            'status' => StationSectionLoopStatuses::class,
            'has_trip_line' => 'boolean',
        ];
    }
}
