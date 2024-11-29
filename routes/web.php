<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => [
    'name' => env('ATOMIC API'),
    'version' => App::version(),
]);
