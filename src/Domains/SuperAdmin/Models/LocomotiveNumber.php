<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LocomotiveNumber extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'number',
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
}
