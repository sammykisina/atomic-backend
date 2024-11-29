<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\SuperAdmin\Models\Shift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Panic extends Model
{
    use LogsActivity;
    use SoftDeletes;


    /** @var array<int, string> */
    protected $fillable = [
        'journey_id',
        'shift_id',
        'issue',
        'description',
        'time',
        'date',
        'latitude',
        'longitude',
        'is_acknowledge',
        'time_of_acknowledge',
        'date_of_acknowledge',
    ];

    /** @return BelongsTo<Journey> */
    public function journey(): BelongsTo
    {
        return $this->belongsTo(
            related: Journey::class,
            foreignKey: 'journey_id',
        );
    }

    /** @return BelongsTo<Shift> */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(
            related: Shift::class,
            foreignKey: 'shift_id',
        );
    }

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'is_acknowledge' => 'boolean',
        ];
    }
}
