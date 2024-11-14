<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Domains\Shared\Enums\AtomikLogsTypes;
use Domains\SuperAdmin\Models\LocomotiveNumber;
use Domains\SuperAdmin\Models\Train;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class AtomikLog extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'type',
        'resourceble_id',
        'resourceble_type',
        'actor_id',
        'receiver_id',
        'current_location',
        'train_id',
        'locomotive_number_id',
    ];

    /**
     * GET RESOURCE TYPE
     * @param mixed $namespaceString
     * @return string
     */
    public static function getResourcebleType($namespaceString): string
    {
        $parts = explode(separator: '\\', string: $namespaceString);
        return end($parts);
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'type' => AtomikLogsTypes::class,
        ];
    }

    /** @return MorphTo */
    public function resourceble(): MorphTo
    {
        return $this->morphTo(name: 'resourceble');
    }

    /**  @return BelongsTo<User> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'actor_id',
        );
    }

    /**  @return BelongsTo<User> */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'receiver_id',
        );
    }

    /**  @return BelongsTo<LocomotiveNumber> */
    public function locomotive(): BelongsTo
    {
        return $this->belongsTo(
            related: LocomotiveNumber::class,
            foreignKey: 'locomotive_number_id',
        );
    }

    /**  @return BelongsTo<Train> */
    public function train(): BelongsTo
    {
        return $this->belongsTo(
            related: Train::class,
            foreignKey: 'train_id',
        );
    }
}
