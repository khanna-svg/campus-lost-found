<?php

namespace App\Http\Controllers;

use App\Enums\FoundItemStatus;
use App\Http\Requests\StoreFoundItemRequest;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Services\SupabaseStorageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FoundItemController extends Controller
{
    /**
     * Show the form for reporting a found item.
     */
    public function create(): View
    {
        $categories = ItemCategory::where('is_active', true)->orderBy('name')->get();

        return view('found-items.create', compact('categories'));
    }

    /**
     * Store a newly reported found item.
     */
    public function store(StoreFoundItemRequest $request, SupabaseStorageService $storage): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $photoPath = $storage->upload($request->file('photo'), null, 'found');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Failed to upload photo: ' . $e->getMessage());
        }

        $foundItem = new FoundItem();
        $foundItem->user_id = $request->user()->id;
        $foundItem->category_id = $validated['category_id'];
        $foundItem->item_name = $validated['item_name'];
        $foundItem->public_description = $validated['public_description'];
        $foundItem->date_found = $validated['date_found'];
        $foundItem->location_found = $validated['location_found'];
        $foundItem->photo_path = $photoPath;
        $foundItem->private_details = $validated['private_details'];
        $foundItem->remarks = $validated['remarks'] ?? null;
        $foundItem->status = FoundItemStatus::PendingReview;
        $foundItem->save();

        return redirect()->route('dashboard')->with('success', 'Found item reported successfully! It is now pending review and will be secured in the campus lost and found repository.');
    }

    /**
     * Display the specified found item report.
     */
    public function show(Request $request, FoundItem $foundItem): View
    {
        $user = $request->user();

        // Private details and remarks are strictly visible only to the finder or administrators
        $isAuthorized = $user->isAdmin() || $foundItem->user_id === $user->id;

        return view('found-items.show', compact('foundItem', 'isAuthorized'));
    }
}
