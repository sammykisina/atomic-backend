<?php


declare(strict_types=1);

use App\Http\Controllers\Domains\Shared\Staff\Roles\IndexController;
use App\Http\Controllers\Domains\Shared\Staff\Roles\ManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:get-roles,create-roles,edit-roles,delete-roles'])->group(function (): void {
    Route::get('/', IndexController::class)->name(name: "index");

    Route::controller(ManagementController::class)->group(function (): void {
        Route::post('/', 'create')->name(name: 'create');
        Route::patch('/{role}/edit', 'edit')->name(name: 'edit');

        Route::post('/{role}/permissions/create', 'createPermissions')->name(name: 'create-permissions');
        Route::delete('/permissions/{permission}/revoke', 'revokePermission')->name(name: 'revoke-permission');
        Route::patch('/permissions/{permission}/ability-revoke', 'revokeAbility')->name(name: 'revoke-ability');

        Route::patch('/{role}/{user}', 'assignUserRole')->name(name: 'assign-role');
    });
});
