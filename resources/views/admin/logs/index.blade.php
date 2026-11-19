<x-layouts.admin title="Audit Trail">
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">System Audit Trail</h1>
            <p class="mt-1 text-sm text-slate-500">
                Immutable, append-only security log recording administrative decisions, approvals, and returns.
            </p>
        </div>

        {{-- Filter Bar --}}
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="flex items-center gap-3 text-xs">
                <input type="text" name="action" value="{{ $action !== 'all' ? $action : '' }}" placeholder="Filter by action keyword (e.g. claim, report, user)..." class="flex-1 rounded-xl border-0 p-2 text-xs text-slate-900 ring-1 ring-inset ring-slate-300">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white">Filter</button>
                @if ($action !== 'all')
                    <a href="{{ route('admin.logs.index') }}" class="text-slate-500 hover:text-slate-800 underline">Reset</a>
                @endif
            </form>
        </div>

        {{-- Audit Logs Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            @if ($logs->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Timestamp</th>
                                <th class="py-3 px-4">Actor</th>
                                <th class="py-3 px-4">Action</th>
                                <th class="py-3 px-4">Subject</th>
                                <th class="py-3 px-4">Description</th>
                                <th class="py-3 px-4">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-900 font-semibold">
                                        {{ $log->actor?->name ?? 'System' }}
                                        @if ($log->actor)
                                            <span class="block text-[10px] text-slate-400 font-normal">{{ $log->actor->email }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-mono font-bold text-slate-700">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                        {{ $log->subject_type ? $log->subject_type . '#' . substr($log->subject_id, 0, 8) : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-700">
                                        {{ $log->description }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @else
                <p class="py-12 text-center text-xs text-slate-400">No activity logs recorded yet.</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
