<?php

declare(strict_types=1);

use App\Http\Controllers\IncomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', IncomeController::class)->name('index');
