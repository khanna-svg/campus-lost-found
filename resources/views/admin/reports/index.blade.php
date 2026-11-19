<x-layouts.admin title="Report Review Desk">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Report Review Desk</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Verify and approve community reports before they are published to the campus directory.
                </p>
            </div>

            {{-- Type Switcher Pills --}}
            <div class="inline-flex rounded-xl bg-slate-100 p-1 ring-1 ring-slate-200">
                <a href="{{ route('admin.reports.index', ['type' => 'found', 'status' => $status]) }}"
                   @class([
                       'px-4 py-1.5 rounded-lg text-xs font-semibold transition',
                       'bg-white text-slate-900 shadow-sm' => $type === 'found',
                       'text-slate-600 hover:text-slate-900' => $type !== 'found',
                   ])>
                    Found Items ({{ $pendingCounts['found'] }})
                </a>
                <a href="{{ route('admin.reports.index', ['type' => 'lost', 'status' => $status]) }}"
                   @class([
                       'px-4 py-1.5 rounded-lg text-xs font-semibold transition',
                       'bg-white text-slate-900 shadow-sm' => $type === 'lost',
                       'text-slate-600 hover:text-slate-900' => $type !== 'lost',
                   ])>
                    Lost Reports ({{ $pendingCounts['lost'] }})
                </a>
            </div>
        </div>

        {{-- Status Filter Badges --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 text-xs">
            <span class="text-slate-400 font-medium">Status Filter:</span>
            @foreach (['pending_review' => 'Pending Review', 'approved' => 'Approved', 'all' => 'All Statuses'] as $k => $label)
                <a href="{{ route('admin.reports.index', ['type' => $type, 'status' => $k]) }}"
                   @class([
                       'px-3 py-1 rounded-lg font-semibold transition',
                       'bg-slate-900 text-white' => $status === $k,
                       'bg-slate-100 text-slate-700 hover:bg-slate-200' => $status !== $k,
                   ])>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Reports Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            @if ($reports->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Item</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4">Reporter</th>
                                <th class="py-3 px-4">Date & Location</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($reports as $report)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ $report->item_name }}
                                        @if ($report->needs_correction)
                                            <span class="ml-1 inline-block rounded bg-amber-100 px-1.5 py-0.2 text-[9px] font-bold text-amber-800">
                                                Correction Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">{{ $report->category->name }}</td>
                                    <td class="py-3 px-4 text-slate-600">
                                        <span class="block font-medium text-slate-900">{{ $report->user->name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $report->user->email }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">
                                        <span class="block">{{ ($type === 'found' ? $report->date_found : $report->date_lost)->format('M d, Y') }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $type === 'found' ? $report->location_found : $report->location_last_seen }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$report->status" />
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if ($type === 'found')
                                            <a href="{{ route('admin.reports.show-found', $report) }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                                                Review
                                            </a>
                                        @else
                                            <a href="{{ route('admin.reports.show-lost', $report) }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                                                Review
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $reports->links() }}
                </div>
            @else
                <p class="py-12 text-center text-xs text-slate-400">No reports found under this filter.</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
