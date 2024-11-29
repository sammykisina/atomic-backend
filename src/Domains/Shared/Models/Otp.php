<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Otp extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**  @var array<int, string> */
    protected $fillable = [
        "type",
        "code",
        "active",
        'user_id',
        'updated_at',
    ];
}
