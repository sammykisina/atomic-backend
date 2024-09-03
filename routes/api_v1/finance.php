<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// INCOME ROUTES
Route::prefix('income')->as('income:')
    ->group(base_path('routes/api_v1/finance/income.php'));
