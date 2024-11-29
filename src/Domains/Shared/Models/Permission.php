<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Permission extends Model
{
    use HasFactory;

    /** @var  array<int, string> */
    protected $fillable = [
        'role_id',
        'module',
        'abilities',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'abilities' => 'array',
        ];
    }
}
