<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlatController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RecommendationController;
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

//category
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('/categories/{category}/plats', [CategoryController::class, 'addPlats']);
//profil 
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
 
    //ingredient
    Route::get('/ingredients', [IngredientController::class, 'index']);
    Route::post('/ingredients', [IngredientController::class, 'store']);
    Route::post('/plats/{plat}/ingredients', [IngredientController::class, 'attachToPlat']);

    //recommendation
    Route::post('/recommendations/analyze/{plate_id}', [RecommendationController::class, 'analyze']);
    Route::get('/recommendations/{recommendation_id}', [RecommendationController::class, 'show']);

    Route::middleware('auth:sanctum')->get('/admin/stats', [RecommendationController::class, 'adminStats']);
});
