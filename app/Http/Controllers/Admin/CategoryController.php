<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List all categories with associated item statistics.
     */
    public function index(): View
    {
        $categories = ItemCategory::withCount(['lostItems', 'foundItems'])
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Create a new item category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:item_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $slug = Str::slug($validated['name']);

        ItemCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Toggle active / inactive state of a category.
     */
    public function toggleActive(ItemCategory $category): RedirectResponse
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        $state = $category->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Category '{$category->name}' has been {$state}.");
    }
}
