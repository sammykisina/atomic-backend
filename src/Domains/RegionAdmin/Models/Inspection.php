<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Inspection extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'inspector_id',
        'time',
        'active',
        'status',
        'line_id',
        'start_kilometer',
        'end_kilometer',
    ];
}
