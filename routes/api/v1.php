<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RegionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(\App\Http\Middleware\SetApiLocale::class)->group(function () {
    // Аутентификация по SMS (регистрация и вход — один сценарий)
    Route::prefix('auth')->group(function () {
        Route::post('/send-code', [AuthController::class, 'sendCode'])->middleware('throttle:5,1');
        Route::post('/verify', [AuthController::class, 'verify'])->middleware('throttle:10,1');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    // Профиль текущего пользователя
    Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
        Route::post('/phone/send-code', [ProfileController::class, 'sendPhoneCode'])->middleware('throttle:5,1');
        Route::post('/phone/confirm', [ProfileController::class, 'confirmPhone'])->middleware('throttle:10,1');
    });

    // Новости (публичные)
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{news}', [NewsController::class, 'show']);

    // Категории (публичные, дерево для мобильного приложения)
    Route::get('/categories', [CategoryController::class, 'index']);

    // Регионы и города (публичные, для форм регистрации/профиля/объявлений)
    Route::get('/regions', [RegionController::class, 'index']);

    // TODO: Добавить остальные resources
    // - Listings (объявления)
    // - Videos (ролики)
    // - Reviews (отзывы)
    // - Chat (сообщения)
});
