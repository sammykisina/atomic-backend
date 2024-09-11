<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

Route::get('/', fn() => [
    'name' => env('ATOMIC API'),
    'version' => App::version()
]);
