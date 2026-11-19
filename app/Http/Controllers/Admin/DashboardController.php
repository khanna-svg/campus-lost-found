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
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display simplified administrative dashboard with 4 core metrics
     * and immediate claim request verification & approval workflow.
     */
    public function __invoke(Request $request): View
    {
        // 4 Core Metrics requested by the user
        $stats = [
            'total_lost' => LostItem::count(),
            'total_found' => FoundItem::count(),
            'pending_claims' => ClaimRequest::whereIn('status', [
                ClaimRequestStatus::Pending,
                ClaimRequestStatus::UnderReview,
                ClaimRequestStatus::AdditionalInformationRequired,
            ])->count(),
            'returned_items' => FoundItem::where('status', FoundItemStatus::Returned)->count()
                + LostItem::where('status', LostItemStatus::Resolved)->count(),
        ];

        // 1. Pending Claims awaiting admin approval
        $pendingClaims = ClaimRequest::with(['claimant', 'foundItem.category'])
            ->whereIn('status', [
                ClaimRequestStatus::Pending,
                ClaimRequestStatus::UnderReview,
                ClaimRequestStatus::AdditionalInformationRequired,
            ])
            ->latest()
            ->get();

        // 2. Approved Claims ready for office pickup (Administration Hall, Room 102)
        $approvedClaims = ClaimRequest::with(['claimant', 'foundItem.category'])
            ->where('status', ClaimRequestStatus::Approved)
            ->latest('approved_at')
            ->take(10)
            ->get();

        // 3. Successfully completed returns
        $recentReturns = ItemReturn::with(['foundItem', 'claimRequest.claimant', 'recordedBy'])
            ->latest('returned_at')
            ->take(10)
            ->get();

        // 4. Recent logged items
        $recentLost = LostItem::with(['user', 'category'])->latest()->take(5)->get();
        $recentFound = FoundItem::with(['user', 'category'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'pendingClaims',
            'approvedClaims',
            'recentReturns',
            'recentLost',
            'recentFound'
        ));
    }
}
