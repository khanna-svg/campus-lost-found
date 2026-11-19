<?php

namespace App\Http\Controllers;

use App\Enums\ClaimRequestStatus;
use App\Enums\LostItemStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Regular user (Student / Faculty) dashboard.
     * Computes real-time overview stats and fetches recent activity.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Stats calculation
        $stats = [
            'active_lost' => $user->lostItems()
                ->whereIn('status', [LostItemStatus::PendingReview, LostItemStatus::Approved])
                ->count(),
            'found_reported' => $user->foundItems()
                ->count(),
            'pending_claims' => $user->claimRequests()
                ->whereIn('status', [
                    ClaimRequestStatus::Pending,
                    ClaimRequestStatus::UnderReview,
                    ClaimRequestStatus::AdditionalInformationRequired,
                    ClaimRequestStatus::Approved,
                ])
                ->count(),
            'resolved' => $user->lostItems()
                ->where('status', LostItemStatus::Resolved)
                ->count() + $user->claimRequests()
                ->where('status', ClaimRequestStatus::Completed)
                ->count(),
        ];

        // Recent items
        $recentLost = $user->lostItems()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $recentFound = $user->foundItems()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $recentClaims = $user->claimRequests()
            ->with(['foundItem.category'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'stats', 'recentLost', 'recentFound', 'recentClaims'));
    }
}
