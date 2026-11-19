<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\LostItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemDiscoveryController extends Controller
{
    /**
     * Display a paginated directory of approved campus lost and found items
     * with real-time search, category filtering, date ranges, and status selectors.
     */
    public function index(Request $request): View
    {
        // 1. Sanitize & extract filters
        $type = in_array($request->query('type'), ['found', 'lost', 'all']) ? $request->query('type') : 'all';
        $search = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $location = trim((string) $request->query('location', ''));
        $sort = $request->query('sort') === 'oldest' ? 'oldest' : 'latest';

        // Validate date formats
        if ($dateFrom && ! $this->isValidDate($dateFrom)) {
            $dateFrom = null;
        }
        if ($dateTo && ! $this->isValidDate($dateTo)) {
            $dateTo = null;
        }

        // 2. Fetch active categories for dropdown & quick relation lookup
        $categories = ItemCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        $categoriesById = $categories->keyBy('id');

        // Verify selected category exists
        if ($categoryId && ! $categoriesById->has($categoryId)) {
            $categoryId = null;
        }

        // 3. Prepare queries for each item type
        // Found Items query (public approved, claimed, returned)
        $foundQuery = DB::table('found_items')
            ->leftJoin('users', 'found_items.user_id', '=', 'users.id')
            ->select([
                'found_items.id',
                DB::raw("'found' as item_type"),
                'found_items.item_name',
                'found_items.public_description as description',
                'found_items.date_found as item_date',
                'found_items.location_found as location',
                'found_items.photo_path',
                'found_items.status',
                'found_items.category_id',
                'found_items.user_id',
                'found_items.created_at',
                'found_items.contact_info',
                'users.name as poster_name',
                'users.email as poster_email',
            ]);

        // Lost Items query (public approved, resolved)
        $lostQuery = DB::table('lost_items')
            ->leftJoin('users', 'lost_items.user_id', '=', 'users.id')
            ->select([
                'lost_items.id',
                DB::raw("'lost' as item_type"),
                'lost_items.item_name',
                'lost_items.description as description',
                'lost_items.date_lost as item_date',
                'lost_items.location_last_seen as location',
                'lost_items.photo_path',
                'lost_items.status',
                'lost_items.category_id',
                'lost_items.user_id',
                'lost_items.created_at',
                'lost_items.contact_info',
                'users.name as poster_name',
                'users.email as poster_email',
            ]);

        // 4. Apply status filters (Simplified: All, Unresolved, Resolved)
        if ($status === 'unresolved' || $status === 'approved') {
            $foundQuery->whereIn('found_items.status', ['approved', 'claimed']);
            $lostQuery->where('lost_items.status', 'approved');
        } elseif ($status === 'resolved') {
            $foundQuery->where('found_items.status', 'returned');
            $lostQuery->where('lost_items.status', 'resolved');
        } elseif (in_array($status, ['claimed', 'returned'])) {
            $foundQuery->where('found_items.status', $status);
            $lostQuery->whereRaw('1 = 0');
        } else {
            // Default public visibility (show both unresolved and resolved items)
            $foundQuery->whereIn('found_items.status', ['approved', 'claimed', 'returned']);
            $lostQuery->whereIn('lost_items.status', ['approved', 'resolved']);
        }

        // 5. Apply category filter
        if ($categoryId) {
            $foundQuery->where('found_items.category_id', $categoryId);
            $lostQuery->where('lost_items.category_id', $categoryId);
        }

        // 6. Apply date filters
        if ($dateFrom) {
            $foundQuery->whereDate('found_items.date_found', '>=', $dateFrom);
            $lostQuery->whereDate('lost_items.date_lost', '>=', $dateFrom);
        }
        if ($dateTo) {
            $foundQuery->whereDate('found_items.date_found', '<=', $dateTo);
            $lostQuery->whereDate('lost_items.date_lost', '<=', $dateTo);
        }

        // 7. Apply location filter
        if ($location !== '') {
            $locPattern = '%' . $location . '%';
            $foundQuery->where('found_items.location_found', 'ILIKE', $locPattern);
            $lostQuery->where('lost_items.location_last_seen', 'ILIKE', $locPattern);
        }

        // 8. Apply search keyword (Full text vector + ILIKE trigram fallback)
        if ($search !== '') {
            $searchPattern = '%' . $search . '%';

            $foundQuery->where(function ($query) use ($search, $searchPattern) {
                $query->whereRaw("found_items.search_vector @@ plainto_tsquery('english', ?)", [$search])
                    ->orWhere('found_items.item_name', 'ILIKE', $searchPattern)
                    ->orWhere('found_items.location_found', 'ILIKE', $searchPattern);
            });

            $lostQuery->where(function ($query) use ($search, $searchPattern) {
                $query->whereRaw("lost_items.search_vector @@ plainto_tsquery('english', ?)", [$search])
                    ->orWhere('lost_items.item_name', 'ILIKE', $searchPattern)
                    ->orWhere('lost_items.location_last_seen', 'ILIKE', $searchPattern);
            });
        }

        // 9. Select target query based on Type
        if ($type === 'found') {
            $mainQuery = $foundQuery;
        } elseif ($type === 'lost') {
            $mainQuery = $lostQuery;
        } else {
            $mainQuery = DB::query()->fromSub($foundQuery->unionAll($lostQuery), 'items');
        }

        // 10. Sort order
        $sortDir = ($sort === 'oldest') ? 'asc' : 'desc';
        if ($type === 'found') {
            $mainQuery->orderBy('found_items.date_found', $sortDir)
                ->orderBy('found_items.created_at', $sortDir);
        } elseif ($type === 'lost') {
            $mainQuery->orderBy('lost_items.date_lost', $sortDir)
                ->orderBy('lost_items.created_at', $sortDir);
        } else {
            $mainQuery->orderBy('items.item_date', $sortDir)
                ->orderBy('items.created_at', $sortDir);
        }

        // 11. Paginate (12 per page)
        /** @var LengthAwarePaginator $items */
        $items = $mainQuery->paginate(12)->withQueryString();

        // 12. Hydrate DTO properties for view presentation (Zero N+1)
        $supabaseUrl = config('supabase.url');
        $photosBucket = config('supabase.buckets.photos', 'item-photos');

        $items->getCollection()->transform(function ($item) use ($categoriesById, $supabaseUrl, $photosBucket) {
            // Attach category
            $item->category = $categoriesById->get($item->category_id);

            // Generate photo URL
            if (! empty($item->photo_path) && $item->photo_path !== 'placeholder.jpg') {
                if (filter_var($item->photo_path, FILTER_VALIDATE_URL)) {
                    $item->photo_url = $item->photo_path;
                } else {
                    $item->photo_url = "{$supabaseUrl}/storage/v1/object/public/{$photosBucket}/" . ltrim($item->photo_path, '/');
                }
            } else {
                $item->photo_url = null;
            }

            // Formatted date (October 13, 2026 style matching reference)
            $item->formatted_date = Carbon::parse($item->item_date)->format('F d, Y');
            $item->posted_date = Carbon::parse($item->created_at)->format('F j, Y');

            // Poster name & contact info
            $item->poster_name = $item->poster_name ?? 'Campus Member';
            $item->contact_info = $item->contact_info ?: ($item->poster_email ?? 'campus@school.edu');

            // Simple status resolution (Unresolved vs Resolved)
            $isResolved = ($item->item_type === 'found' && $item->status === 'returned')
                || ($item->item_type === 'lost' && $item->status === 'resolved');
            $item->is_resolved = $isResolved;
            $item->status_label = $isResolved ? 'Resolved' : 'Unresolved';

            // Authorization & resolve toggle
            $item->can_resolve = auth()->check() && (auth()->user()->isAdmin() || auth()->id() === $item->user_id);
            $item->toggle_resolve_route = $item->item_type === 'found'
                ? route('found-items.toggle-resolve', $item->id)
                : route('lost-items.toggle-resolve', $item->id);

            return $item;
        });

        // 13. System counters for discovery header
        $counts = [
            'total_found' => FoundItem::where('status', 'approved')->count(),
            'total_lost' => LostItem::where('status', 'approved')->count(),
            'total_resolved' => FoundItem::where('status', 'returned')->count() + LostItem::where('status', 'resolved')->count(),
        ];

        return view('items.index', compact('items', 'categories', 'counts', 'type', 'search', 'categoryId', 'status', 'dateFrom', 'dateTo', 'location', 'sort'));
    }

    /**
     * Validate date string in Y-m-d format.
     */
    protected function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
