<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\VideoProgressController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// View statistics increment route
Route::post('/views/increment', [ViewController::class, 'increment']);

// Personal recommendations route
Route::get('/recommendations', [RecommendationController::class, 'getRecommendations']);

// Video Player Core & Progress Tracking routes
Route::post('/video-progress/save', [VideoProgressController::class, 'save']);
Route::post('/video-progress/resume', [VideoProgressController::class, 'resume']);
Route::get('/shows/{type}/{id}/video-metadata', [VideoProgressController::class, 'getVideoMetadata']);


