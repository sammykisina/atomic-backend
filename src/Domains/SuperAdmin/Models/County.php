<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Database\Factories\CountyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class County extends Model
{
    use HasFactory;

    /** @var array<int string> */
    protected $fillable = [
        "name",
    ];

    /** @return CountyFactory */
    protected static function newFactory(): CountyFactory
    {
        return new CountyFactory();
    }
}
