<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\RejectionReason;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $listings = Listing::with('user', 'category', 'region', 'city', 'media')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category_id, fn($q, $c) => $q->where('category_id', $c))
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Listings/Index', [
            'listings'         => $listings,
            'categories'       => Category::whereNull('parent_id')->get(),
            'rejectionReasons' => RejectionReason::where('type', 'listing')->where('is_active', true)->get(),
            'filters'          => $request->only('status', 'category_id', 'search'),
            'counts'           => [
                'pending'  => Listing::where('status', 'pending')->count(),
                'approved' => Listing::where('status', 'approved')->count(),
                'rejected' => Listing::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(Listing $listing)
    {
        $listing->load('user', 'category', 'region', 'city', 'media', 'rejectionReason');

        return Inertia::render('Listings/Show', [
            'listing'          => $listing,
            'rejectionReasons' => RejectionReason::where('type', 'listing')->where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Listing $listing)
    {
        $listing->update($request->only('title', 'description', 'price', 'status'));

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(Request $request, Listing $listing)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $listing->delete();

        return redirect()->route('listings.index')
            ->with('toast', ['type' => 'success', 'message' => 'Объявление удалено']);
    }

    public function approve(Listing $listing)
    {
        $listing->update(['status' => 'approved', 'rejection_reason_id' => null]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Объявление одобрено']);
    }

    public function reject(Request $request, Listing $listing)
    {
        $data = $request->validate(['rejection_reason_id' => 'required|exists:rejection_reasons,id']);
        $listing->update(['status' => 'rejected', 'rejection_reason_id' => $data['rejection_reason_id']]);

        return back()->with('toast', ['type' => 'error', 'message' => 'Объявление отклонено']);
    }

    public function boost(Listing $listing)
    {
        $listing->update(['is_boosted' => true, 'boosted_at' => now()]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Объявление поднято']);
    }
}
