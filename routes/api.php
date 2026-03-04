<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SlideshowController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('news', NewsController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('slideshows', SlideshowController::class);