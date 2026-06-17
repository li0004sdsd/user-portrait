<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPortraitController;
use App\Http\Controllers\TagCategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\RecommendationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::apiResource('portraits', UserPortraitController::class);
    Route::apiResource('tag-categories', TagCategoryController::class)->except(['show']);
    Route::apiResource('tags', TagController::class)->except(['show']);

    Route::get('/analysis', [AnalysisController::class, 'index']);
    Route::get('/recommendations', [RecommendationController::class, 'index']);
});
