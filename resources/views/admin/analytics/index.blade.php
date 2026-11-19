<x-layouts.admin title="Analytics & Reports">
    <div class="space-y-8">
        {{-- Header & CSV Export Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Analytics & Resolution Reports</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Comprehensive metrics on property loss, discovery velocity, and campus reunion rates.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export-csv') }}" class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Export Reports CSV
                </a>
                <a href="{{ route('admin.claims.export-csv') }}" class="rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Export Claims CSV
                </a>
            </div>
        </div>

        {{-- Date Filter Bar --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
                <div>
                    <label for="date_from" class="block font-semibold text-slate-700 mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="rounded-xl border-0 p-2 ring-1 ring-inset ring-slate-300">
                </div>
                <div>
                    <label for="date_to" class="block font-semibold text-slate-700 mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="rounded-xl border-0 p-2 ring-1 ring-inset ring-slate-300">
                </div>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white">
                    Filter Date Range
                </button>
                @if ($dateFrom || $dateTo)
                    <a href="{{ route('admin.analytics.index') }}" class="py-2 text-slate-500 hover:text-slate-800 underline">
                        Clear dates
                    </a>
                @endif
            </form>
        </div>

        {{-- 4 Big Metric Cards --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Total Found Items</span>
                <span class="text-3xl font-extrabold text-slate-900 block mt-2">{{ number_format($totalFound) }}</span>
                <span class="text-xs text-slate-500 block mt-1">Discovered on campus</span>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Total Lost Reports</span>
                <span class="text-3xl font-extrabold text-slate-900 block mt-2">{{ number_format($totalLost) }}</span>
                <span class="text-xs text-slate-500 block mt-1">Reported by students/faculty</span>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Reunited with Owners</span>
                <span class="text-3xl font-extrabold text-emerald-600 block mt-2">{{ number_format($totalReturned) }}</span>
                <span class="text-xs text-slate-500 block mt-1">Physical hand-overs recorded</span>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-brand-900 to-slate-900 text-white p-6 shadow-sm">
                <span class="text-xs uppercase font-bold tracking-wider text-brand-300 block">Overall Recovery Rate</span>
                <span class="text-3xl font-extrabold block mt-2">{{ $recoveryRate }}%</span>
                <span class="text-xs text-brand-200 block mt-1">Reunited vs Total Found</span>
            </div>
        </div>

        {{-- Category Breakdown Grid --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Item Category Distribution</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="pb-2">Category</th>
                            <th class="pb-2">Found Items</th>
                            <th class="pb-2">Lost Reports</th>
                            <th class="pb-2">Total Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($categories as $cat)
                            <tr>
                                <td class="py-2.5 font-bold text-slate-900">{{ $cat->name }}</td>
                                <td class="py-2.5 text-slate-600">{{ $cat->found_items_count }}</td>
                                <td class="py-2.5 text-slate-600">{{ $cat->lost_items_count }}</td>
                                <td class="py-2.5 font-bold text-brand-700">{{ $cat->found_items_count + $cat->lost_items_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
