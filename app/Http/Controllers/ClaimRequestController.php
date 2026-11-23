<?php

namespace App\Http\Controllers;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Http\Requests\StoreClaimRequest;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Models\ItemReturn;
use App\Services\NotificationService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClaimRequestController extends Controller
{
    /**
     * List claim requests submitted by or received by the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $claims = $user->claimRequests()
            ->with(['foundItem.category', 'foundItem.user'])
            ->latest()
            ->paginate(10);

        // Claims received on items reported found by this user
        $receivedClaims = ClaimRequest::whereHas('foundItem', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['foundItem.category', 'claimant', 'itemReturn'])
            ->latest()
            ->get();

        return view('claims.index', compact('claims', 'receivedClaims'));
    }

    /**
     * Show form to initiate a claim for a specific found item.
     */
    public function create(Request $request, FoundItem $foundItem): View|RedirectResponse
    {
        $user = $request->user();

        // Guard against administrators submitting claims
        if ($user->isAdmin()) {
            return redirect()->route('admin.claims.index')
                ->with('error', 'Administrators verify claims and cannot file claim requests.');
        }

        // 1. Guard against claiming own report
        if ($foundItem->user_id === $user->id) {
            return redirect()->route('found-items.show', $foundItem)
                ->with('error', 'You cannot submit a claim on an item that you reported finding.');
        }

        // 2. Guard against non-approved items
        if ($foundItem->status !== FoundItemStatus::Approved) {
            return redirect()->route('found-items.show', $foundItem)
                ->with('error', 'This item is not currently available for claim submission.');
        }

        // 3. Guard against duplicate active claims
        $existingClaim = ClaimRequest::where('found_item_id', $foundItem->id)
            ->where('claimant_id', $user->id)
            ->whereIn('status', [
                ClaimRequestStatus::Pending,
                ClaimRequestStatus::UnderReview,
                ClaimRequestStatus::AdditionalInformationRequired,
            ])
            ->first();

        if ($existingClaim) {
            return redirect()->route('claims.show', $existingClaim)
                ->with('info', 'You already have an active claim in progress for this item.');
        }

        // 4. Fetch claimant's own lost item reports that match or can be linked
        $myLostItems = $user->lostItems()
            ->whereIn('status', [LostItemStatus::PendingReview, LostItemStatus::Approved])
            ->latest()
            ->get();

        return view('claims.create', compact('foundItem', 'myLostItems'));
    }

    /**
     * Store newly submitted claim with proof description and optional private evidence.
     */
    public function store(
        StoreClaimRequest $request,
        SupabaseStorageService $storage,
        NotificationService $notifications
    ): RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.claims.index')
                ->with('error', 'Administrators cannot file claim requests.');
        }

        $evidencePaths = [];

        if ($request->hasFile('evidence')) {
            $bucket = config('supabase.buckets.evidence', 'claim-evidence');
            foreach ($request->file('evidence') as $file) {
                $path = $storage->upload($file, $bucket, 'claims/' . $request->user()->id);
                $evidencePaths[] = $path;
            }
        }

        $claim = ClaimRequest::create([
            'found_item_id' => $request->input('found_item_id'),
            'claimant_id' => $request->user()->id,
            'lost_item_id' => $request->input('lost_item_id'),
            'proof_description' => $request->input('proof_description'),
            'evidence_paths' => $evidencePaths,
            'status' => ClaimRequestStatus::Pending,
        ]);

        // Load foundItem relationship
        $claim->load('foundItem');

        // Notify admins and/or finder of new claim
        $notifications->claimReceived($claim);

        $successMsg = $claim->foundItem->isKeptByFinder()
            ? 'Your ownership claim has been submitted directly to the finder. The finder will review your proof and contact you.'
            : 'Your ownership claim has been submitted. Campus administrators will verify your proof.';

        return redirect()->route('claims.show', $claim)->with('success', $successMsg);
    }

    /**
     * Display a single claim request and its verification status.
     */
    public function show(Request $request, ClaimRequest $claim): View
    {
        $user = $request->user();

        // Claimant, Finder, or Admin
        $isClaimant = $claim->claimant_id === $user->id;
        $isFinder = $claim->foundItem->user_id === $user->id;
        $isAdmin = $user->isAdmin();

        if (! $isClaimant && ! $isFinder && ! $isAdmin) {
            abort(403, 'Unauthorized access to claim details.');
        }

        $claim->load(['foundItem.category', 'foundItem.user', 'lostItem', 'reviewer', 'approver', 'claimant', 'itemReturn.recordedBy']);

        return view('claims.show', compact('claim', 'isClaimant', 'isFinder', 'isAdmin'));
    }

    /**
     * Claimant response to an information request.
     */
    public function respond(
        Request $request,
        ClaimRequest $claim,
        NotificationService $notifications
    ): RedirectResponse {
        $user = $request->user();

        if ($claim->claimant_id !== $user->id) {
            abort(403);
        }

        if ($claim->status !== ClaimRequestStatus::AdditionalInformationRequired) {
            return redirect()->route('claims.show', $claim)
                ->with('error', 'Additional information is not currently requested for this claim.');
        }

        $validated = $request->validate([
            'claimant_response' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'claimant_response.required' => 'Please provide the additional information requested.',
            'claimant_response.min' => 'Your response must be at least 10 characters long.',
        ]);

        $claim->update([
            'claimant_response' => $validated['claimant_response'],
            'status' => ClaimRequestStatus::UnderReview,
        ]);

        // Notify finder if item is kept by finder
        if ($claim->foundItem->isKeptByFinder()) {
            $notifications->send(
                $claim->foundItem->user_id,
                'claim_response',
                'Claimant Responded · ' . $claim->foundItem->item_name,
                "{$user->name} submitted the requested details for their claim on '{$claim->foundItem->item_name}'.",
                'claim_request',
                $claim->id
            );
        }

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Your response has been submitted and your claim is now under review.');
    }

    /**
     * Finder approves claim and provides meetup / retrieval instructions.
     */
    public function finderApprove(
        Request $request,
        ClaimRequest $claim,
        NotificationService $notifications
    ): RedirectResponse {
        $user = $request->user();

        if ($claim->foundItem->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Only the finder or an administrator can approve this claim.');
        }

        if (in_array($claim->status, [ClaimRequestStatus::Approved, ClaimRequestStatus::Completed, ClaimRequestStatus::Rejected])) {
            return back()->with('error', 'This claim has already been approved or concluded.');
        }

        $alreadyApproved = ClaimRequest::where('found_item_id', $claim->found_item_id)
            ->whereIn('status', [ClaimRequestStatus::Approved, ClaimRequestStatus::Completed])
            ->where('id', '!=', $claim->id)
            ->exists();

        if ($alreadyApproved) {
            return back()->with('error', 'Another claim for this item has already been approved.');
        }

        $validated = $request->validate([
            'pickup_instructions' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($claim, $user, $validated, $notifications) {
            $claim->update([
                'status' => ClaimRequestStatus::Approved,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'pickup_instructions' => $validated['pickup_instructions'],
            ]);

            $claim->foundItem->update([
                'status' => FoundItemStatus::Claimed,
            ]);

            $notifications->claimApproved($claim);
        });

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Claim approved! The claimant has been notified with your meetup instructions.');
    }

    /**
     * Finder requests additional details from claimant.
     */
    public function finderRequestInfo(
        Request $request,
        ClaimRequest $claim,
        NotificationService $notifications
    ): RedirectResponse {
        $user = $request->user();

        if ($claim->foundItem->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $claim->update([
            'status' => ClaimRequestStatus::AdditionalInformationRequired,
            'admin_message' => $validated['message'],
        ]);

        $notifications->claimInfoRequired($claim, $validated['message']);

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Information request sent to the claimant.');
    }

    /**
     * Finder rejects claim.
     */
    public function finderReject(
        Request $request,
        ClaimRequest $claim,
        NotificationService $notifications
    ): RedirectResponse {
        $user = $request->user();

        if ($claim->foundItem->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $claim->update([
            'status' => ClaimRequestStatus::Rejected,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $notifications->claimRejected($claim, $validated['rejection_reason']);

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Claim has been rejected.');
    }

    /**
     * Finder records physical return / handover to owner.
     */
    public function finderRecordReturn(
        Request $request,
        ClaimRequest $claim,
        NotificationService $notifications
    ): RedirectResponse {
        $user = $request->user();

        if ($claim->foundItem->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        if ($claim->status !== ClaimRequestStatus::Approved) {
            return back()->with('error', 'Only an approved claim can be handed over.');
        }

        $validated = $request->validate([
            'returned_to_name' => ['required', 'string', 'max:150'],
            'lrn' => ['required', 'string', 'max:100'],
            'course_year' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $claim, $user, $notifications) {
            $itemReturn = ItemReturn::create([
                'claim_request_id' => $claim->id,
                'found_item_id' => $claim->found_item_id,
                'recorded_by' => $user->id,
                'returned_to_name' => $validated['returned_to_name'],
                'lrn' => $validated['lrn'],
                'course_year' => $validated['course_year'],
                'id_presented' => 'Student ID',
                'notes' => $validated['notes'] ?? 'Handed over directly by finder',
                'returned_at' => now(),
            ]);

            $claim->update([
                'status' => ClaimRequestStatus::Completed,
                'completed_at' => now(),
            ]);

            $claim->foundItem->update([
                'status' => FoundItemStatus::Returned,
            ]);

            $notifications->itemReturned($itemReturn);
        });

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Item handover successfully recorded! This item is now marked as returned to its owner.');
    }
}
