<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Region extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'creator_id',
        'updater_id',
    ];

    /** @return RegionFactory */
    protected static function newFactory(): RegionFactory
    {
        return new RegionFactory();
    }
}
