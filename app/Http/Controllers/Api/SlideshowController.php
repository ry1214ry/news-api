<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use Illuminate\Http\Request;

class SlideshowController extends Controller
{
    // GET ALL SLIDES
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Slideshow::latest()->get()
        ], 200);
    }

    // STORE NEW SLIDE (TEXT IMAGE LINK)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|string|max:1000' // store as text
        ]);

        $slide = Slideshow::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Slide created successfully',
            'data' => $slide
        ], 201);
    }

    // SHOW SINGLE SLIDE
    public function show(Slideshow $slideshow)
    {
        return response()->json([
            'status' => true,
            'data' => $slideshow
        ], 200);
    }

    // UPDATE SLIDE (TEXT IMAGE LINK)
    public function update(Request $request, Slideshow $slideshow)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|string|max:1000'
        ]);

        $slideshow->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Slide updated successfully',
            'data' => $slideshow
        ], 200);
    }

    // DELETE SLIDE
    public function destroy(Slideshow $slideshow)
    {
        $slideshow->delete();

        return response()->json([
            'status' => true,
            'message' => 'Slide deleted successfully'
        ], 200);
    }
}