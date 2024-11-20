<?php

declare(strict_types=1);

use Domains\Shared\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function (): void {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->everyMinute();

// Schedule::call(fn() =>  Log::info('here'))->everyMinute();

Schedule::call(function () {
    // dd();
    Log::info('here');
    // // dd('Task executed');

    // $user = User::where('id', 1)->first();
    // $user->delete();

    return 0;
})->everyMinute();

// tr4n5I0g1cART
