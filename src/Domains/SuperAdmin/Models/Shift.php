<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\Operator\Enums\ShiftStatuses;
use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Shift extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'desk_id',
        'user_id',
        'line_id',
        'shift_start_station_id',
        'shift_end_station_id',
        'day',
        'from',
        'to',
        'status',
        'stations',
        'creator_id',
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

    /** @return BelongsTo<Station>*/
    public function startStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'shift_start_station_id',
        );
    }

    /** @return BelongsTo<Station>*/
    public function endStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'shift_end_station_id',
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

    /** @return BelongsTo<Line>*/
    public function line(): BelongsTo
    {
        return $this->belongsTo(
            related: Line::class,
            foreignKey: 'line_id',
        );
    }

    /** @return BelongsTo<User> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'creator_id',
        );
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'status' => ShiftStatuses::class,
            'stations' => 'json',
        ];
    }
}
