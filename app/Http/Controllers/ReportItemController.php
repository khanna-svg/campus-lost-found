<?php

namespace App\Http\Controllers;

use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\LostItem;
use App\Services\SupabaseStorageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportItemController extends Controller
{
    /**
     * Show the unified report form (matching reference).
     */
    public function create(Request $request): View
    {
        $defaultType = $request->query('type') === 'found' ? 'found' : 'lost';

        return view('items.report', compact('defaultType'));
    }

    /**
     * Store a newly created lost or found item.
     */
    public function store(Request $request, SupabaseStorageService $storage): RedirectResponse
    {
        $validated = $request->validate([
            'item_type' => 'required|in:lost,found',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'location' => 'nullable|string|max:255',
            'date' => 'required|date|before_or_equal:today',
            'contact_info' => 'required|string|max:255',
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = $request->user();
        $photoPath = null;

        if ($request->hasFile('photo')) {
            try {
                $photoPath = $storage->upload($request->file('photo'), null, $validated['item_type']);
            } catch (Exception $e) {
                return back()->withInput()->with('error', 'Failed to upload photo: ' . $e->getMessage());
            }
        }

        // Get or fallback category
        $category = ItemCategory::first();

        if ($validated['item_type'] === 'lost') {
            $lostItem = new LostItem();
            $lostItem->user_id = $user->id;
            $lostItem->category_id = $category->id;
            $lostItem->item_name = $validated['title'];
            $lostItem->description = $validated['description'];
            $lostItem->date_lost = $validated['date'];
            $lostItem->location_last_seen = $validated['location'] ?: 'Campus Area';
            $lostItem->photo_path = $photoPath;
            $lostItem->contact_info = $validated['contact_info'];
            $lostItem->status = LostItemStatus::Approved;
            $lostItem->reviewed_by = $user->id;
            $lostItem->reviewed_at = now();
            $lostItem->save();
        } else {
            $foundItem = new FoundItem();
            $foundItem->user_id = $user->id;
            $foundItem->category_id = $category->id;
            $foundItem->item_name = $validated['title'];
            $foundItem->public_description = $validated['description'];
            $foundItem->private_details = $validated['description'];
            $foundItem->date_found = $validated['date'];
            $foundItem->location_found = $validated['location'] ?: 'Campus Area';
            $foundItem->photo_path = $photoPath ?: 'placeholder.jpg';
            $foundItem->contact_info = $validated['contact_info'];
            $foundItem->status = FoundItemStatus::Approved;
            $foundItem->reviewed_by = $user->id;
            $foundItem->reviewed_at = now();
            $foundItem->save();
        }

        return redirect()->route('items.index')->with('success', 'Item posted successfully! It is now listed in the directory.');
    }
}
