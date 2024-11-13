<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class LocomotiveNumber extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'number',
        'driver_id',
    ];
}
