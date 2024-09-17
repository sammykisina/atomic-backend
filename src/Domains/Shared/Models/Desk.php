<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Database\Factories\DeskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Desk extends Model
{
    use HasFactory;

    /**  @var array<int, string> */
    protected $fillable = [
        'name',
    ];

    /** @return BelongsToMany<User> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'desks_users',
            foreignPivotKey: 'desk_id',
            relatedPivotKey: 'user_id',
        );
    }

    /** @return DeskFactory */
    protected static function newFactory(): DeskFactory
    {
        return new DeskFactory();
    }
}
