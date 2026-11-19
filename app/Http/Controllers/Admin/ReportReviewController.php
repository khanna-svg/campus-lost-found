<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportReviewController extends Controller
{
    /**
     * Browse pending and past reports.
     */
    public function index(Request $request): View
    {
        $type = in_array($request->query('type'), ['lost', 'found']) ? $request->query('type') : 'found';
        $status = $request->query('status', 'pending_review');

        if ($type === 'lost') {
            $query = LostItem::with(['user', 'category'])->latest();
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            $reports = $query->paginate(12)->withQueryString();
        } else {
            $query = FoundItem::with(['user', 'category'])->latest();
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            $reports = $query->paginate(12)->withQueryString();
        }

        $pendingCounts = [
            'found' => FoundItem::where('status', FoundItemStatus::PendingReview)->count(),
            'lost' => LostItem::where('status', LostItemStatus::PendingReview)->count(),
        ];

        return view('admin.reports.index', compact('reports', 'type', 'status', 'pendingCounts'));
    }

    /**
     * Show full lost item report details for administrator review.
     */
    public function showLost(LostItem $lostItem): View
    {
        $lostItem->load(['user', 'category', 'reviewer']);
        return view('admin.reports.show-lost', compact('lostItem'));
    }

    /**
     * Show full found item report details for administrator review.
     */
    public function showFound(FoundItem $foundItem): View
    {
        $foundItem->load(['user', 'category', 'reviewer']);
        return view('admin.reports.show-found', compact('foundItem'));
    }

    /**
     * Approve a lost item report.
     */
    public function approveLost(Request $request, LostItem $lostItem, NotificationService $notifications): RedirectResponse
    {
        $lostItem->update([
            'status' => LostItemStatus::Approved,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'needs_correction' => false,
            'correction_note' => null,
        ]);

        $notifications->reportApproved($lostItem);

        return redirect()->route('admin.reports.index', ['type' => 'lost'])
            ->with('success', "Lost report for '{$lostItem->item_name}' approved and published.");
    }

    /**
     * Reject a lost item report.
     */
    public function rejectLost(Request $request, LostItem $lostItem, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $lostItem->update([
            'status' => LostItemStatus::Rejected,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $notifications->reportRejected($lostItem, $validated['rejection_reason']);

        return redirect()->route('admin.reports.index', ['type' => 'lost'])
            ->with('success', "Lost report for '{$lostItem->item_name}' rejected.");
    }

    /**
     * Request corrections on a lost item report.
     */
    public function correctLost(Request $request, LostItem $lostItem, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'correction_note' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $lostItem->update([
            'status' => LostItemStatus::PendingReview,
            'needs_correction' => true,
            'correction_note' => $validated['correction_note'],
        ]);

        $notifications->reportCorrectionRequested($lostItem, $validated['correction_note']);

        return redirect()->route('admin.reports.show-lost', $lostItem)
            ->with('success', 'Corrections requested from the reporter.');
    }

    /**
     * Approve a found item report.
     */
    public function approveFound(Request $request, FoundItem $foundItem, NotificationService $notifications): RedirectResponse
    {
        $storageLocation = trim((string) $request->input('storage_location', ''));

        $foundItem->update([
            'status' => FoundItemStatus::Approved,
            'storage_location' => $storageLocation !== '' ? $storageLocation : $foundItem->storage_location,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'needs_correction' => false,
            'correction_note' => null,
        ]);

        $notifications->reportApproved($foundItem);

        return redirect()->route('admin.reports.index', ['type' => 'found'])
            ->with('success', "Found report for '{$foundItem->item_name}' approved and published.");
    }

    /**
     * Reject a found item report.
     */
    public function rejectFound(Request $request, FoundItem $foundItem, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $foundItem->update([
            'status' => FoundItemStatus::Archived,
            'rejection_reason' => $validated['rejection_reason'],
            'archived_at' => now(),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $notifications->reportRejected($foundItem, $validated['rejection_reason']);

        return redirect()->route('admin.reports.index', ['type' => 'found'])
            ->with('success', "Found report for '{$foundItem->item_name}' archived and rejected.");
    }

    /**
     * Request corrections on a found item report.
     */
    public function correctFound(Request $request, FoundItem $foundItem, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'correction_note' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $foundItem->update([
            'status' => FoundItemStatus::PendingReview,
            'needs_correction' => true,
            'correction_note' => $validated['correction_note'],
        ]);

        $notifications->reportCorrectionRequested($foundItem, $validated['correction_note']);

        return redirect()->route('admin.reports.show-found', $foundItem)
            ->with('success', 'Corrections requested from the finder.');
    }
}
