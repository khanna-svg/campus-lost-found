<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display the append-only administrative audit log.
     */
    public function index(Request $request): View
    {
        $action = $request->query('action', 'all');

        $query = ActivityLog::with('actor')->latest('created_at');

        if ($action !== 'all') {
            $query->where('action', 'ILIKE', '%' . $action . '%');
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.logs.index', compact('logs', 'action'));
    }
}
