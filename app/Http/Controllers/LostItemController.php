<?php

namespace App\Http\Controllers;

use App\Enums\LostItemStatus;
use App\Http\Requests\StoreLostItemRequest;
use App\Models\ItemCategory;
use App\Models\LostItem;
use App\Services\SupabaseStorageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LostItemController extends Controller
{
    /**
     * Show the form for creating a new lost item report.
     */
    public function create(): View
    {
        $categories = ItemCategory::where('is_active', true)->orderBy('name')->get();

        return view('lost-items.create', compact('categories'));
    }

    /**
     * Store a newly created lost item report in storage.
     */
    public function store(StoreLostItemRequest $request, SupabaseStorageService $storage): RedirectResponse
    {
        $validated = $request->validated();
        $photoPath = null;

        if ($request->hasFile('photo')) {
            try {
                $photoPath = $storage->upload($request->file('photo'), null, 'lost');
            } catch (Exception $e) {
                return back()->withInput()->with('error', 'Failed to upload photo: ' . $e->getMessage());
            }
        }

        $lostItem = new LostItem();
        $lostItem->user_id = $request->user()->id;
        $lostItem->category_id = $validated['category_id'];
        $lostItem->item_name = $validated['item_name'];
        $lostItem->description = $validated['description'];
        $lostItem->date_lost = $validated['date_lost'];
        $lostItem->time_lost = $validated['time_lost'] ?? null;
        $lostItem->location_last_seen = $validated['location_last_seen'];
        $lostItem->photo_path = $photoPath;
        $lostItem->identifying_details = $validated['identifying_details'] ?? null;
        $lostItem->status = LostItemStatus::PendingReview;
        $lostItem->save();

        return redirect()->route('dashboard')->with('success', 'Your lost item report has been submitted and is currently pending review by campus administration.');
    }

    /**
     * Display the specified lost item report.
     */
    public function show(Request $request, LostItem $lostItem): View
    {
        $user = $request->user();

        // Ensure user is authorized to view private identifying details (reporter or admin only)
        $isAuthorized = $user->isAdmin() || $lostItem->user_id === $user->id;

        return view('lost-items.show', compact('lostItem', 'isAuthorized'));
    }

    /**
     * Toggle the resolved status of a lost item report.
     */
    public function toggleResolve(Request $request, LostItem $lostItem): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && $lostItem->user_id !== $user->id) {
            abort(403, 'You are not authorized to modify the status of this item.');
        }

        if ($lostItem->status === LostItemStatus::Resolved) {
            $lostItem->status = $lostItem->reviewed_at ? LostItemStatus::Approved : LostItemStatus::PendingReview;
            $lostItem->resolved_at = null;
            $lostItem->save();
            $message = 'Item marked as unresolved.';
        } else {
            if ($lostItem->reviewed_by === null) {
                $lostItem->reviewed_by = $user->id;
                $lostItem->reviewed_at = now();
            }
            $lostItem->status = LostItemStatus::Resolved;
            $lostItem->resolved_at = now();
            $lostItem->save();
            $message = 'Item marked as resolved! We are glad your belonging was found.';
        }

        return back()->with('success', $message);
    }
}
