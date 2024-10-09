<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\SuperAdmin\Enums\StationPositions;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Station extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "start_kilometer",
        "end_kilometer",

        'start_latitude_top',
        'start_longitude_top',
        'start_latitude_bottom',
        'start_longitude_bottom',
        'end_latitude_top',
        'end_longitude_top',
        'end_latitude_bottom',
        'end_longitude_bottom',

        "line_id",
        'is_yard',
        'position_from_line',

        'status',
    ];

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

    /** @return HasOne<Section> */
    public function section(): HasOne
    {
        return $this->hasOne(
            related: Section::class,
            foreignKey: 'station_id',
            localKey: 'id',
        );
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'position_from_line' => StationPositions::class,
            'is_yard' => 'boolean',
            'status' => StationSectionLoopStatuses::class,
        ];
    }
}
