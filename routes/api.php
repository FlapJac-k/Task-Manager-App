<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'me']);

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('tasks', [TaskController::class, 'index'])
        ->middleware('permission:view tasks');

    Route::put('tasks/{task}', [TaskController::class, 'update'])
        ->middleware('permission:edit tasks');

    Route::post('tasks', [TaskController::class, 'store'])
        ->middleware('permission:create tasks');

    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])
        ->middleware('permission:delete tasks');
});
