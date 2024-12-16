<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;

final class Obc extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'serial_number',
    ];
}
