<?php

namespace App\Http\Controllers;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Http\Requests\StoreClaimRequest;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Services\SupabaseStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClaimRequestController extends Controller
{
    /**
     * List all claim requests submitted by the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $claims = $user->claimRequests()
            ->with(['foundItem.category'])
            ->latest()
            ->paginate(10);

        return view('claims.index', compact('claims'));
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
        \App\Services\NotificationService $notifications
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

        // Notify admins of new claim
        $notifications->claimReceived($claim);

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Your ownership claim has been submitted. Campus administrators will verify your proof.');
    }

    /**
     * Display a single claim request and its verification status.
     */
    public function show(Request $request, ClaimRequest $claim): View
    {
        $user = $request->user();

        // Claimant or Admin only
        if ($claim->claimant_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized access to claim details.');
        }

        $claim->load(['foundItem.category', 'lostItem', 'reviewer', 'approver', 'itemReturn.recordedBy']);

        return view('claims.show', compact('claim'));
    }

    /**
     * Claimant response to an administrator's request for additional information.
     */
    public function respond(Request $request, ClaimRequest $claim): RedirectResponse
    {
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
            'claimant_response.required' => 'Please provide the additional information requested by the administrator.',
            'claimant_response.min' => 'Your response must be at least 10 characters long.',
        ]);

        $claim->update([
            'claimant_response' => $validated['claimant_response'],
            'status' => ClaimRequestStatus::UnderReview,
        ]);

        return redirect()->route('claims.show', $claim)
            ->with('success', 'Your response has been submitted and your claim is now under review.');
    }
}
