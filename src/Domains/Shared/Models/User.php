<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Database\Factories\UserFactory;
use Domains\Shared\Enums\ModelStatuses;
use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'employee_id',
        'national_id',
        'type',
        'is_admin',
        'department_id',
        'region_id',
        'image_url',
        'status',
        'work_status',
        'creator_id',
        'updater_id',
        'password',
        'role_id',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return BelongsTo<Department> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(
            related: Department::class,
            foreignKey: 'department_id',
        );
    }

    /** @return BelongsTo<Region> */
    public function region(): BelongsTo
    {
        return $this->belongsTo(
            related: Region::class,
            foreignKey: 'region_id',
        );
    }

    /** @return BelongsTo<Role> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(
            related: Role::class,
            foreignKey: 'role_id',
        );
    }

    /**
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', '!=', $type);
    }

    /** @return UserFactory */
    protected static function newFactory(): UserFactory
    {
        return new UserFactory();
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => ModelStatuses::class,
            'work_status' => WorkStatuses::class,
            'type' => UserTypes::class,
            'is_admin' => 'boolean',
        ];
    }
}
