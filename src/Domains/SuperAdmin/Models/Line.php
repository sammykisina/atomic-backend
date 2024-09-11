<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\Shared\Models\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Line extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'region_id',
        'county_id',
    ];

    /** @return  BelongsTo<Region> */
    public function Region(): BelongsTo
    {
        return $this->belongsTo(
            related:Region::class,
            foreignKey: 'region_id',
        );
    }

    /** @return  BelongsToMany<County> */
    public function counties(): BelongsToMany
    {
        return $this->belongsToMany(
            related: County::class,
            table: "counties_lines",
            foreignPivotKey: "line_id",
        );
    }

    /** @return  BelongsToMany<Region> */
    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Region::class,
            table: "lines_regions",
            foreignPivotKey: "line_id",
        );
    }

    /** @return  HasMany<Station> */
    public function stations(): HasMany
    {
        return $this->hasMany(
            related: Station::class,
            foreignKey:"line_id",
        );
    }

    /** @return HasMany<Loop> */
    public function loops(): HasMany
    {
        return $this->hasMany(
            related: Loop::class,
            foreignKey: 'line_id',
        );
    }
}
