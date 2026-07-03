<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Новости (публичные)
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{news}', [NewsController::class, 'show']);

    // Категории (публичные, дерево для мобильного приложения)
    Route::get('/categories', [CategoryController::class, 'index']);

    // TODO: Добавить остальные resources
    // - Users (профили)
    // - Listings (объявления)
    // - Videos (ролики)
    // - Reviews (отзывы)
    // - Chat (сообщения)
    // - Auth (аутентификация)
});
