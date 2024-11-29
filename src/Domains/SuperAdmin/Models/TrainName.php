<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class TrainName extends Model
{
    use LogsActivity;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'is_active',
    ];

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
