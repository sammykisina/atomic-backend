<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Walk extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'inspection_id',
        'latitude',
        'longitude',
        'time',
    ];
}
