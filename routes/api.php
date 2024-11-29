<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('v1:')->group(base_path('routes/api_v1.php'));
// Route::prefix('v2')->as('v2:')->group(base_path('routes/api_v2.php'));
