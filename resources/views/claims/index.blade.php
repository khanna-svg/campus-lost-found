<x-layouts.app title="My Ownership Claims">
    <div class="mx-auto max-w-5xl space-y-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">My Ownership Claims</h1>
                <p class="mt-1 text-sm text-zinc-600">
                    Track the verification progress and resolution of belongings you have claimed.
                </p>
            </div>
            <a href="{{ route('items.index', ['type' => 'found']) }}" class="inline-flex items-center rounded-xl bg-black px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-zinc-800 transition self-start sm:self-auto">
                Browse Found Items
            </a>
        </div>

        {{-- Claims List --}}
        @if ($claims->isNotEmpty())
            <div class="space-y-4">
                @foreach ($claims as $claim)
                    <div class="rounded-2xl bg-white p-5 sm:p-6 shadow-sm ring-1 ring-zinc-200 hover:shadow-md transition space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            {{-- Item Info --}}
                            <div class="flex items-center gap-4">
                                @if ($claim->foundItem->photo_url)
                                    <img src="{{ $claim->foundItem->photo_url }}" alt="{{ $claim->foundItem->item_name }}"
                                         onerror="this.onerror=null; this.remove();"
                                         class="h-16 w-16 rounded-xl object-cover shrink-0">
                                @else
                                    <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-zinc-100 text-zinc-400 shrink-0">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                    </div>
                                @endif
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold text-zinc-800">
                                            {{ $claim->foundItem->category->name }}
                                        </span>
                                        <span class="text-xs text-zinc-400">·</span>
                                        <span class="text-xs text-zinc-500">Submitted {{ $claim->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <h2 class="text-base font-bold text-zinc-900">
                                        <a href="{{ route('claims.show', $claim) }}" class="hover:text-black hover:underline transition">
                                            {{ $claim->foundItem->item_name }}
                                        </a>
                                    </h2>
                                    <p class="text-xs text-zinc-500">
                                        Found at {{ $claim->foundItem->location_found }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status Badge & Action --}}
                            <div class="flex items-center gap-3 sm:self-center">
                                <x-status-badge :status="$claim->status" />
                                <a href="{{ route('claims.show', $claim) }}" class="rounded-xl bg-zinc-100 px-3.5 py-2 text-xs font-semibold text-zinc-800 ring-1 ring-inset ring-zinc-200 hover:bg-zinc-200 transition">
                                    View Details →
                                </a>
                            </div>
                        </div>

                        {{-- Action Callout Banners depending on status --}}
                        @if ($claim->status->value === 'additional_information_required')
                            <div class="rounded-xl border border-zinc-300 bg-zinc-100 p-3.5 flex items-center justify-between gap-3 text-xs text-zinc-900">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                                    <span><strong>Action Required:</strong> The administrator requested additional details before this claim can be approved.</span>
                                </div>
                                <a href="{{ route('claims.show', $claim) }}" class="text-xs font-bold text-black underline shrink-0">
                                    Respond Now
                                </a>
                            </div>
                        @elseif ($claim->status->value === 'approved')
                            <div class="rounded-xl border border-zinc-900 bg-zinc-900 text-white p-3.5 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    <span><strong>Approved:</strong> Your ownership has been verified! Check pickup instructions to retrieve your item.</span>
                                </div>
                                <a href="{{ route('claims.show', $claim) }}" class="text-xs font-bold text-white underline shrink-0">
                                    View Instructions
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $claims->links() }}
            </div>
        @else
            <x-empty-state
                title="No claims submitted yet"
                description="If you see your lost belonging listed in the campus directory, view its details and click 'Claim Item' to begin the recovery process."
                action-label="Browse Directory"
                :action-url="route('items.index', ['type' => 'found'])" />
        @endif
    </div>
</x-layouts.app>
