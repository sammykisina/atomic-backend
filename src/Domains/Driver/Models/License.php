<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\Driver\Enums\LicenseDirections;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Shared\Models\User;
use Domains\SuperAdmin\Models\Loop;
use Domains\SuperAdmin\Models\Section;
use Domains\SuperAdmin\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class License extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'license_number',
        'journey_id',
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
            'issued_at' => 'datetime',
            'rejected_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
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
    public function originStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'origin_station_id',
        );
    }

    /** @return BelongsTo<Station>*/
    public function destinationStation(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'destination_station_id',
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

    public function paths(): HasMany
    {
        return $this->hasMany(
            related: Path::class,
            foreignKey: 'license_id',
        );
    }
}
