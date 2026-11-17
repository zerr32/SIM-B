<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories', [
            'categories' => Category::withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'keterangan' => 'nullable|string',
        ]);
        Category::create($data);
        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'keterangan' => 'nullable|string',
        ]);
        $category->update($data);
        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->items()->withTrashed()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih dipakai oleh barang, tidak bisa dihapus.']);
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}
