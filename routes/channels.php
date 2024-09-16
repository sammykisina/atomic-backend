<?php

declare(strict_types=1);

use Domains\Shared\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notification.{id}', fn(User $user, $id) => (int) $user->id === (int) $id);
