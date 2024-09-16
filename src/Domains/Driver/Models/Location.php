<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Location extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'journey_id',
        'station_id',
        'main_id',
        'loop_id',
        'section_id',
        'status',

        'latitude',
        'longitude',
    ];
}
