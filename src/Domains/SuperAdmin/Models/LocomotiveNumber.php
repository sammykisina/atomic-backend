<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\Driver\Models\Journey;
use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

final class LocomotiveNumber extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'number',
        'obc_id',
        'driver_id',
    ];

    /** @return BelongsTo<User> */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'driver_id',
        );
    }

    /** @return BelongsTo<Obc> */
    public function obc(): BelongsTo
    {
        return $this->belongsTo(
            related: Obc::class,
            foreignKey: 'obc_id',
        );
    }

    /**  @return HasMany<Train> */
    public function trains(): HasMany
    {
        return $this->hasMany(
            related: Train::class,
            foreignKey: 'locomotive_number_id',
        );
    }

    /** @return HasOneThrough<Journey> */
    public function activeJourney(): HasOneThrough
    {
        return $this->hasOneThrough(
            related: Journey::class,
            through: Train::class,
            firstKey: 'id', // LocomotiveNumber's primary key
            secondKey: 'train_id', // Train's foreign key in Journey
            localKey: 'id', // LocomotiveNumber's primary key
            secondLocalKey: 'locomotive_number_id', // Foreign key in Train
        )->where('is_active', true); // Add a condition to filter only active journeys
    }
}
