<x-layouts.admin title="Category Management">
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Item Categories</h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage property categories used to classify campus lost and found reports.
            </p>
        </div>

        {{-- Add Category Form --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-base font-bold text-slate-900 mb-3">Add New Category</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                @csrf
                <div class="flex-1">
                    <label for="name" class="block text-xs font-semibold text-slate-700">Category Name *</label>
                    <input type="text" name="name" id="name" required placeholder="e.g. Musical Instruments" class="mt-1 w-full rounded-xl border-0 py-2 px-3 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                </div>
                <div class="flex-1">
                    <label for="description" class="block text-xs font-semibold text-slate-700">Description (Optional)</label>
                    <input type="text" name="description" id="description" placeholder="Guitars, violins, sheet music..." class="mt-1 w-full rounded-xl border-0 py-2 px-3 text-xs text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-brand-600">
                </div>
                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-500 transition shrink-0">
                    + Add Category
                </button>
            </form>
        </div>

        {{-- Categories Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Slug</th>
                            <th class="py-3 px-4">Found Items</th>
                            <th class="py-3 px-4">Lost Items</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($categories as $category)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $category->name }}
                                    @if ($category->description)
                                        <span class="block text-[11px] text-slate-400 font-normal">{{ $category->description }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">{{ $category->slug }}</td>
                                <td class="py-3 px-4 text-slate-600 font-medium">{{ $category->found_items_count }}</td>
                                <td class="py-3 px-4 text-slate-600 font-medium">{{ $category->lost_items_count }}</td>
                                <td class="py-3 px-4">
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                        'bg-emerald-50 text-emerald-700' => $category->is_active,
                                        'bg-slate-100 text-slate-600' => ! $category->is_active,
                                    ])>
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form method="POST" action="{{ route('admin.categories.toggle-active', $category) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-700 hover:bg-slate-200">
                                            {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
