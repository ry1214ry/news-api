<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $news = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'News list retrieved successfully',
            'data' => $news
        ], 200);
    }

    public function show($id)
    {
        $news = News::with('category')->find($id);

        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'News retrieved successfully',
            'data' => $news
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            // 'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:51200',
            'image' => 'nullable|string', 
            'category_id' => 'nullable|exists:categories,id',
            'author' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:100',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $folder = in_array($file->extension(), ['jpg','jpeg','png','gif']) ? 'images' : 'videos';
            $file->move(public_path("uploads/$folder"), $filename);
            $validated['image'] = url("uploads/$folder/$filename");
        } elseif ($request->filled('image')) {
            // If URL is too long, you may want to store filename instead
            $validated['image'] = $request->image;
        }

        $news = News::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'News created successfully',
            'data' => $news->load('category')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'content' => 'nullable|string',
            // 'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:51200',
            'image' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'author' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $folder = in_array($file->extension(), ['jpg','jpeg','png','gif']) ? 'images' : 'videos';
            $file->move(public_path("uploads/$folder"), $filename);
            $validated['image'] = url("uploads/$folder/$filename");
        }

        $news->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'News updated successfully',
            'data' => $news->load('category')
        ], 200);
    }

    public function destroy($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'status' => false,
                'message' => 'News not found'
            ], 404);
        }

        $news->delete();

        return response()->json([
            'status' => true,
            'message' => 'News deleted successfully'
        ], 200);
    }
}