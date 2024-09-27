<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Condition extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'station_id',
        'section_id',
        'loop_id',
        'condition',
        'is_active',
    ];
}
