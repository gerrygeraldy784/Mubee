<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViewCount;

class ViewController extends Controller
{
    /**
     * Increment views_count for a movie or show episode securely using Eloquent increment().
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'episode_id' => 'nullable|string', // episode identifier (e.g. S01E01) or ID
        ]);

        // Find existing record or initialize a new one with views_count default 0
        $viewCount = ViewCount::firstOrCreate([
            'tmdb_id' => $validated['tmdb_id'],
            'episode_id' => $validated['episode_id'] ?? null,
        ]);

        // Safely increment views_count database value by 1
        $viewCount->increment('views_count');

        return response()->json([
            'success' => true,
            'message' => 'View count updated successfully.',
            'data' => [
                'tmdb_id' => $viewCount->tmdb_id,
                'episode_id' => $viewCount->episode_id,
                'views_count' => $viewCount->views_count,
            ]
        ]);
    }
}
