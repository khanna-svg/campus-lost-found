<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Administrator dashboard (placeholder; the real dashboard is built in Phase 10). */
    public function __invoke(Request $request): View
    {
        return view('admin.dashboard', ['user' => $request->user()]);
    }
}
