<x-layouts.app title="Notification Center">
    <div class="mx-auto max-w-4xl space-y-8">
        {{-- Header & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Notification Center</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Stay updated on report approvals, claim status changes, and campus recovery announcements.
                </p>
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition">
                        ✓ Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
               @class([
                   'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition',
                   'bg-slate-900 text-white' => $filter !== 'unread',
                   'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => $filter === 'unread',
               ])>
                All
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               @class([
                   'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5',
                   'bg-slate-900 text-white' => $filter === 'unread',
                   'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => $filter !== 'unread',
               ])>
                Unread
                @if ($unreadCount > 0)
                    <span class="rounded-full bg-brand-500 px-1.5 py-0.2 text-[10px] text-white font-bold">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Notifications List --}}
        @if ($notifications->isNotEmpty())
            <div class="space-y-3">
                @foreach ($notifications as $notification)
                    <div @class([
                        'group flex items-start justify-between gap-4 rounded-2xl p-4 sm:p-5 transition ring-1',
                        'bg-brand-50/40 ring-brand-200/80' => ! $notification->isRead(),
                        'bg-white ring-slate-200 hover:bg-slate-50/70' => $notification->isRead(),
                    ])>
                        <div class="flex items-start gap-3.5 min-w-0">
                            {{-- Event Icon --}}
                            <div class="mt-0.5 shrink-0">
                                @if (str_contains($notification->type, 'approved') || str_contains($notification->type, 'returned'))
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    </span>
                                @elseif (str_contains($notification->type, 'rejected'))
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    </span>
                                @elseif (str_contains($notification->type, 'info_required') || str_contains($notification->type, 'correction'))
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                                    </span>
                                @else
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-100 text-brand-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm font-bold text-slate-900 truncate">
                                        {{ $notification->title }}
                                    </h2>
                                    @if (! $notification->isRead())
                                        <span class="flex h-2 w-2 rounded-full bg-brand-600"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                                <span class="text-[11px] text-slate-400 block pt-0.5">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0 self-center">
                            @if ($notification->action_url)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                                        View →
                                    </button>
                                </form>
                            @elseif (! $notification->isRead())
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition">
                                        Mark read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <x-empty-state
                title="No notifications yet"
                description="{{ $filter === 'unread' ? 'You have caught up with all your notifications!' : 'When administrators review your reports or claims, notifications will appear here.' }}"
                action-label="Browse Directory"
                :action-url="route('items.index')" />
        @endif
    </div>
</x-layouts.app>
