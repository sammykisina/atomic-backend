<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Models;

use Domains\Inspector\Models\Issue;
use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Assignment extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'issue_id',
        'gang_men',
        'resolver_id',
        'image_url',
    ];

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'gang_men' => 'json',
        ];
    }

    /** @return BelongsTo<User>*/
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'resolver_id',
        );
    }


    /** @return BelongsTo<Issue>*/
    public function issue(): BelongsTo
    {
        return $this->belongsTo(
            related: Issue::class,
            foreignKey: 'issue_id',
        );
    }
}
