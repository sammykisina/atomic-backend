<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Path extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'license_id',
        'origin_station_id',
        'origin_main_id',
        'origin_loop_id',

        'section_id',

        'destination_station_id',
        'destination_main_id',
        'destination_loop_id',
    ];

    /**
     * ORIGIN STATION
     * @return BelongsTo<Station>
     */
    public function originStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'origin_station_id',
        );
    }

    /**
     * FROM STATION
     * @return BelongsTo<Station>
     */
    public function fromMainLine(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'origin_main_id',
        );
    }

    /**
     * ORIGIN LOOP STATION
     * @return BelongsTo<Loop>
     */
    public function fromLoop(): BelongsTo
    {
        return $this->belongsTo(
            related: Loop::class,
            foreignKey: 'origin_loop_id',
        );
    }

    /**
     * SECTION
     * @return BelongsTo<Section>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(
            related: Section::class,
            foreignKey: 'section_id',
        );
    }

    /**
     * DESTINATION STATION
     * @return BelongsTo<Station>
     */
    public function destinationStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'destination_station_id',
        );
    }

    /**
     * TO MAIN LINE
     * @return BelongsTo<Station>
     */
    public function toMainLine(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'destination_main_id',
        );
    }

    /**
     * DESTINATION LOOP STATION
     * @return BelongsTo<Loop>
     */
    public function toLoop(): BelongsTo
    {
        return $this->belongsTo(
            related: Loop::class,
            foreignKey: 'destination_loop_id',
        );
    }
}
