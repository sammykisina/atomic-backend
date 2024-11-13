<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\Driver\Enums\LicenseDirections;
use Domains\SuperAdmin\Models\Train;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Journey extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        "train_id",
        'is_authorized',
        'is_active',
        'shifts',
        'direction',
        'last_destination',
    ];


    /** @return BelongsTo<Train>*/
    public function train(): BelongsTo
    {
        return $this->belongsTo(
            related: Train::class,
            foreignKey: 'train_id',
        );
    }

    /**  @return HasMany<License>*/
    public function licenses(): HasMany
    {
        return $this->hasMany(
            related: License::class,
            foreignKey: 'journey_id',
        );
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_authorized' => 'boolean',
            'shifts' => 'json',
            'last_destination' => 'json',
            'direction' => LicenseDirections::class,
        ];
    }
}
