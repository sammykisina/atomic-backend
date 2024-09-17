<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Loop extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'distance',
        'position',
        'station_id',
        'line_id',

        'start_latitude_top',
        'start_longitude_top',
        'start_latitude_bottom',
        'start_longitude_bottom',
        'end_latitude_top',
        'end_longitude_top',
        'end_latitude_bottom',
        'end_longitude_bottom',
    ];

    /** @return BelongsTo<Station> */
    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }
}
