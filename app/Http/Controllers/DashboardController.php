<?php

namespace App\Http\Controllers;

use App\Enums\ClaimRequestStatus;
use App\Enums\FoundItemStatus;
use App\Enums\LostItemStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Entry point for root URL '/'.
     * Redirects to home if authenticated, or login if guest.
     */
    public function welcome(): RedirectResponse
    {
        return Auth::check() ? redirect()->route('home') : redirect()->route('login');
    }

    /**
     * Role-based dispatch for '/home'.
     */
    public function home(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Regular user (Student / Faculty) dashboard.
     * Computes real-time overview stats and fetches recent activity.
     */
    public function index(Request $request): View|RedirectResponse
    {
        /** @var User $user */
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
                ->count() + $user->foundItems()
                ->where('status', FoundItemStatus::Returned)
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

    /**
     * Temporary placeholder handler for items browse.
     */
    public function placeholderItems(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('info', 'Search and item discovery is being enabled in Phase 7.');
    }

    /**
     * Temporary placeholder handler for report lost.
     */
    public function placeholderReportLost(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('info', 'Lost item reporting form is being enabled in Phase 6.');
    }

    /**
     * Temporary placeholder handler for report found.
     */
    public function placeholderReportFound(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('info', 'Found item reporting form is being enabled in Phase 6.');
    }

    /**
     * Temporary placeholder handler for claims.
     */
    public function placeholderClaims(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('info', 'Claim tracking is being enabled in Phase 8.');
    }
}
