<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Models\ItemCategory;
use App\Models\ItemReturn;
use App\Models\LostItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    /**
     * Analytics and recovery intelligence dashboard.
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $lostQuery = LostItem::query();
        $foundQuery = FoundItem::query();
        $returnsQuery = ItemReturn::query();

        if ($dateFrom) {
            $lostQuery->whereDate('date_lost', '>=', $dateFrom);
            $foundQuery->whereDate('date_found', '>=', $dateFrom);
            $returnsQuery->whereDate('returned_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $lostQuery->whereDate('date_lost', '<=', $dateTo);
            $foundQuery->whereDate('date_found', '<=', $dateTo);
            $returnsQuery->whereDate('returned_at', '<=', $dateTo);
        }

        $totalLost = $lostQuery->count();
        $totalFound = $foundQuery->count();
        $totalReturned = $returnsQuery->count();

        // Recovery Rate: (Reunited Items / Found Items) * 100%
        $recoveryRate = $totalFound > 0 ? round(($totalReturned / $totalFound) * 100, 1) : 0;

        // Category breakdown
        $categories = ItemCategory::withCount([
            'foundItems' => fn ($q) => $dateFrom ? $q->whereDate('date_found', '>=', $dateFrom) : $q,
            'lostItems' => fn ($q) => $dateFrom ? $q->whereDate('date_lost', '>=', $dateFrom) : $q,
        ])->get();

        // Status distributions
        $foundStatusDistribution = FoundItem::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $lostStatusDistribution = LostItem::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.analytics.index', compact(
            'totalLost',
            'totalFound',
            'totalReturned',
            'recoveryRate',
            'categories',
            'foundStatusDistribution',
            'lostStatusDistribution',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export lost and found reports as a clean CSV stream.
     */
    public function exportReportsCsv(Request $request): StreamedResponse
    {
        $filename = 'campus-reports-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel support
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'Report ID',
                'Report Type',
                'Item Name',
                'Category',
                'Incident Date',
                'Location',
                'Status',
                'Reporter Name',
                'Reporter Email',
                'Created At',
            ]);

            // Stream Found Items
            FoundItem::with(['user', 'category'])->chunk(100, function ($items) use ($handle) {
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->id,
                        'Found Item',
                        $this->sanitizeCsv($item->item_name),
                        $this->sanitizeCsv($item->category?->name ?? 'Uncategorized'),
                        $item->date_found->format('Y-m-d'),
                        $this->sanitizeCsv($item->location_found),
                        $item->status->value ?? $item->status,
                        $this->sanitizeCsv($item->user->name),
                        $this->sanitizeCsv($item->user->email),
                        $item->created_at->toIso8601String(),
                    ]);
                }
            });

            // Stream Lost Items
            LostItem::with(['user', 'category'])->chunk(100, function ($items) use ($handle) {
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->id,
                        'Lost Item',
                        $this->sanitizeCsv($item->item_name),
                        $this->sanitizeCsv($item->category?->name ?? 'Uncategorized'),
                        $item->date_lost->format('Y-m-d'),
                        $this->sanitizeCsv($item->location_last_seen),
                        $item->status->value ?? $item->status,
                        $this->sanitizeCsv($item->user->name),
                        $this->sanitizeCsv($item->user->email),
                        $item->created_at->toIso8601String(),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export claim requests and verification history as CSV stream.
     */
    public function exportClaimsCsv(Request $request): StreamedResponse
    {
        $filename = 'campus-claims-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 Excel support
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Claim ID',
                'Found Item Name',
                'Claimant Name',
                'Claimant Email',
                'Status',
                'Date Submitted',
                'Approved At',
                'Completed At',
            ]);

            ClaimRequest::with(['claimant', 'foundItem'])->chunk(100, function ($claims) use ($handle) {
                foreach ($claims as $claim) {
                    fputcsv($handle, [
                        $claim->id,
                        $this->sanitizeCsv($claim->foundItem?->item_name ?? 'Unknown Item'),
                        $this->sanitizeCsv($claim->claimant?->name ?? 'Unknown Claimant'),
                        $this->sanitizeCsv($claim->claimant?->email ?? ''),
                        $claim->status->value ?? $claim->status,
                        $claim->created_at->toIso8601String(),
                        $claim->approved_at ? $claim->approved_at->toIso8601String() : 'N/A',
                        $claim->completed_at ? $claim->completed_at->toIso8601String() : 'N/A',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Sanitize string against CSV Formula Injection (=, +, -, @).
     */
    protected function sanitizeCsv(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Escape dangerous initial characters that could trigger spreadsheet formula execution
        if (in_array($value[0], ['=', '+', '-', '@', "\t", "\r"])) {
            return "'" . $value;
        }

        return $value;
    }
}
