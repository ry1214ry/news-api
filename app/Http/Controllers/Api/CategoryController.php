<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories with optional search and pagination.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('news')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $categories = $query->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Category list retrieved successfully',
            'data'    => $categories->items(),
            'meta'    => [
                'current_page' => $categories->currentPage(),
                'last_page'    => $categories->lastPage(),
                'per_page'     => $categories->perPage(),
                'total'        => $categories->total(),
            ],
        ], 200);
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Category created successfully',
            'data'    => $category,
        ], 201);
    }

    /**
     * Show a single category with its news count.
     */
    public function show($id)
    {
        $category = Category::withCount('news')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $category,
        ], 200);
    }

    /**
     * Update a category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Category updated successfully',
            'data'    => $category,
        ], 200);
    }

    /**
     * Delete a category. Prevents deletion if it has related news.
     */
    public function destroy($id)
    {
        $category = Category::withCount('news')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        if ($category->news_count > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot delete category with existing news. Reassign or delete news first.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Category deleted successfully',
        ], 200);
    }
}