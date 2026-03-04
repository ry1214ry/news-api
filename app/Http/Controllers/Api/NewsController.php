<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    // Allowed MIME types for uploads
    private const IMAGE_MIMES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const VIDEO_MIMES = ['mp4', 'mov', 'avi'];

    /**
     * List news with filters, search, and pagination.
     */
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id',
            'search'      => 'nullable|string|max:255',
            'author'      => 'nullable|string|max:100',
            'per_page'    => 'nullable|integer|min:1|max:100',
        ]);

        $query = News::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $news = $query->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'News list retrieved successfully',
            'data'    => $news->items(),
            'meta'    => [
                'current_page' => $news->currentPage(),
                'last_page'    => $news->lastPage(),
                'per_page'     => $news->perPage(),
                'total'        => $news->total(),
            ],
        ], 200);
    }

    /**
     * Show a single news item.
     */
    public function show($id)
    {
        $news = News::with('category')->find($id);

        if (!$news) {
            return response()->json([
                'status'  => false,
                'message' => 'News not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'News retrieved successfully',
            'data'    => $news,
        ], 200);
    }

    /**
     * Store a new news item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'content'     => 'nullable|string',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi|max:51200',
            'image'       => 'nullable|string|url|max:2000',
            'category_id' => 'nullable|integer|exists:categories,id',
            'author'      => 'nullable|string|max:100',
            'source'      => 'nullable|string|max:255',
        ]);

        $validated['image'] = $this->handleFileUpload($request, $validated['image'] ?? null);

        $news = News::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'News created successfully',
            'data'    => $news->load('category'),
        ], 201);
    }

    /**
     * Update an existing news item.
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'status'  => false,
                'message' => 'News not found',
            ], 404);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'content'     => 'nullable|string',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi|max:51200',
            'image'       => 'nullable|string|url|max:2000',
            'category_id' => 'nullable|integer|exists:categories,id',
            'author'      => 'nullable|string|max:100',
            'source'      => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file') || $request->filled('image')) {
            // Delete old uploaded file if it was stored locally
            $this->deleteOldFile($news->image);
            $validated['image'] = $this->handleFileUpload($request, $validated['image'] ?? null);
        }

        $news->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'News updated successfully',
            'data'    => $news->load('category'),
        ], 200);
    }

    /**
     * Delete a news item and its associated uploaded file.
     */
    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'status'  => false,
                'message' => 'News not found',
            ], 404);
        }

        $this->deleteOldFile($news->image);
        $news->delete();

        return response()->json([
            'status'  => true,
            'message' => 'News deleted successfully',
        ], 200);
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    /**
     * Handle file upload or fall back to a URL string.
     * Returns the public URL of the stored file, or the provided URL string.
     */
    private function handleFileUpload(Request $request, ?string $fallbackUrl): ?string
    {
        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $ext       = strtolower($file->getClientOriginalExtension());
            $folder    = in_array($ext, self::IMAGE_MIMES) ? 'images' : 'videos';
            $filename  = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $ext;

            $file->move(public_path("uploads/{$folder}"), $filename);

            return url("uploads/{$folder}/{$filename}");
        }

        return $fallbackUrl;
    }

    /**
     * Delete a locally uploaded file (skip external URLs).
     */
    private function deleteOldFile(?string $imageUrl): void
    {
        if (!$imageUrl) return;

        // Only delete if it's our own hosted file
        $appUrl = rtrim(config('app.url'), '/');
        if (str_starts_with($imageUrl, $appUrl . '/uploads/')) {
            $relativePath = str_replace($appUrl . '/', '', $imageUrl);
            $fullPath = public_path($relativePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}