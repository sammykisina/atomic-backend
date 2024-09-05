<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// LOGIN ROUTES
Route::post('/login', LoginController::class)->name(name: "login");
