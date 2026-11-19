<x-layouts.admin :title="'Review Lost: ' . $lostItem->item_name">
    <div class="max-w-4xl space-y-6">
        {{-- Breadcrumbs & Header --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.reports.index', ['type' => 'lost']) }}" class="hover:text-slate-700">Lost Reports</a>
                <span>/</span>
                <span class="text-slate-900">Review</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $lostItem->item_name }}
                </h1>
                <x-status-badge :status="$lostItem->status" />
            </div>
            <p class="mt-1 text-xs text-slate-500">
                Reported by <strong>{{ $lostItem->user->name }}</strong> ({{ $lostItem->user->email }}) on {{ $lostItem->created_at->format('M d, Y h:i A') }}
            </p>
        </div>

        {{-- Main Review Card Grid --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Photo & Basic Info --}}
            <div class="md:col-span-1 space-y-4">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    @if ($lostItem->photo_url)
                        <img src="{{ $lostItem->photo_url }}" alt="" class="h-56 w-full object-cover">
                    @else
                        <div class="h-40 bg-slate-50 flex items-center justify-center text-xs text-slate-400">No Photo</div>
                    @endif
                    <div class="p-4 space-y-2 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Category</span>
                            <span class="font-semibold text-slate-900">{{ $lostItem->category->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Date Lost</span>
                            <span class="font-semibold text-slate-900">{{ $lostItem->date_lost->format('F d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Last Seen Location</span>
                            <span class="font-semibold text-slate-900">{{ $lostItem->location_last_seen }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Descriptions & Confidential Details --}}
            <div class="md:col-span-2 space-y-4">
                {{-- Public Description --}}
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 space-y-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Description</h2>
                    <p class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $lostItem->description }}</p>
                </div>

                {{-- Private Identifying Details --}}
                @if ($lostItem->identifying_details)
                    <div class="rounded-2xl bg-amber-50/60 p-5 ring-1 ring-amber-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-900">Private Distinguishing Characteristics</h2>
                            <span class="rounded bg-amber-200/80 px-2 py-0.5 text-[10px] font-bold text-amber-900">Owner & Admin Only</span>
                        </div>
                        <p class="text-sm text-amber-950 font-medium leading-relaxed whitespace-pre-line">{{ $lostItem->identifying_details }}</p>
                    </div>
                @endif

                {{-- Admin Decisions & Actions --}}
                @if ($lostItem->status->value === 'pending_review')
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 space-y-6">
                        <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Review Actions</h2>

                        {{-- Action 1: Approve --}}
                        <form method="POST" action="{{ route('admin.reports.lost.approve', $lostItem) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition">
                                ✓ Approve & Publish Report
                            </button>
                        </form>

                        {{-- Action 2: Request Corrections --}}
                        <form method="POST" action="{{ route('admin.reports.lost.correct', $lostItem) }}" class="pt-4 border-t border-slate-100 space-y-2">
                            @csrf
                            <label for="correction_note" class="block text-xs font-semibold text-slate-700">
                                Request Corrections from Reporter
                            </label>
                            <textarea name="correction_note" id="correction_note" rows="2" placeholder="Specify missing information or clarification needed..." class="block w-full rounded-xl border-0 p-2.5 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600" required></textarea>
                            <button type="submit" class="rounded-xl bg-amber-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-amber-500 transition">
                                Request Corrections
                            </button>
                        </form>

                        {{-- Action 3: Reject --}}
                        <form method="POST" action="{{ route('admin.reports.lost.reject', $lostItem) }}" class="pt-4 border-t border-slate-100 space-y-2">
                            @csrf
                            <label for="rejection_reason" class="block text-xs font-semibold text-rose-700">
                                Reject Lost Report
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="2" placeholder="Reason for rejection (e.g. invalid report, duplicate)..." class="block w-full rounded-xl border-0 p-2.5 text-xs text-slate-900 ring-1 ring-inset ring-rose-300 focus:ring-2 focus:ring-rose-600" required></textarea>
                            <button type="submit" class="rounded-xl bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-500 transition">
                                Reject Report
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
