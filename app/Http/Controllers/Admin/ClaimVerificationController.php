<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Http\Controllers\Controller;
use App\Models\ClaimRequest;
use App\Models\ItemReturn;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClaimVerificationController extends Controller
{
    /**
     * Browse submitted claims across the campus.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $query = ClaimRequest::with(['claimant', 'foundItem.category'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $claims = $query->paginate(15)->withQueryString();

        $counts = [
            'pending' => ClaimRequest::where('status', ClaimRequestStatus::Pending)->count(),
            'under_review' => ClaimRequest::where('status', ClaimRequestStatus::UnderReview)->count(),
            'info_required' => ClaimRequest::where('status', ClaimRequestStatus::AdditionalInformationRequired)->count(),
            'approved' => ClaimRequest::where('status', ClaimRequestStatus::Approved)->count(),
            'completed' => ClaimRequest::where('status', ClaimRequestStatus::Completed)->count(),
        ];

        return view('admin.claims.index', compact('claims', 'status', 'counts'));
    }

    /**
     * Show claim verification dossier (comparing proof against private characteristics).
     */
    public function show(ClaimRequest $claim): View
    {
        $claim->load([
            'claimant',
            'foundItem.user',
            'foundItem.category',
            'lostItem',
            'reviewer',
            'approver',
            'itemReturn.recordedBy',
        ]);

        return view('admin.claims.show', compact('claim'));
    }

    /**
     * Move claim status to under_review.
     */
    public function underReview(Request $request, ClaimRequest $claim): RedirectResponse
    {
        $claim->update([
            'status' => ClaimRequestStatus::UnderReview,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Claim status updated to Under Review.');
    }

    /**
     * Ask the claimant for additional verifying information.
     */
    public function requestInfo(Request $request, ClaimRequest $claim, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'admin_message' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $claim->update([
            'status' => ClaimRequestStatus::AdditionalInformationRequired,
            'admin_message' => $validated['admin_message'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $notifications->claimInfoRequired($claim, $validated['admin_message']);

        return back()->with('success', 'Request for additional information dispatched to claimant.');
    }

    /**
     * Approve claim.
     */
    public function approve(Request $request, ClaimRequest $claim, NotificationService $notifications): RedirectResponse
    {
        $pickupInstructions = trim((string) $request->input('pickup_instructions', ''));

        DB::transaction(function () use ($request, $claim, $pickupInstructions, $notifications) {
            // Update claim
            $claim->update([
                'status' => ClaimRequestStatus::Approved,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'pickup_instructions' => $pickupInstructions !== '' ? $pickupInstructions : null,
            ]);

            // Mark found item as claimed (trigger requires approved claim)
            $claim->foundItem()->update([
                'status' => FoundItemStatus::Claimed,
            ]);

            $notifications->claimApproved($claim);
        });

        return back()->with('success', 'Claim approved! Item marked as claimed and claimant notified.');
    }

    /**
     * Reject claim.
     */
    public function reject(Request $request, ClaimRequest $claim, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $claim->update([
            'status' => ClaimRequestStatus::Rejected,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $notifications->claimRejected($claim, $validated['rejection_reason']);

        return back()->with('success', 'Claim has been rejected.');
    }

    /**
     * Record physical hand-over of the item at campus security.
     */
    public function recordReturn(Request $request, ClaimRequest $claim, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'returned_to_name' => ['required', 'string', 'max:150'],
            'lrn' => ['required', 'string', 'max:100'],
            'course_year' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $claim, $validated, $notifications) {
            // 1. Create Return Record
            $return = ItemReturn::create([
                'claim_request_id' => $claim->id,
                'found_item_id' => $claim->found_item_id,
                'recorded_by' => $request->user()->id,
                'returned_to_name' => $validated['returned_to_name'],
                'lrn' => $validated['lrn'],
                'course_year' => $validated['course_year'],
                'id_presented' => 'LRN: ' . $validated['lrn'],
                'notes' => $validated['notes'] ?? ('Course & Year: ' . $validated['course_year']),
                'returned_at' => now(),
            ]);

            // 2. Mark found item as returned (trigger requires item_returns record)
            $claim->foundItem()->update([
                'status' => FoundItemStatus::Returned,
            ]);

            // 3. Mark claim as completed (trigger requires item_returns record)
            $claim->update([
                'status' => ClaimRequestStatus::Completed,
                'completed_at' => now(),
            ]);

            // 4. Send hand-over notifications
            $notifications->itemReturned($return);
        });

        return back()->with('success', 'Physical return recorded successfully! Item marked as Returned.');
    }
}
