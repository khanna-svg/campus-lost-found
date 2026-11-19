<x-layouts.admin title="Claims Verification Desk">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Claims Verification Desk</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Verify claimant evidence against physical items and record official hand-overs.
                </p>
            </div>
        </div>

        {{-- Status Filter Badges --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 text-xs">
            <span class="text-slate-400 font-medium">Status:</span>
            @foreach ([
                'pending' => 'Pending Queue (' . $counts['pending'] . ')',
                'under_review' => 'In Review (' . $counts['under_review'] . ')',
                'additional_information_required' => 'Info Requested (' . $counts['info_required'] . ')',
                'approved' => 'Approved / Ready (' . $counts['approved'] . ')',
                'completed' => 'Returned (' . $counts['completed'] . ')',
                'all' => 'All Claims',
            ] as $k => $label)
                <a href="{{ route('admin.claims.index', ['status' => $k]) }}"
                   @class([
                       'px-3 py-1 rounded-lg font-semibold transition',
                       'bg-slate-900 text-white' => $status === $k,
                       'bg-slate-100 text-slate-700 hover:bg-slate-200' => $status !== $k,
                   ])>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Claims Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            @if ($claims->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Claimed Item</th>
                                <th class="py-3 px-4">Claimant</th>
                                <th class="py-3 px-4">Date Submitted</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($claims as $claim)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <div class="flex items-center gap-3">
                                            @if ($claim->foundItem->photo_url)
                                                <img src="{{ $claim->foundItem->photo_url }}" alt="" class="h-9 w-9 rounded-lg object-cover shrink-0">
                                            @endif
                                            <div>
                                                <span>{{ $claim->foundItem->item_name }}</span>
                                                <span class="block text-[11px] text-slate-400 font-normal">{{ $claim->foundItem->category->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        <span class="block font-medium text-slate-900">{{ $claim->claimant->name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $claim->claimant->email }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">
                                        {{ $claim->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-status-badge :status="$claim->status" />
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.claims.show', $claim) }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                                            Verify Proof
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $claims->links() }}
                </div>
            @else
                <p class="py-12 text-center text-xs text-slate-400">No claims match the selected status.</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
