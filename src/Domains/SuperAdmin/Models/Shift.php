<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\Operator\Enums\ShiftStatuses;
use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Shift extends Model
{
    use HasFactory;
    use LogsActivity;

    /** @var array<int, string> */
    protected $fillable = [
        'desk_id',
        'user_id',
        'day',
        'from',
        'to',
        'status',
        'active',
    ];

    /** @return BelongsTo<User>*/
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'user_id',
        );
    }

    /** @return BelongsTo<Desk>*/
    public function desk(): BelongsTo
    {
        return $this->belongsTo(
            related: Desk::class,
            foreignKey: 'desk_id',
        );
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'status' => ShiftStatuses::class,
            'active'  => 'boolean',
        ];
    }

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
