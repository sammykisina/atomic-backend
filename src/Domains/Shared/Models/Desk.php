<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Database\Factories\DeskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Desk extends Model
{
    use HasFactory;

    /**  @var array<int, string> */
    protected $fillable = [
        'name',
    ];


    /** @return DeskFactory */
    protected static function newFactory(): DeskFactory
    {
        return new DeskFactory();
    }
}
