<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ApproveListingAction;
use App\Actions\BoostListingAction;
use App\Actions\RejectListingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\RejectionReason;
use App\Services\ListingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListingController extends Controller
{
    public function __construct(
        private readonly ListingService $listingService,
        private readonly ApproveListingAction $approveAction,
        private readonly RejectListingAction $rejectAction,
        private readonly BoostListingAction $boostAction,
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Listings/Index', [
            'listings'         => $this->listingService->list($request->only('status', 'category_id', 'search')),
            'categories'       => Category::whereNull('parent_id')->get(),
            'rejectionReasons' => RejectionReason::where('type', 'listing')->where('is_active', true)->get(),
            'filters'          => $request->only('status', 'category_id', 'search'),
            'counts'           => $this->listingService->counts(),
        ]);
    }

    public function show(Listing $listing)
    {
        return Inertia::render('Listings/Show', [
            'listing'          => $listing->load('user', 'category.parent.parent', 'region', 'city', 'media', 'rejectionReason'),
            'rejectionReasons' => RejectionReason::where('type', 'listing')->where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Listing $listing)
    {
        $this->listingService->list([]);
        $listing->update($request->only('title', 'description', 'price'));

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.updated')]);
    }

    public function destroy(Listing $listing)
    {
        abort_unless(request()->user()->isAdmin(), 403);
        $this->listingService->delete($listing);

        return redirect()->route('listings.index')
            ->with('toast', ['type' => 'success', 'message' => __('messages.deleted')]);
    }

    public function approve(Listing $listing)
    {
        $this->approveAction->execute($listing);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.listing_approved')]);
    }

    public function reject(RejectListingRequest $request, Listing $listing)
    {
        $this->rejectAction->execute($listing, $request->validated('rejection_reason_id'));

        return back()->with('toast', ['type' => 'error', 'message' => __('messages.listing_rejected')]);
    }

    public function boost(Listing $listing)
    {
        $this->boostAction->execute($listing);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.listing_boosted')]);
    }
}
