<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\Driver\Enums\LicenseDirections;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Line;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class License extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'journey_id',
        'line_id',
        'section_id',
        'station_id',
        'driver_id',
        'loop_id',
        'status',
        'direction',
        'issuer_id',
        'rejector_id',
        'issued_at',
        'rejected_at',
        'confirmed_at',
    ];

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'status' => LicenseStatuses::class,
            'direction' => LicenseDirections::class,
        ];
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
            foreignKey: 'line_id',
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

    /** @return BelongsTo<Station>*/
    public function main(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'main_id',
        );
    }

    /** @return BelongsTo<User>*/
    public function driver(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'driver_id',
        );
    }

    /** @return BelongsTo<User>*/
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'issuer_id',
        );
    }

    /** @return BelongsTo<User>*/
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'rejector_id',
        );
    }

    /** @return BelongsTo<Loop>*/
    public function loop(): BelongsTo
    {
        return $this->belongsTo(
            related: Loop::class,
            foreignKey: 'loop_id',
        );
    }


}
