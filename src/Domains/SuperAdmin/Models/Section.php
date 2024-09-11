<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

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
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(
            related: Station::class,
            foreignKey: 'station_id',
        );
    }
}
