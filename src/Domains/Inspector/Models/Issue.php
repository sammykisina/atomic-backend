<?php

declare(strict_types=1);

namespace Domains\Inspector\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /** @return BelongsTo<IssueName>*/
    public function issueName(): BelongsTo
    {
        return $this->belongsTo(
            related: IssueName::class,
            foreignKey: 'issue_name_id',
        );
    }
}
