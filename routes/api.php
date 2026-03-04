<?php

// routes/api.php
// ─────────────────────────────────────────────────────────────
// Add these routes to your routes/api.php file
// ─────────────────────────────────────────────────────────────

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\SlideshowController;
use Illuminate\Support\Facades\Route;

// ── PUBLIC ROUTES ─────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Public news & categories (read-only)
Route::get('/news',            [NewsController::class, 'index']);
Route::get('/news/{id}',       [NewsController::class, 'show']);
Route::get('/categories',      [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/slides',          [SlideshowController::class, 'index']);
Route::get('/slides/{slideshow}', [SlideshowController::class, 'show']);

// ── PROTECTED ROUTES (Sanctum) ────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/profile',          [AuthController::class, 'profile']);
    Route::put('/profile',          [AuthController::class, 'updateProfile']);
    Route::put('/change-password',  [AuthController::class, 'changePassword']);
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::post('/logout-all',      [AuthController::class, 'logoutAll']);

    // Categories (write)
    Route::post('/categories',          [CategoryController::class, 'store']);
    Route::put('/categories/{id}',      [CategoryController::class, 'update']);
    Route::delete('/categories/{id}',   [CategoryController::class, 'destroy']);

    // News (write)
    Route::post('/news',            [NewsController::class, 'store']);
    Route::put('/news/{id}',        [NewsController::class, 'update']);
    Route::delete('/news/{id}',     [NewsController::class, 'destroy']);

    // Slides (write)
    Route::post('/slides',                        [SlideshowController::class, 'store']);
    Route::put('/slides/{slideshow}',             [SlideshowController::class, 'update']);
    Route::delete('/slides/{slideshow}',          [SlideshowController::class, 'destroy']);
    Route::post('/slides/reorder',                [SlideshowController::class, 'reorder']);
    Route::patch('/slides/{slideshow}/toggle',    [SlideshowController::class, 'toggleActive']);
});