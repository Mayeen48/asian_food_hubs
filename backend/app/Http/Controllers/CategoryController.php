<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::with('children.children')->whereNull('parent_id')->get();
    }

    public function show(Category $category)
    {
        return $category->load('children.children');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id(); // first update = creator
        return Category::create($data);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        $data['updated_by'] = auth()->id();
        $category->update($data);
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->children()->delete();
        $category->delete();
        return response()->noContent();
    }
}
