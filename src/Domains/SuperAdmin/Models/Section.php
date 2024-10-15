<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Domains\SuperAdmin\Enums\StationSectionLoopStatuses;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Section extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'start_name',
        'end_name',
        'start_kilometer',
        'end_kilometer',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'number_of_kilometers_to_divide_section_to_subsection',
        'line_id',
        'station_id',
        'status',
        'speed',
    ];

    /** @return BelongsTo<Station>*/
    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => StationSectionLoopStatuses::class,
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
