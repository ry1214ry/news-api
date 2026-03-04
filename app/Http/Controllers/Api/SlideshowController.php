<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SlideshowController extends Controller
{
    /**
     * Get all slides, ordered by sort_order then latest.
     * Optionally filter to active slides only.
     */
    public function index(Request $request)
    {
        $query = Slideshow::orderBy('sort_order')->latest();

        // Public API: only show active slides
        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $slides = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Slides retrieved successfully',
            'data'    => $slides,
        ], 200);
    }

    /**
     * Store a new slide.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image'       => 'required|string|url|max:2000',
            'link_url'    => 'nullable|string|url|max:2000',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        // Handle file upload (overrides image URL if provided)
        if ($request->hasFile('file')) {
            $validated['image'] = $this->uploadImage($request);
        }

        $validated['sort_order'] = $validated['sort_order'] ?? (Slideshow::max('sort_order') + 1);
        $validated['is_active']  = $validated['is_active'] ?? true;

        $slide = Slideshow::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Slide created successfully',
            'data'    => $slide,
        ], 201);
    }

    /**
     * Show a single slide.
     */
    public function show(Slideshow $slideshow)
    {
        return response()->json([
            'status' => true,
            'data'   => $slideshow,
        ], 200);
    }

    /**
     * Update a slide.
     */
    public function update(Request $request, Slideshow $slideshow)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image'       => 'sometimes|required|string|url|max:2000',
            'link_url'    => 'nullable|string|url|max:2000',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $this->deleteOldImage($slideshow->image);
            $validated['image'] = $this->uploadImage($request);
        }

        $slideshow->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Slide updated successfully',
            'data'    => $slideshow,
        ], 200);
    }

    /**
     * Reorder slides by providing an ordered array of IDs.
     * Body: { "ids": [3, 1, 4, 2] }
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:slideshows,id',
        ]);

        foreach ($request->ids as $order => $id) {
            Slideshow::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Slides reordered successfully',
        ], 200);
    }

    /**
     * Toggle is_active status of a slide.
     */
    public function toggleActive(Slideshow $slideshow)
    {
        $slideshow->update(['is_active' => !$slideshow->is_active]);

        return response()->json([
            'status'    => true,
            'message'   => 'Slide status updated',
            'is_active' => $slideshow->is_active,
        ], 200);
    }

    /**
     * Delete a slide and remove its uploaded image if locally stored.
     */
    public function destroy(Slideshow $slideshow)
    {
        $this->deleteOldImage($slideshow->image);
        $slideshow->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Slide deleted successfully',
        ], 200);
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function uploadImage(Request $request): string
    {
        $file     = $request->file('file');
        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $ext;

        $file->move(public_path('uploads/slides'), $filename);

        return url("uploads/slides/{$filename}");
    }

    private function deleteOldImage(?string $imageUrl): void
    {
        if (!$imageUrl) return;

        $appUrl = rtrim(config('app.url'), '/');
        if (str_starts_with($imageUrl, $appUrl . '/uploads/slides/')) {
            $relativePath = str_replace($appUrl . '/', '', $imageUrl);
            $fullPath = public_path($relativePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}