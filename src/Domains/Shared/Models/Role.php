<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<int , string> */
    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'updater_id',
    ];


    /** @return HasMany<User> */
    public function permissions(): HasMany
    {
        return $this->hasMany(
            related: Permission::class,
            foreignKey: 'role_id',
        );
    }
}
