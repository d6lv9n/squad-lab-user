<?php

use App\Http\Controllers\Api\v1\AuthenticationController;
use App\Http\Controllers\Api\v1\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:100,1'])->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('profile', [AuthenticationController::class, 'profile']);
        Route::post('login', [AuthenticationController::class, 'login']);
        Route::post('signup', [AuthenticationController::class, 'signup']);
        // Route::post('refresh-token', [AuthenticationController::class, 'refreshToken']);
    });

    Route::prefix('users')->group(function () {
        Route::get('get-by-ids', [UsersController::class, 'getByIds']);
        Route::get('search/{query}', [UsersController::class, 'search']);
    });
});
