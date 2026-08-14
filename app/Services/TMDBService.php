<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TMDBService — Original K-Drama Dashboard service (used by DashboardController).
 * Handles Korean content filtering, actor profiles, trending now, etc.
 */
class TMDBService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url', 'https://api.themoviedb.org/3');
        $this->apiKey = config('services.tmdb.api_key');
        $this->apiToken = config('services.tmdb.api_token');
    }

    /**
     * Send HTTP request to TMDB API.
     */
    protected function request(string $endpoint, array $queryParams = [])
    {
        $client = Http::baseUrl($this->baseUrl);

        // Authenticate using Bearer token if provided, fallback to API Key
        if ($this->apiToken && $this->apiToken !== 'your_bearer_token_here' && $this->apiToken !== '') {
            $client = $client->withToken($this->apiToken);
        } elseif ($this->apiKey && $this->apiKey !== 'your_api_key_here' && $this->apiKey !== '') {
            $queryParams['api_key'] = $this->apiKey;
        } else {
            Log::warning('TMDB API Key or Token is not configured.');
        }

        if (!isset($queryParams['language'])) {
            $queryParams['language'] = 'en-US';
        }

        $response = $client->get($endpoint, $queryParams);

        if ($response->failed()) {
            Log::error("TMDB API request failed for endpoint: {$endpoint}", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Get K-Movies or K-Dramas filtered by Korean language.
     */
    public function getKoreanContent(string $type = 'movie', int $page = 1)
    {
        $params = [
            'with_original_language' => 'ko',
            'page'    => $page,
            'sort_by' => 'popularity.desc',
        ];

        if ($type === 'movie') {
            $params['primary_release_date.gte'] = '2024-01-01';
            $params['primary_release_date.lte'] = '2025-12-31';
        } elseif ($type === 'tv') {
            $params['first_air_date.gte'] = '2024-01-01';
            $params['first_air_date.lte'] = '2025-12-31';
        }

        return $this->request("discover/{$type}", $params);
    }

    /**
     * Get "Trending Now" contents using endpoint trending/all/week.
     * Optionally filters only Korean content (default: true).
     */
    public function getTrendingNow(int $page = 1, bool $koreanOnly = true)
    {
        $data = $this->request('trending/all/week', ['page' => $page]);

        if (!$data || !isset($data['results'])) {
            return $data;
        }

        if ($koreanOnly) {
            $data['results'] = array_values(array_filter($data['results'], function ($item) {
                return isset($item['original_language']) && $item['original_language'] === 'ko';
            }));
        }

        return $data;
    }

    /**
     * Get detailed info for a specific Movie or TV Show by ID.
     */
    public function getShowDetails(string $type, int $id): array
    {
        $params = [
            'append_to_response' => 'credits,videos,translations',
            'language'           => 'en-US',
        ];

        $data = $this->request("{$type}/{$id}", $params);

        if (!$data) {
            return [];
        }

        // Format common fields
        $data['media_type']  = $type;
        $data['cast']        = $data['credits']['cast'] ?? [];
        $data['videos_list'] = $data['videos']['results'] ?? [];

        if ($type === 'tv') {
            $data['title_english']  = $data['name'] ?? '';
            $data['title_original'] = $data['original_name'] ?? '';
            $data['release_date']   = $data['first_air_date'] ?? null;
            $data['runtime']        = $data['episode_run_time'][0] ?? null;

            $tvStatus = $data['status'] ?? '';
            $data['kdrama_status'] = in_array($tvStatus, ['Returning Series', 'In Production'])
                ? 'Ongoing'
                : 'Completed';
        } else {
            $data['title_english']  = $data['title'] ?? '';
            $data['title_original'] = $data['original_title'] ?? '';
        }

        // Synopsis fallback
        $data['synopsis_default'] = $data['overview'] ?? 'Sinopsis tidak tersedia.';

        // Extract Korean translation if available
        $translations = $data['translations']['translations'] ?? [];
        foreach ($translations as $t) {
            if ($t['iso_3166_1'] === 'ID') {
                if (!empty($t['data']['overview'])) {
                    $data['translations']['id'] = $t;
                }
                break;
            }
        }

        return $data;
    }

    /**
     * Get recommendations for a given Movie or TV Show.
     */
    public function getRecommendations(string $type, int $id): array
    {
        $data = $this->request("{$type}/{$id}/recommendations");
        return $data ?? [];
    }

    /**
     * Get actor profile and top Korean credits.
     */
    public function getActorProfile(int $actorId): ?array
    {
        $data = $this->request("person/{$actorId}", [
            'append_to_response' => 'combined_credits',
        ]);

        if (!$data) {
            return null;
        }

        $allCredits  = $data['combined_credits']['cast'] ?? [];
        $koCredits   = array_filter($allCredits, function ($c) {
            return ($c['original_language'] ?? '') === 'ko' || ($c['popularity'] ?? 0) > 1;
        });

        // Fallback to all credits if koCredits filter is empty
        if (empty($koCredits)) {
            $koCredits = $allCredits;
        }

        usort($koCredits, fn($a, $b) => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));
        $data['credits'] = array_slice(array_values($koCredits), 0, 6);

        return $data;
    }

    /**
     * Get videos list for a given Movie or TV Show.
     */
    public function getShowVideos(string $type, int $id)
    {
        return $this->request("{$type}/{$id}/videos");
    }

    /**
     * Get TV season details.
     */
    public function getTvSeasonDetails(int $id, int $seasonNumber)
    {
        return $this->request("tv/{$id}/season/{$seasonNumber}");
    }

    /**
     * Get Trending Movies.
     */
    public function getTrendingMovies()
    {
        return $this->request('trending/movie/week');
    }

    /**
     * Get Trending TV Shows.
     */
    public function getTrendingTvShows()
    {
        return $this->request('trending/tv/week');
    }

    /**
     * Get Movie Details.
     */
    public function getMovieDetails($tmdbId)
    {
        return $this->request("movie/{$tmdbId}");
    }

    /**
     * Get TV Show Details.
     */
    public function getTvShowDetails($tmdbId)
    {
        return $this->request("tv/{$tmdbId}");
    }

    /**
     * Search multiple types of media (movies, tv shows, people) using query.
     */
    public function searchMulti(string $query, int $page = 1)
    {
        return $this->request('search/multi', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    /**
     * Get all-time popular K-Movies or K-Dramas (no date restrictions).
     */
    public function getPopularKoreanContent(string $type = 'tv', int $page = 1)
    {
        return $this->request("discover/{$type}", [
            'with_original_language' => 'ko',
            'page'    => $page,
            'sort_by' => 'popularity.desc',
        ]);
    }
}
