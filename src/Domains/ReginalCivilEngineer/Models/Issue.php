<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Issue extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'latitude',
        'longitude',
        'inspection_id',
        'condition',
        'description',
        'image_url',
    ];
}
