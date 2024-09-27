<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Models;

use Domains\ReginalCivilEngineer\Enums\InspectionScheduleStatuses;
use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Line;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class InspectionSchedule extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'inspector_id',
        'time',
        'status',
        'line_id',
        'start_kilometer',
        'end_kilometer',
        'region_id',
    ];

    /**  @return BelongsTo<Line>*/
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /**  @return BelongsTo<User>*/
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'inspector_id',
        );
    }


    /** @return HasMany<Inspection>*/
    public function inspections(): HasMany
    {
        return $this->hasMany(
            related: Inspection::class,
            foreignKey: 'inspection_schedule_id',
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => InspectionScheduleStatuses::class,
        ];
    }
}
