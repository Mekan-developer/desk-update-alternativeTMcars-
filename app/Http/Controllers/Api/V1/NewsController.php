<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Получить опубликованные новости (для мобильного приложения)
     * GET /api/v1/news
     *
     * Query параметры:
     * - type: regular|ad (опционально, фильтр по типу)
     * - page: 1 (пагинация)
     * - limit: 20 (элементов на странице)
     */
    public function index(Request $request)
    {
        $query = News::where('is_published', true)->latest('published_at');

        // Фильтр по типу новости
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        $news = $query->paginate($request->get('limit', 20));

        return response()->json([
            'data' => NewsResource::collection($news->items()),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
            ],
            'links' => [
                'first' => $news->url(1),
                'last' => $news->url($news->lastPage()),
                'prev' => $news->previousPageUrl(),
                'next' => $news->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Получить одну новость по ID
     * GET /api/v1/news/{id}
     */
    public function show(News $news)
    {
        abort_unless($news->is_published, 404);
        return response()->json([
            'data' => new NewsResource($news),
        ]);
    }
}
