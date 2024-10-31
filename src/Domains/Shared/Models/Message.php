<?php

declare(strict_types=1);

namespace Domains\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Message extends Model
{
    use LogsActivity;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'message',
        'sender_id',
        'receiver_id',
        'read_at',
    ];

    /** @return BelongsTo<User>*/
    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'sender_id',
        );
    }


    /** @return BelongsTo<User>*/
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'receiver_id',
        );
    }

    /** @return LogOptions */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
