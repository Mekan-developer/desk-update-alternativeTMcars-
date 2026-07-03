<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    /**
     * Дерево активных категорий (до 3 уровней) для мобильного приложения.
     * GET /api/v1/categories
     */
    public function index()
    {
        return response()->json([
            'data' => CategoryResource::collection($this->categoryService->activeTree()),
            'message' => 'Success',
        ]);
    }
}
