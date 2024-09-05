<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Domains\Shared\Enums\ModelStatuses;
use Domains\Shared\Enums\UserRoles;
use Domains\Shared\Enums\WorkStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /** @var array<int, string> */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'employee_id',
        'national_id',
        'role',
        'department_id',
        'region_id',
        'image_url',
        'status',
        'work_status',
        'creator_id',
        'updater_id',
        'password',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => ModelStatuses::class,
            'work_status' => WorkStatuses::class,
            'role' => UserRoles::class,
        ];
    }
}
