<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Obc extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'serial_number',
    ];

    /** @return HasOne<LocomotiveNumber> */
    public function locomotiveNumber(): HasOne
    {
        return $this->hasOne(
            related: LocomotiveNumber::class,
            foreignKey: 'obc_id',
        );
    }
}
