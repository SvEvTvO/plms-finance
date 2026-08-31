<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Ambil semua kategori dalam 1 query saja
        $categories = Category::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        // Pisahkan via koleksi PHP (tanpa query tambahan)
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:income,expense',
            'icon' => 'nullable|string|max:50',
        ]);

        Category::create([
            'user_id' => auth()->id(),
            'name'    => $validated['name'],
            'type'    => $validated['type'],
            'icon'    => $validated['icon'] ?? 'ti-circle',
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:income,expense',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? $category->icon,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
