<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Inspection extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'inspection_schedule_id',
        'date',
        'start_time',
        'end_time',
        'is_active',
    ];


    /** @return HasMany<Inspection>*/
    public function issues(): HasMany
    {
        return $this->hasMany(
            related: Issue::class,
            foreignKey: 'inspection_id',
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
