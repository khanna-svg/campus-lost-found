<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Http\Controllers\Controller;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Models\ItemReturn;
use App\Models\LostItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display live executive overview of campus lost and found operations.
     */
    public function __invoke(Request $request): View
    {
        $stats = [
            'pending_lost' => LostItem::where('status', LostItemStatus::PendingReview)->count(),
            'pending_found' => FoundItem::where('status', FoundItemStatus::PendingReview)->count(),
            'open_claims' => ClaimRequest::whereIn('status', [
                ClaimRequestStatus::Pending,
                ClaimRequestStatus::UnderReview,
                ClaimRequestStatus::AdditionalInformationRequired,
            ])->count(),
            'approved_claims' => ClaimRequest::where('status', ClaimRequestStatus::Approved)->count(),
            'reunited_items' => ItemReturn::count(),
            'total_users' => User::count(),
        ];

        // Recent items awaiting review
        $pendingLost = LostItem::with(['user', 'category'])
            ->where('status', LostItemStatus::PendingReview)
            ->latest()
            ->take(5)
            ->get();

        $pendingFound = FoundItem::with(['user', 'category'])
            ->where('status', FoundItemStatus::PendingReview)
            ->latest()
            ->take(5)
            ->get();

        // Recent incoming claims
        $pendingClaims = ClaimRequest::with(['claimant', 'foundItem.category'])
            ->whereIn('status', [ClaimRequestStatus::Pending, ClaimRequestStatus::UnderReview])
            ->latest()
            ->take(5)
            ->get();

        // Recent completed returns
        $recentReturns = ItemReturn::with(['foundItem', 'claimRequest.claimant', 'recordedBy'])
            ->latest('returned_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingLost', 'pendingFound', 'pendingClaims', 'recentReturns'));
    }
}
