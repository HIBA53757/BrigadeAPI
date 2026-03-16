<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlatController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

//plats
    Route::get('/plats', [PlatController::class, 'index']);
    Route::post('/plats', [PlatController::class, 'store']);
    Route::get('/plats/{plat}', [PlatController::class, 'show']);
    Route::put('/plats/{plat}', [PlatController::class, 'update']);
    Route::delete('/plats/{plat}', [PlatController::class, 'destroy']);



});
