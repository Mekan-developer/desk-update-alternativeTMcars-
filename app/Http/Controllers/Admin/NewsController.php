<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::when($request->search, fn($q, $s) => $q->where('title_ru', 'like', "%$s%"))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('News/Index', [
            'news'    => $news,
            'filters' => $request->only('search', 'type'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ru'    => 'required|string|max:255',
            'title_tk'    => 'required|string|max:255',
            'body_ru'     => 'required|string',
            'body_tk'     => 'required|string',
            'type'        => 'required|in:regular,advertising',
            'link_type'   => 'nullable|string',
            'link_id'     => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        if (! empty($data['is_published']) && $data['is_published']) {
            $data['published_at'] = now();
        }

        News::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Новость создана']);
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title_ru'    => 'sometimes|string|max:255',
            'title_tk'    => 'sometimes|string|max:255',
            'body_ru'     => 'sometimes|string',
            'body_tk'     => 'sometimes|string',
            'type'        => 'sometimes|in:regular,advertising',
            'link_type'   => 'nullable|string',
            'link_id'     => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $news->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Новость обновлена']);
    }

    public function destroy(Request $request, News $news)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $news->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Новость удалена']);
    }

    public function publish(News $news)
    {
        $news->update(['is_published' => true, 'published_at' => now()]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Опубликовано']);
    }

    public function unpublish(News $news)
    {
        $news->update(['is_published' => false]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Снято с публикации']);
    }
}
