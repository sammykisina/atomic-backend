<?php

declare(strict_types=1);

namespace Domains\Inspector\Models;

use Domains\Inspector\Enums\IssueStatuses;
use Domains\PermanentWayInspector\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Issue extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'latitude',
        'longitude',
        'inspection_id',
        'issue_name_id',
        'condition',
        'description',
        'image_url',
        'status',
        'issue_kilometer'
    ];

    /** @return BelongsTo<IssueName>*/
    public function issueName(): BelongsTo
    {
        return $this->belongsTo(
            related: IssueName::class,
            foreignKey: 'issue_name_id',
        );
    }


    /** @return BelongsTo<Inspection>*/
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(
            related: Inspection::class,
            foreignKey: 'inspection_id',
        );
    }


    /** @return HasOne<Assignment>*/
    public function assignment(): HasOne
    {
        return $this->hasOne(
            related: Assignment::class,
            foreignKey: 'issue_id',
        );
    }

    public function casts(): array
    {
        return [
            'status' => IssueStatuses::class,
        ];
    }
}
