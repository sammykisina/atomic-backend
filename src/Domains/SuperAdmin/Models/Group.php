<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Group extends Model
{
    use LogsActivity;


    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
        'stations',
    ];

    /** @return HasMany<Desk>*/
    public function desks(): HasMany
    {
        return $this->hasMany(
            related: Desk::class,
            foreignKey: 'group_id',
        );
    }

    /**  @return HasManyThrough<Shift> */
    public function shifts(): HasManyThrough
    {
        return $this->hasManyThrough(related: Shift::class, through: Desk::class);
    }

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'stations' => 'json',
        ];
    }
}
