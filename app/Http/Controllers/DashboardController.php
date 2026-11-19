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
     * Displays simple, clean campus landing page.
     */
    public function welcome(): View
    {
        return view('welcome');
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

        return redirect()->route('items.index');
    }

    /**
     * Regular user dashboard - redirected to Browse Item Directory.
     */
    public function index(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('items.index');
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
