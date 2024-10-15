<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Models;

use Domains\Inspector\Models\Issue;
use Domains\RegionalCivilEngineer\Enums\SpeedSuggestionStatuses;
use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class IssueArea extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'issue_id',
        'line_id',
        'section_id',
        'station_id',
        'speed_suggestion',
        'speed_suggestion_comment',
        'speed_suggestion_status',
        'proposed_speed',
        'proposed_speed_comment',
    ];

    /** @return BelongsTo<Issue>*/
    public function issue(): BelongsTo
    {
        return $this->belongsTo(
            related: Issue::class,
            foreignKey: 'issue_id',
        );
    }

    /** @return BelongsTo<Line>*/
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /** @return BelongsTo<Section>*/
    public function section(): BelongsTo
    {
        return $this->belongsTo(
            related: Section::class,
            foreignKey: 'section_id',
        );
    }

    /** @return BelongsTo<Station>*/
    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }


    /** @return array<string, mixed>*/
    public function casts(): array
    {
        return [
            'speed_suggestion_status' => SpeedSuggestionStatuses::class,
        ];
    }
}
