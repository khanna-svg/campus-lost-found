<x-layouts.admin title="Claims Management">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 sm:text-3xl">Claims Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-zinc-500">
                    Verify claimant evidence, approve pickups, and record physical item handovers.
                </p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-50 transition self-start sm:self-auto">
                ← Back to Dashboard
            </a>
        </div>

        {{-- Status Filter Badges --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-zinc-200 pb-3 text-xs">
            <span class="text-zinc-400 font-bold uppercase tracking-wider text-[10px]">Filter:</span>
            @foreach ([
                'pending' => 'Pending Queue (' . $counts['pending'] . ')',
                'under_review' => 'In Review (' . $counts['under_review'] . ')',
                'additional_information_required' => 'Info Requested (' . $counts['info_required'] . ')',
                'approved' => 'Approved / Pickup (' . $counts['approved'] . ')',
                'completed' => 'Returned (' . $counts['completed'] . ')',
                'all' => 'All Claims',
            ] as $k => $label)
                <a href="{{ route('admin.claims.index', ['status' => $k]) }}"
                   @class([
                       'px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs',
                       'bg-black text-white' => $status === $k,
                       'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' => $status !== $k,
                   ])>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Claims Table --}}
        <div class="rounded-3xl bg-white border border-zinc-200 shadow-xs overflow-hidden">
            @if ($claims->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 text-zinc-400 uppercase tracking-wider text-[10px] font-bold border-b border-zinc-200">
                            <tr>
                                <th class="py-3.5 px-5">Claimed Item</th>
                                <th class="py-3.5 px-5">Claimant</th>
                                <th class="py-3.5 px-5">Date Submitted</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach ($claims as $claim)
                                <tr class="hover:bg-zinc-50/50">
                                    <td class="py-3.5 px-5 font-bold text-zinc-900">
                                        <div class="flex items-center gap-3">
                                            @if ($claim->foundItem->photo_url)
                                                <img src="{{ $claim->foundItem->photo_url }}" alt="" class="h-10 w-10 rounded-xl object-cover border border-zinc-200 shrink-0">
                                            @else
                                                <div class="h-10 w-10 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-400 font-bold shrink-0">
                                                    ?
                                                </div>
                                            @endif
                                            <div>
                                                <span class="block text-zinc-900 font-bold">{{ $claim->foundItem->item_name }}</span>
                                                <span class="block text-[11px] text-zinc-400 font-normal">{{ $claim->foundItem->category->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-5 text-zinc-600">
                                        <span class="block font-bold text-zinc-900">{{ $claim->claimant->name }}</span>
                                        <span class="text-[11px] text-zinc-400">{{ $claim->claimant->email }}</span>
                                    </td>
                                    <td class="py-3.5 px-5 text-zinc-500">
                                        {{ $claim->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <x-status-badge :status="$claim->status" />
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if (in_array($claim->status->value, ['pending', 'under_review']))
                                                <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="pickup_instructions" value="Your item is ready for pickup at the Admin Office. Please bring your School ID.">
                                                    <button type="submit"
                                                            onclick="return confirm('Approve claim and send email to {{ addslashes($claim->claimant->name) }}?');"
                                                            class="rounded-xl bg-black px-3 py-1.5 text-xs font-bold text-white hover:bg-zinc-800 transition">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.claims.show', $claim) }}"
                                               class="rounded-xl border border-zinc-300 bg-white px-3 py-1.5 text-xs font-bold text-zinc-800 hover:bg-zinc-50 transition">
                                                Review
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-zinc-100">
                    {{ $claims->links() }}
                </div>
            @else
                <p class="py-12 text-center text-xs text-zinc-400">No claims match the selected status.</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
