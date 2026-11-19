<x-layouts.app title="Notification Center">
    <div class="mx-auto max-w-4xl space-y-8">
        {{-- Header & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">Notification Center</h1>
                <p class="mt-1 text-sm text-zinc-600">
                    Stay updated on report approvals, claim status changes, and campus recovery announcements.
                </p>
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="rounded-xl bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 shadow-sm ring-1 ring-inset ring-zinc-300 hover:bg-zinc-100 transition">
                        ✓ Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        {{-- Filter Tabs (Monochrome) --}}
        <div class="flex items-center gap-2 border-b border-zinc-200 pb-3">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
               @class([
                   'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition',
                   'bg-black text-white' => $filter !== 'unread',
                   'text-zinc-600 hover:bg-zinc-100 hover:text-black' => $filter === 'unread',
               ])>
                All
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               @class([
                   'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5',
                   'bg-black text-white' => $filter === 'unread',
                   'text-zinc-600 hover:bg-zinc-100 hover:text-black' => $filter !== 'unread',
               ])>
                Unread
                @if ($unreadCount > 0)
                    <span class="rounded-full bg-zinc-800 px-1.5 py-0.2 text-[10px] text-white font-bold">
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
                        'bg-zinc-100/70 ring-zinc-300' => ! $notification->isRead(),
                        'bg-white ring-zinc-200 hover:bg-zinc-50' => $notification->isRead(),
                    ])>
                        <div class="flex items-start gap-3.5 min-w-0">
                            {{-- Event Icon --}}
                            <div class="mt-0.5 shrink-0">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-200 text-zinc-900 font-bold">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </span>
                            </div>

                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm font-bold text-zinc-900 truncate">
                                        {{ $notification->title }}
                                    </h2>
                                    @if (! $notification->isRead())
                                        <span class="flex h-2 w-2 rounded-full bg-black"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-600 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                                <span class="text-[11px] text-zinc-400 block pt-0.5">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0 self-center">
                            @if ($notification->action_url)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-black px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-zinc-800 transition">
                                        View
                                    </button>
                                </form>
                            @elseif (! $notification->isRead())
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-200 transition">
                                        Mark Read
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
                title="No notifications"
                description="You're all caught up! Updates regarding your submitted reports and claims will appear here." />
        @endif
    </div>
</x-layouts.app>
