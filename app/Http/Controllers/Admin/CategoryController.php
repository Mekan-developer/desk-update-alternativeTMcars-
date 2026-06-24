<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children.children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ru'   => 'required|string|max:255',
            'name_tk'   => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order'     => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name_ru'] . '-' . uniqid());
        $data['level'] = $data['parent_id']
            ? (Category::find($data['parent_id'])?->level ?? 0) + 1
            : 1;

        Category::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Категория добавлена']);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name_ru'   => 'sometimes|string|max:255',
            'name_tk'   => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order'     => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Категория обновлена']);
    }

    public function destroy(Request $request, Category $category)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $category->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Категория удалена']);
    }

    public function toggle(Category $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $item) {
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return back();
    }
}
