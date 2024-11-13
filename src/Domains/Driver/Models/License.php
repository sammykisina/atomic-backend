<?php

declare(strict_types=1);

namespace Domains\Driver\Models;

use Domains\Driver\Enums\LicenseDirections;
use Domains\Driver\Enums\LicenseStatuses;
use Domains\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class License extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'license_number',
        'journey_id',
        'status',
        'direction',
        'issuer_id',
        'rejector_id',
        'issued_at',

        'origin',
        'train_at_origin',

        'through',

        'destination',
        'train_at_destination',

        'logs',

        'rejected_at',
        'confirmed_at',
    ];

    /** @return BelongsTo<User>*/
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'issuer_id',
        );
    }

    /** @return BelongsTo<User>*/
    public function journey(): BelongsTo
    {
        return $this->belongsTo(
            related: Journey::class,
            foreignKey: 'journey_id',
        );
    }

    /** @return BelongsTo<User>*/
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'rejector_id',
        );
    }

    /**
     * GET TYPE
     * @param mixed $namespaceString
     * @return string
     */
    public function getType($namespaceString): string
    {
        $parts = explode(separator: '\\', string: $namespaceString);
        return Str::upper(value: end($parts));
    }

    /** @return array<string, mixed> */
    public function casts(): array
    {
        return [
            'status' => LicenseStatuses::class,
            'direction' => LicenseDirections::class,
            'through' => 'json',
            'logs' => 'json',
            'origin' => 'json',
            'destination' => 'json',
            'issued_at' => 'datetime',
            'rejected_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'train_at_destination' => 'boolean',
            'train_at_origin' => 'boolean',
        ];
    }
}
