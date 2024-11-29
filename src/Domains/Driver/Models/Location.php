<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Location extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'journey_id',
        'station_id',
        'loop_id',
        'section_id',
        'status',

        'latitude',
        'longitude',
    ];

    /** @return BelongsTo<Station> */
    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }

    /** @return BelongsTo<Loop> */
    public function loop(): BelongsTo
    {
        return $this->belongsTo(
            related: Loop::class,
            foreignKey: 'loop_id',
        );
    }

    /** @return BelongsTo<Section> */
    public function section(): BelongsTo
    {
        return $this->belongsTo(
            related: Section::class,
            foreignKey: 'section_id',
        );
    }
}
