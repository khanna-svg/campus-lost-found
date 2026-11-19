<x-layouts.admin title="Executive Console">
    <div class="space-y-8">
        {{-- Header & Quick Action Banner --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Administrative Dashboard</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Real-time oversight of reports, claim verifications, and property hand-overs.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-500 transition">
                    Review Pending Reports
                </a>
                <a href="{{ route('admin.claims.index') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                    Verify Claims
                </a>
            </div>
        </div>

        {{-- 6 Live Metric Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            <x-stat-card title="Pending Found" :value="$stats['pending_found']" description="Review" color="emerald" />
            <x-stat-card title="Pending Lost" :value="$stats['pending_lost']" description="Review" color="amber" />
            <x-stat-card title="Open Claims" :value="$stats['open_claims']" description="Verify" color="brand" />
            <x-stat-card title="Ready Pickup" :value="$stats['approved_claims']" description="Approved" color="indigo" />
            <x-stat-card title="Reunited" :value="$stats['reunited_items']" description="Returned" color="emerald" />
            <x-stat-card title="Total Users" :value="$stats['total_users']" description="Accounts" color="brand" />
        </div>

        {{-- Two-Column Review Feeds --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Pending Found Reports --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <h2 class="text-sm font-bold text-slate-900">Found Reports Awaiting Review</h2>
                    </div>
                    <a href="{{ route('admin.reports.index', ['type' => 'found']) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">
                        View All ({{ $stats['pending_found'] }}) →
                    </a>
                </div>

                @if ($pendingFound->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach ($pendingFound as $found)
                            <div class="flex items-center justify-between py-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if ($found->photo_url)
                                        <img src="{{ $found->photo_url }}" alt="" class="h-10 w-10 rounded-lg object-cover shrink-0">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-slate-100 shrink-0 flex items-center justify-center text-slate-400">?</div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-slate-900 truncate">{{ $found->item_name }}</h3>
                                        <p class="text-[11px] text-slate-500 truncate">
                                            Found by {{ $found->user->name }} · {{ $found->location_found }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.reports.show-found', $found) }}" class="rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100 transition shrink-0 ml-2">
                                    Review
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-6 text-center text-xs text-slate-400">No found items pending review.</p>
                @endif
            </div>

            {{-- Pending Lost Reports --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                        <h2 class="text-sm font-bold text-slate-900">Lost Reports Awaiting Review</h2>
                    </div>
                    <a href="{{ route('admin.reports.index', ['type' => 'lost']) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">
                        View All ({{ $stats['pending_lost'] }}) →
                    </a>
                </div>

                @if ($pendingLost->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach ($pendingLost as $lost)
                            <div class="flex items-center justify-between py-3">
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-slate-900 truncate">{{ $lost->item_name }}</h3>
                                    <p class="text-[11px] text-slate-500 truncate">
                                        Reported by {{ $lost->user->name }} · Last seen {{ $lost->location_last_seen }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.reports.show-lost', $lost) }}" class="rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100 transition shrink-0 ml-2">
                                    Review
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-6 text-center text-xs text-slate-400">No lost items pending review.</p>
                @endif
            </div>
        </div>

        {{-- Pending Claims Queue --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900">Incoming Ownership Claims Awaiting Verification</h2>
                <a href="{{ route('admin.claims.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">
                    Claims Desk ({{ $stats['open_claims'] }}) →
                </a>
            </div>

            @if ($pendingClaims->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="pb-2">Claimed Item</th>
                                <th class="pb-2">Claimant</th>
                                <th class="pb-2">Submitted</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($pendingClaims as $claim)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-900">{{ $claim->foundItem->item_name }}</td>
                                    <td class="py-3 text-slate-600">{{ $claim->claimant->name }}</td>
                                    <td class="py-3 text-slate-500">{{ $claim->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="py-3"><x-status-badge :status="$claim->status" /></td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('admin.claims.show', $claim) }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                                            Verify Proof
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="py-6 text-center text-xs text-slate-400">No claims currently in verification queue.</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
