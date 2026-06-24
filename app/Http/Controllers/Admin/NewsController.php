<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService $newsService,
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('News/Index', [
            'news'    => $this->newsService->list($request->only('search', 'type', 'published')),
            'filters' => $request->only('search', 'type', 'published'),
        ]);
    }

    public function store(StoreNewsRequest $request)
    {
        $this->newsService->store(
            $request->safe()->except('image'),
            $request->user(),
            $request->file('image'),
        );

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.created')]);
    }

    public function update(StoreNewsRequest $request, News $news)
    {
        $this->newsService->update(
            $news,
            $request->safe()->except('image'),
            $request->file('image'),
        );

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.updated')]);
    }

    public function destroy(News $news)
    {
        abort_unless(request()->user()->isAdmin(), 403);
        $this->newsService->delete($news);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.deleted')]);
    }

    public function publish(News $news)
    {
        $this->newsService->publish($news);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.published')]);
    }

    public function unpublish(News $news)
    {
        $this->newsService->unpublish($news);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.unpublished')]);
    }
}
