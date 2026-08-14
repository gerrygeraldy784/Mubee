<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use App\Models\Bookmark;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Display a listing of Korean movies and dramas.
     */
    public function index()
    {
        $trendingMovies = $this->tmdbService->getKoreanContent('movie')['results'] ?? [];
        $trendingTv = $this->tmdbService->getKoreanContent('tv')['results'] ?? [];

        return view('movies.index', compact('trendingMovies', 'trendingTv'));
    }

    /**
     * Display the movie details and player.
     */
    public function show($id)
    {
        $isCustomMockId = ($id >= 10000 && $id <= 10100);
        $movie = null;

        if (!$isCustomMockId) {
            try {
                $movie = $this->tmdbService->getMovieDetails($id);
            } catch (\Exception $e) {
                $movie = null;
            }
        }

        if (!$movie || $isCustomMockId) {
            $dashboardCtrl = new DashboardController($this->tmdbService);
            $movie = $dashboardCtrl->getMockDetails('movie', $id);
        }

        $userId = Auth::id();
        $isBookmarked = false;
        
        if ($userId) {
            // Check if user bookmarked this movie
            $isBookmarked = Bookmark::where('user_id', $userId)
                ->where('tmdb_id', $id)
                ->where('type', 'movie')
                ->exists();

            // Save or update watch history
            WatchHistory::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tmdb_id' => $id,
                    'media_type' => 'movie',
                ],
                [
                    'last_watched_at' => now(),
                    'watched_at' => now(), // fallback for compatibility with existing DB schema
                ]
            );
        }

        // We will embed vidsrc player: https://vidsrc.cc/v2/embed/movie/{id}
        $embedUrl = "https://vidsrc.cc/v2/embed/movie/{$id}";

        return view('movies.show', compact('movie', 'isBookmarked', 'embedUrl'));
    }

    /**
     * Toggle bookmark state for movie/show.
     */
    public function toggleBookmark(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'type' => 'required|string|in:movie,tv',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('tmdb_id', $request->tmdb_id)
            ->where('type', $request->type)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
            $message = 'Removed from bookmarks';
        } else {
            Bookmark::create([
                'user_id' => $userId,
                'tmdb_id' => $request->tmdb_id,
                'type' => $request->type,
                'title' => $request->title,
                'poster_path' => $request->poster_path,
            ]);
            $bookmarked = true;
            $message = 'Added to bookmarks';
        }

        return response()->json([
            'success' => true,
            'bookmarked' => $bookmarked,
            'message' => $message
        ]);
    }
}
