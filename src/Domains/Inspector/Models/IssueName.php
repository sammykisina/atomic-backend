<?php

declare(strict_types=1);

namespace Domains\Inspector\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class IssueName extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
    ];
}
