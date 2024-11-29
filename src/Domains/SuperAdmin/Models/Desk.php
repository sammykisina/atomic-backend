<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Database\Factories\DeskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Desk extends Model
{
    use HasFactory;
    use LogsActivity;

    /**  @var array<int, string> */
    protected $fillable = [
        'name',
        'group_id',
    ];


    /** @return HasMany<Shift> */
    public function shifts(): HasMany
    {
        return $this->hasMany(
            related: Shift::class,
            foreignKey: 'desk_id',
        );
    }

    /** @return BelongsTo<Group> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            related: Group::class,
            foreignKey: 'group_id',
        );
    }

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }


    /** @return DeskFactory */
    protected static function newFactory(): DeskFactory
    {
        return new DeskFactory();
    }
}
