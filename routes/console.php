<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function (): void {
    Log::info('Running scheduled command in route console');
})->everyMinute();
