<?php

declare(strict_types=1);

namespace Domains\Inspector\Models;

use Carbon\Carbon;
use Domains\PermanentWayInspector\Models\InspectionSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Inspection extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'inspection_schedule_id',
        'date',
        'start_time',
        'end_time',
        'is_active',
        'aborted_time',
        'reason_for_abortion',
        'inspector_reached_origin',
        'inspector_reached_destination',
    ];


    /** @return HasMany<Inspection>*/
    public function issues(): HasMany
    {
        return $this->hasMany(
            related: Issue::class,
            foreignKey: 'inspection_id',
        );
    }

    /** @return BelongsTo<InspectionSchedule>*/
    public function inspectionSchedule(): BelongsTo
    {
        return $this->belongsTo(
            related: InspectionSchedule::class,
            foreignKey: 'inspection_schedule_id',
        );
    }

    /**
     * SCOPE INSPECTIONS BASED ON DATE
     * @param Builder $query
     * @param mixed $date
     * @return Builder
     */
    public function scopeCreatedAt(Builder $query, $date): Builder
    {
        return $query->whereDate('created_at', Carbon::parse($date));
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'inspector_reached_origin' => 'boolean',
            'inspector_reached_destination' => 'boolean',
        ];
    }
}
