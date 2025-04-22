<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => [
    'name' => env('APP_NAME'),
    'laravel-version' => App::version(),
]);
