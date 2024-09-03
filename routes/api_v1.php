<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// FINANCE ROUTES
Route::prefix('/finance')->as('finance:')->group(
    base_path('routes/api_v1/finance.php'),
);
