<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Inspection extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'inspection_schedule_id',
        'date',
        'time',
        'is_active',
    ];
}
