<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'me']);

    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('tasks', TaskController::class);

    Route::post('tasks', [TaskController::class, 'store'])
        ->middleware('permission:create task');
});
