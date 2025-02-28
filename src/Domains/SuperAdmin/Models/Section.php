<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use App\Enums\Superadmin\SectionType;
use Domains\SeniorEngineer\Models\Speed;
use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class Section extends Model
{
    use HasFactory;


    /** @return BelongsTo<Station>*/
    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }

    /** @return MorphMany */
    public function speeds(): MorphMany
    {
        return $this->morphMany(
            related: Speed::class,
            name: 'areable',
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => StationSectionLoopStatuses::class,
            'section_type' => SectionType::class,
        ];
    }

    /**
     * GENERATE FULL NAME
     * @return Attribute
     */
    protected function fullname(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['start_name'] . ' - ' . $attributes['end_name'],
        );
    }

}
