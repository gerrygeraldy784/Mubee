<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Cache;

class RecommendationController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Get personalized K-Movie and K-Drama recommendations based on user's watch history genres.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendations(Request $request)
    {
        // Authenticated user check, fallback to user_id param for testing convenience
        $user = $request->user();
        $userId = $user ? $user->id : $request->query('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or missing user_id query parameter.'
            ], 401);
        }

        // 1. Get latest 10 watch history records
        $watchHistory = WatchHistory::where('user_id', $userId)
            ->orderBy('watched_at', 'desc')
            ->limit(10)
            ->get();

        // Fallback: If watch history is empty, recommend trending/popular Korean content
        if ($watchHistory->isEmpty()) {
            $trendingMovies = $this->tmdbService->getKoreanContent('movie', 1);
            $trendingTv = $this->tmdbService->getKoreanContent('tv', 1);

            return response()->json([
                'success' => true,
                'recommendation_basis' => 'trending_fallback',
                'data' => [
                    'movies' => $trendingMovies['results'] ?? [],
                    'tv_shows' => $trendingTv['results'] ?? [],
                ]
            ]);
        }

        // 2. Count genre frequencies from watch history
        $genreFrequencies = [];

        foreach ($watchHistory as $record) {
            $cacheKey = "tmdb_show_genres_{$record->media_type}_{$record->tmdb_id}";
            
            // Cache show details to avoid exceeding TMDB API request rate limits
            $genres = Cache::remember($cacheKey, now()->addDays(7), function () use ($record) {
                $details = $this->tmdbService->getShowDetails($record->media_type, $record->tmdb_id);
                if ($details && isset($details['genres'])) {
                    return collect($details['genres'])->pluck('id')->toArray();
                }
                return [];
            });

            foreach ($genres as $genreId) {
                $genreFrequencies[$genreId] = ($genreFrequencies[$genreId] ?? 0) + 1;
            }
        }

        // 3. Extract top 3 most-watched genre IDs
        arsort($genreFrequencies);
        $topGenreIds = array_slice(array_keys($genreFrequencies), 0, 3);

        // 4. Query TMDB using top genre IDs
        $recommendedMovies = $this->tmdbService->getRecommendationsBasedOnGenres($topGenreIds, 'movie', 1);
        $recommendedTv = $this->tmdbService->getRecommendationsBasedOnGenres($topGenreIds, 'tv', 1);

        return response()->json([
            'success' => true,
            'recommendation_basis' => [
                'records_analyzed' => $watchHistory->count(),
                'top_genres' => $topGenreIds,
            ],
            'data' => [
                'movies' => $recommendedMovies['results'] ?? [],
                'tv_shows' => $recommendedTv['results'] ?? [],
            ]
        ]);
    }
}
