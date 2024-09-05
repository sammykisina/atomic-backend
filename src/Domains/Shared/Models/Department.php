<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Department extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'updater_id',
    ];

    /** @return DepartmentFactory */
    protected static function newFactory(): DepartmentFactory
    {
        return new DepartmentFactory();
    }
}
