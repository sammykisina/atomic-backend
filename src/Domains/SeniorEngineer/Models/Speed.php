<?php

declare(strict_types=1);

namespace Domains\SeniorEngineer\Models;

use Domains\SuperAdmin\Models\Line;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Speed extends Model
{
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'areable_id',
        'areable_type',
        'line_id',
        'start_kilometer',
        'end_kilometer',
        'speed',
        'start_kilometer_latitude',
        'start_kilometer_longitude',
        'end_kilometer_latitude',
        'end_kilometer_longitude',
    ];

    /** @return BelongsTo<Line>*/
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /** @return MorphTo */
    public function areable(): MorphTo
    {
        return $this->morphTo(name: 'areable');
    }
}
