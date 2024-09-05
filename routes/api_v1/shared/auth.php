<?php

declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Auth\LoginController;
use App\Http\Controllers\Domains\Shared\Auth\RequestPasswordRestCodeController;
use App\Http\Controllers\Domains\Shared\Auth\RestPasswordController;
use Illuminate\Support\Facades\Route;

// LOGIN ROUTES
Route::post('/login', LoginController::class)->name(name: "login");

// REQUEST PASSWORD RESET CODE
Route::post('/request-password-rest-code', RequestPasswordRestCodeController::class)->name(name: "request-password-rest-code");

// RESET PASSWORD
Route::post('/reset-password', RestPasswordController::class)->name(name: "reset-password");
