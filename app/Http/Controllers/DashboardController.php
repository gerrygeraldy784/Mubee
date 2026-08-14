<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Display the streaming dashboard homepage with K-content from TMDB or Fallback Mock.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');

        // Check if TMDB API is configured
        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $trendingList = [];
        $moviesList = [];
        $tvList = [];
        $actorsList = [];
        $popularTvList = [];
        $popularMoviesList = [];
        $isMocked = false;

        if ($hasCredentials) {
            try {
                // 1. Fetch Trending K-Content: try trending endpoint first, fallback to discover/tv
                $trending = $this->tmdbService->getTrendingNow(1, true);
                $trendingList = $trending['results'] ?? [];

                // If trending returned no Korean results, use popular Korean TV as hero banner source
                if (empty($trendingList)) {
                    $fallbackTrending = $this->tmdbService->getPopularKoreanContent('tv', 1);
                    $trendingList = array_slice($fallbackTrending['results'] ?? [], 0, 10);
                    // Add media_type for consistency
                    foreach ($trendingList as &$t) {
                        $t['media_type'] = 'tv';
                    }
                    unset($t);
                }

                // 2. Fetch Korean Movies (2024-2025)
                $movies = $this->tmdbService->getKoreanContent('movie', 1);
                $moviesList = $movies['results'] ?? [];

                // 3. Fetch Korean TV Shows (K-Dramas, 2024-2025)
                $tv = $this->tmdbService->getKoreanContent('tv', 1);
                $tvListRaw = $tv['results'] ?? [];

                // Map status label Ongoing vs Completed for dramas
                foreach ($tvListRaw as $item) {
                    $showId = $item['id'];
                    $kdramaStatus = Cache::remember("tmdb_tv_status_{$showId}", now()->addDays(3), function () use ($showId) {
                        $details = $this->tmdbService->getShowDetails('tv', $showId);
                        return $details['kdrama_status'] ?? 'Ongoing';
                    });
                    $item['kdrama_status'] = $kdramaStatus;
                    $tvList[] = $item;
                }

                // 4. Fetch All-Time Popular Korean TV Shows
                $popularTvData = $this->tmdbService->getPopularKoreanContent('tv', 1);
                $popularTvListRaw = $popularTvData['results'] ?? [];
                foreach (array_slice($popularTvListRaw, 0, 8) as $item) {
                    $showId = $item['id'];
                    $kdramaStatus = Cache::remember("tmdb_tv_status_{$showId}", now()->addDays(3), function () use ($showId) {
                        $details = $this->tmdbService->getShowDetails('tv', $showId);
                        return $details['kdrama_status'] ?? 'Ongoing';
                    });
                    $item['kdrama_status'] = $kdramaStatus;
                    $popularTvList[] = $item;
                }

                // 5. Fetch All-Time Popular Korean Movies
                $popularMoviesData = $this->tmdbService->getPopularKoreanContent('movie', 1);
                $popularMoviesList = $popularMoviesData['results'] ?? [];

                // 30 Verified Korean actors & actresses IDs:
                $actorIds = [
                    1251581, 582130, 74421, 86889, 1252318, 1067849, 2112859, 1014784, 
                    1252045, 1156197, 1134684, 116175, 1537768, 1353829, 140335, 63436, 
                    1252016, 1347525, 1245104, 150903, 2117890, 587634, 1878952, 1253391, 
                    1095818, 1459772, 1470763, 1571598, 1238592, 1613836
                ];
                foreach ($actorIds as $id) {
                    $actorData = Cache::remember("actor_profile_{$id}", now()->addDays(7), function () use ($id) {
                        return $this->tmdbService->getActorProfile($id);
                    });
                    if ($actorData) {
                        $actorsList[] = $actorData;
                    }
                }
            } catch (\Exception $e) {
                // If API call throws exception, fallback to mock
                $isMocked = true;
                Log::error('Dashboard API error: ' . $e->getMessage());
            }
        }

        // Trigger mock fallback only if credentials are absent or entire API call failed
        if (!$hasCredentials || $isMocked) {
            $isMocked = true;
            $trendingList    = $this->getMockTrending();
            $moviesList      = $this->getMockMovies();
            $tvList          = $this->getMockTvShows();
            $actorsList      = $this->getMockActors();
            $popularTvList   = $this->getMockPopularTvShows();
            $popularMoviesList = $this->getMockPopularMovies();
        } else {
            // Safety fallback for individual empty sections
            if (empty($trendingList)) {
                $trendingList = $this->getMockTrending();
            }
            if (empty($moviesList)) {
                $moviesList = $this->getMockMovies();
            }
            if (empty($tvList)) {
                $tvList = $this->getMockTvShows();
            }
            if (empty($actorsList)) {
                $actorsList = $this->getMockActors();
            }
            if (empty($popularTvList)) {
                $popularTvList = $this->getMockPopularTvShows();
            }
            if (empty($popularMoviesList)) {
                $popularMoviesList = $this->getMockPopularMovies();
            }
        }

        // Ensure trendingList has exactly top 10 items
        if (count($trendingList) < 10) {
            $existingIds = array_column($trendingList, 'id');
            $fillers = array_merge($popularTvList, $moviesList, $tvList, $this->getMockTrending());
            foreach ($fillers as $f) {
                if (!in_array($f['id'], $existingIds)) {
                    if (!isset($f['media_type'])) {
                        $f['media_type'] = isset($f['name']) ? 'tv' : 'movie';
                    }
                    $trendingList[] = $f;
                    $existingIds[] = $f['id'];
                    if (count($trendingList) >= 10) break;
                }
            }
        }
        $trendingList = array_slice($trendingList, 0, 10);

        // Define primary banner/hero item
        $heroItem = $trendingList[0] ?? ($tvList[0] ?? null);

        // Fetch dynamic Continue Watching progress for the logged-in user
        $userId = Auth::id();
        $latestProgress = \App\Models\VideoProgress::where('user_id', $userId)
            ->where('is_finished', false)
            ->orderBy('updated_at', 'desc')
            ->first();

        $continueWatching = null;
        if ($latestProgress) {
            $cwType = $latestProgress->episode_id ? 'tv' : 'movie';
            $showDetails = null;
            if ($hasCredentials) {
                try {
                    $showDetails = $this->tmdbService->getShowDetails($cwType, $latestProgress->tmdb_id);
                } catch (\Exception $e) {
                    // ignore and fallback to mock
                }
            }
            if (!$showDetails) {
                $showDetails = $this->getMockDetails($cwType, $latestProgress->tmdb_id);
            }
            
            if ($showDetails) {
                $episodeName = null;
                $episodeOverview = null;

                if ($cwType === 'tv' && $latestProgress->episode_id) {
                    // Parse season and episode numbers from episode_id (e.g. S01E02)
                    $seasonNum = 1;
                    $episodeNum = 1;
                    if (preg_match('/S(\d+)E(\d+)/i', $latestProgress->episode_id, $matches)) {
                        $seasonNum = (int)$matches[1];
                        $episodeNum = (int)$matches[2];
                    } else if (is_numeric($latestProgress->episode_id)) {
                        $episodeNum = (int)$latestProgress->episode_id;
                    }

                    // Fetch season details
                    $seasonDetails = null;
                    if ($hasCredentials && !$isMocked) {
                        try {
                            $seasonDetails = $this->tmdbService->getTvSeasonDetails($latestProgress->tmdb_id, $seasonNum);
                        } catch (\Exception $e) {
                            // ignore
                        }
                    }

                    if (!$seasonDetails) {
                        // Fallback mock episodes
                        $episodes = [
                            ['episode_number' => 1, 'name' => 'Episode 1: Awal Mula', 'overview' => 'Kisah awal perjalanan hidup yang penuh tantangan.'],
                            ['episode_number' => 2, 'name' => 'Episode 2: Konfrontasi', 'overview' => 'Pertemuan yang tak terduga antara dua musuh bebuyutan.'],
                            ['episode_number' => 3, 'name' => 'Episode 3: Kebenaran', 'overview' => 'Misteri perlahan mulai terungkap ke permukaan.'],
                            ['episode_number' => 4, 'name' => 'Episode 4: Keputusan', 'overview' => 'Langkah besar yang harus diambil demi masa depan.'],
                            ['episode_number' => 5, 'name' => 'Episode 5: Harapan Baru', 'overview' => 'Sebuah peluang baru muncul membawa secercah harapan.'],
                        ];
                    } else {
                        $episodes = $seasonDetails['episodes'] ?? [];
                    }

                    foreach ($episodes as $ep) {
                        if ((int)($ep['episode_number'] ?? 0) === $episodeNum) {
                            $episodeName = $ep['name'] ?? null;
                            $episodeOverview = $ep['overview'] ?? null;
                            break;
                        }
                    }
                }

                $continueWatching = [
                    'tmdb_id' => $latestProgress->tmdb_id,
                    'episode_id' => $latestProgress->episode_id,
                    'type' => $cwType,
                    'title' => $showDetails['title_english'] ?? $showDetails['title'] ?? 'K-Content',
                    'backdrop' => $showDetails['backdrop_path'] ?? null,
                    'overview' => $showDetails['overview'] ?? ($showDetails['synopsis_default'] ?? null),
                    'episode_name' => $episodeName,
                    'episode_overview' => $episodeOverview,
                    'last_position' => $latestProgress->last_position_seconds,
                    'last_position_formatted' => $this->formatSeconds($latestProgress->last_position_seconds),
                    'percentage' => min(95, max(5, round(($latestProgress->last_position_seconds / ($cwType === 'tv' ? 2700 : 5400)) * 100)))
                ];
            }
        }

        $myListIds = \App\Models\MyList::where('user_id', $userId)->pluck('tmdb_id')->toArray();

        // Fetch Recent Watch History records for the logged-in user
        $watchHistoryRecords = \App\Models\WatchHistory::where('user_id', $userId)
            ->orderByRaw('COALESCE(last_watched_at, watched_at, updated_at, created_at) DESC')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $watchHistoryList = [];

        if ($watchHistoryRecords->isNotEmpty()) {
            foreach ($watchHistoryRecords as $record) {
                $mType = $record->media_type ?? 'movie';
                $tmdbId = (int)$record->tmdb_id;
                $showDetails = null;

                if ($hasCredentials && !$isMocked) {
                    try {
                        $cacheKey = "tmdb_show_details_{$mType}_{$tmdbId}";
                        $showDetails = Cache::remember($cacheKey, now()->addDays(1), function () use ($mType, $tmdbId) {
                            return $this->tmdbService->getShowDetails($mType, $tmdbId);
                        });
                    } catch (\Exception $e) {
                        // ignore API error and fallback
                    }
                }

                if (!$showDetails) {
                    $showDetails = $this->getMockDetails($mType, $tmdbId);
                }

                if ($showDetails) {
                    $title = $showDetails['title'] ?? $showDetails['name'] ?? $showDetails['title_english'] ?? 'K-Content';
                    $posterPath = $showDetails['poster_path'] ?? null;
                    $voteAverage = $showDetails['vote_average'] ?? 8.0;
                    $releaseDate = $showDetails['release_date'] ?? $showDetails['first_air_date'] ?? '2024';

                    $lastWatched = $record->last_watched_at ?? $record->watched_at;
                    $formattedTime = $lastWatched ? $lastWatched->diffForHumans() : 'Terakhir Ditonton';

                    $watchHistoryList[] = [
                        'id' => $tmdbId,
                        'media_type' => $mType,
                        'title' => $title,
                        'poster_path' => $posterPath,
                        'vote_average' => $voteAverage,
                        'release_date' => $releaseDate,
                        'season_number' => $record->season_number,
                        'episode_number' => $record->episode_number,
                        'last_watched_formatted' => $formattedTime,
                        'genre_ids' => isset($showDetails['genres']) ? collect($showDetails['genres'])->pluck('id')->toArray() : ($showDetails['genre_ids'] ?? []),
                    ];
                }
            }
        }

        // Fallback sample watch history if history is empty (e.g. initial demo/guest user)
        if (empty($watchHistoryList)) {
            $sampleMocks = array_slice(array_merge($this->getMockPopularMovies(), $this->getMockTvShows(), $this->getMockTrending()), 0, 6);
            foreach ($sampleMocks as $idx => $sample) {
                $sType = $sample['media_type'] ?? (isset($sample['name']) ? 'tv' : 'movie');
                $watchHistoryList[] = [
                    'id' => $sample['id'],
                    'media_type' => $sType,
                    'title' => $sample['title'] ?? $sample['name'] ?? 'K-Content',
                    'poster_path' => $sample['poster_path'] ?? null,
                    'vote_average' => $sample['vote_average'] ?? 8.5,
                    'release_date' => $sample['release_date'] ?? $sample['first_air_date'] ?? '2024',
                    'season_number' => $sType === 'tv' ? 1 : null,
                    'episode_number' => $sType === 'tv' ? ($idx + 1) : null,
                    'last_watched_formatted' => ($idx + 1) . ' jam lalu',
                    'genre_ids' => $sample['genre_ids'] ?? [18],
                ];
            }
        }

        $genres = [
            ['id' => 'all', 'name' => 'Semua Genre', 'icon' => 'fa-border-all'],
            ['id' => 18, 'name' => 'Drama', 'icon' => 'fa-masks-theater'],
            ['id' => 10749, 'name' => 'Romantis', 'icon' => 'fa-heart'],
            ['id' => 28, 'name' => 'Aksi', 'icon' => 'fa-gun'],
            ['id' => 35, 'name' => 'Komedi', 'icon' => 'fa-face-laugh-beam'],
            ['id' => 9648, 'name' => 'Misteri & Thriller', 'icon' => 'fa-user-secret'],
            ['id' => 10765, 'name' => 'Fantasi', 'icon' => 'fa-wand-magic-sparkles'],
            ['id' => 27, 'name' => 'Horor', 'icon' => 'fa-ghost'],
        ];

        $thrillerHorrorList = $this->getCuratedThrillerHorror();
        $romanceList        = $this->getCuratedRomance();
        $mysteryList        = $this->getCuratedMystery();
        $comedyList         = $this->getCuratedComedy();
        $actionList         = $this->getCuratedAction();

        return view('dashboard', compact(
            'trendingList', 
            'moviesList', 
            'tvList', 
            'actorsList', 
            'heroItem', 
            'isMocked', 
            'continueWatching', 
            'myListIds',
            'popularTvList',
            'popularMoviesList',
            'genres',
            'watchHistoryList',
            'thrillerHorrorList',
            'romanceList',
            'mysteryList',
            'comedyList',
            'actionList'
        ));
    }

    /**
     * Get direct playback / resume URL for a show or episode.
     */
    public function getResumeUrl(Request $request, $type, $id)
    {
        $userId = Auth::id();

        if ($type === 'tv') {
            // 1. Check WatchHistory table
            $history = \App\Models\WatchHistory::where('user_id', $userId)
                ->where('tmdb_id', $id)
                ->where('media_type', 'tv')
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($history && $history->season_number && $history->episode_number) {
                return response()->json([
                    'success' => true,
                    'url' => route('tv.watch', [
                        'id' => $id,
                        'season' => $history->season_number,
                        'episode' => $history->episode_number
                    ])
                ]);
            }

            // 2. Check VideoProgress fallback
            $progress = \App\Models\VideoProgress::where('user_id', $userId)
                ->where('tmdb_id', $id)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($progress && $progress->episode_id) {
                $season = 1;
                $episode = 1;
                if (preg_match('/S(\d+)E(\d+)/i', $progress->episode_id, $matches)) {
                    $season = (int)$matches[1];
                    $episode = (int)$matches[2];
                } elseif (is_numeric($progress->episode_id)) {
                    $episode = (int)$progress->episode_id;
                }
                return response()->json([
                    'success' => true,
                    'url' => route('tv.watch', [
                        'id' => $id,
                        'season' => $season,
                        'episode' => $episode
                    ])
                ]);
            }

            // 3. Default to Episode 1 (Season 1 Episode 1)
            return response()->json([
                'success' => true,
                'url' => route('tv.watch', [
                    'id' => $id,
                    'season' => 1,
                    'episode' => 1
                ])
            ]);
        }

        // For Movies
        return response()->json([
            'success' => true,
            'url' => route('movies.show', ['id' => $id])
        ]);
    }

    /**
     * Display details for a selected movie or K-drama show, with cast and recommendations.
     *
     * @param string $type
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(string $type, int $id)
    {
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');

        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $details = [];
        $recommendations = [];
        $isMocked = false;

        $isCustomMockId = ($id >= 10000 && $id <= 10100);

        if ($hasCredentials && !$isCustomMockId) {
            try {
                // Fetch actual TMDB show details
                $details = $this->tmdbService->getShowDetails($type, $id);
                
                // Fetch recommendations from TMDB
                $recommendationsRaw = $this->tmdbService->getRecommendations($type, $id);
                $recommendationsList = $recommendationsRaw['results'] ?? [];

                // Filter for Korean recommendations only and format dates/ratings
                $recommendations = array_values(array_filter($recommendationsList, function ($item) {
                    return isset($item['original_language']) && $item['original_language'] === 'ko';
                }));
            } catch (\Exception $e) {
                $isMocked = true;
            }
        }

        // Trigger mock fallback if credentials not present, API call failed, or is custom mock ID
        if (!$hasCredentials || empty($details) || $isMocked || $isCustomMockId) {
            $isMocked = true;
            $details = $this->getMockDetails($type, $id);
            $recommendations = $this->getMockRecommendations($type, $id);
        }

        // Get view count for the show
        $viewsRecord = \App\Models\ViewCount::where('tmdb_id', $id)->first();
        $viewsCount = $viewsRecord ? $viewsRecord->views_count : 0;

        $inMyList = \App\Models\MyList::where('user_id', Auth::id())
            ->where('tmdb_id', $id)
            ->exists();

        $selectedSeasonNumber = (int) request()->get('season', 1);
        $seasonDetails = null;

        if ($type === 'tv') {
            if ($hasCredentials && !$isMocked) {
                try {
                    $seasonDetails = $this->tmdbService->getTvSeasonDetails($id, $selectedSeasonNumber);
                } catch (\Exception $e) {
                    // Ignore
                }
            }

            if (!$seasonDetails) {
                $seasonDetails = [
                    'name' => "Season {$selectedSeasonNumber}",
                    'episodes' => [
                        ['episode_number' => 1, 'name' => 'Episode 1: Awal Mula', 'overview' => 'Kisah awal perjalanan hidup yang penuh tantangan.', 'still_path' => null, 'runtime' => 60],
                        ['episode_number' => 2, 'name' => 'Episode 2: Konfrontasi', 'overview' => 'Pertemuan yang tak terduga antara dua musuh bebuyutan.', 'still_path' => null, 'runtime' => 58],
                        ['episode_number' => 3, 'name' => 'Episode 3: Kebenaran', 'overview' => 'Misteri perlahan mulai terungkap ke permukaan.', 'still_path' => null, 'runtime' => 65],
                        ['episode_number' => 4, 'name' => 'Episode 4: Keputusan', 'overview' => 'Langkah besar yang harus diambil demi masa depan.', 'still_path' => null, 'runtime' => 60],
                    ]
                ];
            }
        }

        return view('show', compact('details', 'recommendations', 'isMocked', 'viewsCount', 'inMyList', 'seasonDetails', 'selectedSeasonNumber'));
    }

    /**
     * Fetch mock details for show detail page
     */
    public function getMockDetails(string $type, int $id): array
    {
        $allMocks = array_merge(
            $this->getMockMovies(), 
            $this->getMockTvShows(),
            $this->getMockPopularMovies(),
            $this->getMockPopularTvShows(),
            $this->getCuratedThrillerHorror(),
            $this->getCuratedRomance(),
            $this->getCuratedMystery(),
            $this->getCuratedComedy(),
            $this->getCuratedAction()
        );
        foreach ($allMocks as $mock) {
            if ($mock['id'] == $id) {
                $mock['cast'] = $this->getMockCastForShow($id);
                $mock['title_english'] = $mock['title'] ?? $mock['name'] ?? 'K-Content';
                $mock['title_original'] = $mock['title'] ?? $mock['name'] ?? 'K-Content';
                $mock['synopsis_default'] = $mock['overview'] ?? 'Detail plot drama/film ini menyajikan kisah menarik khas perfilman Korea.';
                
                if (!empty($mock['genre_ids'])) {
                    $genreMap = [
                        18 => 'Drama',
                        10749 => 'Romantis',
                        28 => 'Aksi',
                        35 => 'Komedi',
                        9648 => 'Misteri',
                        10765 => 'Fantasi',
                        27 => 'Horor'
                    ];
                    $mock['genres'] = array_map(function($gId) use ($genreMap) {
                        return ['id' => $gId, 'name' => $genreMap[$gId] ?? 'Drama'];
                    }, $mock['genre_ids']);
                } else {
                    $mock['genres'] = [
                        ['id' => 18, 'name' => 'Drama'],
                        ['id' => 9648, 'name' => 'Misteri']
                    ];
                }

                $mock['rating'] = $mock['vote_average'] ?? 8.5;
                $mock['media_type'] = $type;
                $mock['kdrama_status'] = $mock['kdrama_status'] ?? 'Completed';
                
                if (empty($mock['backdrop_path']) || str_contains($mock['backdrop_path'], 'unsplash')) {
                    $mock['backdrop_path'] = $mock['poster_path'] ?? 'https://image.tmdb.org/t/p/w500/1QdXdRYfktUSONkl1oD5gc6Be0s.jpg';
                }

                return $mock;
            }
        }

        return [
            'id' => $id,
            'media_type' => $type,
            'title_english' => 'K-Content Pilihan',
            'title_original' => 'Korean Drama / Movie',
            'synopsis_default' => 'Detail plot film ini sedang dimuat secara dinamis dari TMDB API.',
            'poster_path' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400',
            'backdrop_path' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=1200',
            'rating' => 8.2,
            'release_date' => '2024-06-01',
            'genres' => [['id' => 18, 'name' => 'Drama']],
            'kdrama_status' => 'Ongoing',
            'cast' => [
                ['name' => 'Aktor Utama', 'character' => 'Pemeran', 'profile_path' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=400']
            ]
        ];
    }

    /**
     * Fetch mock recommendations for show detail page
     */
    private function getMockRecommendations(string $type, int $id): array
    {
        $allMocks = array_merge(
            $this->getMockMovies(), 
            $this->getMockTvShows(),
            $this->getCuratedThrillerHorror(),
            $this->getCuratedRomance(),
            $this->getCuratedMystery(),
            $this->getCuratedComedy(),
            $this->getCuratedAction()
        );
        return array_values(array_filter($allMocks, function ($item) use ($id) {
            return $item['id'] !== $id;
        }));
    }

    /**
     * Fallback data for Trending K-Content
     */
    private function getMockTrending(): array
    {
        return [
            [
                'id' => 10001,
                'title' => 'Squid Game (Season 2)',
                'media_type' => 'tv',
                'overview' => 'Ratusan pemain yang terlilit utang menerima undangan aneh untuk berkompetisi dalam permainan anak-anak dengan hadiah menggiurkan senilai 45,6 Miliar Won, namun taruhannya adalah nyawa mereka sendiri.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400',
                'vote_average' => 8.8,
                'release_date' => '2024-12-26',
                'original_language' => 'ko',
                'genre_ids' => [18, 9648, 28]
            ],
            [
                'id' => 10002,
                'title' => 'My Demon',
                'media_type' => 'tv',
                'overview' => 'Iblis kejam kehilangan kekuatannya setelah terikat kontrak pernikahan dengan Do Do-hee, pewaris dingin yang memegang kunci kekuatannya yang hilang.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400',
                'vote_average' => 8.5,
                'release_date' => '2024-01-20',
                'original_language' => 'ko',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10003,
                'title' => 'Exhuma',
                'media_type' => 'movie',
                'overview' => 'Proses penggalian kuburan leluhur kaya raya melepas entitas jahat yang terkubur di bawah tanah keramat.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
                'vote_average' => 8.2,
                'release_date' => '2024-02-22',
                'original_language' => 'ko',
                'genre_ids' => [27, 9648]
            ],
            [
                'id' => 10007,
                'title' => 'Queen of Tears',
                'media_type' => 'tv',
                'overview' => 'Krisis pernikahan dan kembalinya cinta antara pewaris Queens Group dan anak kepala desa.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400',
                'vote_average' => 9.0,
                'release_date' => '2024-03-09',
                'original_language' => 'ko',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10008,
                'title' => 'Lovely Runner',
                'media_type' => 'tv',
                'overview' => 'Perjalanan waktu melintasi takdir demi menyelamatkan idol favorit dari nasib tragis.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400',
                'vote_average' => 8.9,
                'release_date' => '2024-04-08',
                'original_language' => 'ko',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10004,
                'title' => 'The Roundup: Punishment',
                'media_type' => 'movie',
                'overview' => 'Detektif tangguh Ma Seok-do bergabung dengan Tim Investigasi Cyber untuk membongkar sindikat judi online ilegal terbesar.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=400',
                'vote_average' => 7.9,
                'release_date' => '2024-04-24',
                'original_language' => 'ko',
                'genre_ids' => [28, 35]
            ],
            [
                'id' => 10005,
                'title' => 'Officer Black Belt',
                'media_type' => 'movie',
                'overview' => 'Seorang ahli bela diri berbakat bekerjasama dengan petugas percobaan untuk melawan dan mencegah kejahatan di jalanan Seoul.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=400',
                'vote_average' => 8.1,
                'release_date' => '2024-09-13',
                'original_language' => 'ko',
                'genre_ids' => [28, 35]
            ],
            [
                'id' => 10009,
                'title' => 'Gyeongseong Creature',
                'media_type' => 'tv',
                'overview' => 'Pada musim semi tahun 1945 di Gyeongseong, dua orang dewasa muda menghadapi monster yang lahir dari keserakahan manusia.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400',
                'vote_average' => 8.4,
                'release_date' => '2023-12-22',
                'original_language' => 'ko',
                'genre_ids' => [18, 27, 9648]
            ],
            [
                'id' => 10006,
                'title' => 'Uprising',
                'media_type' => 'movie',
                'overview' => 'Di era Dinasti Joseon, dua teman masa kecil yang terpisah oleh kelas sosial bersatu kembali sebagai musuh dalam perang saudara.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1599837565318-67429bde7162?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1599837565318-67429bde7162?q=80&w=400',
                'vote_average' => 8.0,
                'release_date' => '2024-10-11',
                'original_language' => 'ko',
                'genre_ids' => [28, 18]
            ],
            [
                'id' => 10010,
                'title' => 'Goblin (Guardian)',
                'media_type' => 'tv',
                'overview' => 'Pelindung jiwa abadi mencari pengantin manusia untuk mengakhiri kehidupan abadinya yang penuh kutukan.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400',
                'vote_average' => 9.1,
                'release_date' => '2016-12-02',
                'original_language' => 'ko',
                'genre_ids' => [18, 10749, 10765]
            ]
        ];
    }

    /**
     * Fallback data for Korean Movies (2024-2025)
     */
    private function getMockMovies(): array
    {
        return [
            [
                'id' => 10003,
                'title' => 'Exhuma',
                'overview' => 'Proses penggalian kuburan leluhur kaya raya melepas entitas jahat yang terkubur di bawah tanah.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/6dasJ58GGFcC62H9KuukAryltUp.jpg',
                'vote_average' => 8.2,
                'release_date' => '2024-02-22'
            ],
            [
                'id' => 10004,
                'title' => 'The Roundup: Punishment',
                'overview' => 'Detektif tangguh Ma Seok-do bergabung dengan Tim Investigasi Cyber untuk membongkar sindikat judi online ilegal terbesar.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/kfdVbOzwsKy65eaizDnBfxAJ95p.jpg',
                'vote_average' => 7.9,
                'release_date' => '2024-04-24'
            ],
            [
                'id' => 10005,
                'title' => 'Officer Black Belt',
                'overview' => 'Seorang ahli bela diri berbakat bekerjasama dengan petugas percobaan untuk melawan dan mencegah kejahatan di jalanan Seoul.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/pWOiCearczq7zmWo4ASbUIF6HYS.jpg',
                'vote_average' => 8.1,
                'release_date' => '2024-09-13'
            ],
            [
                'id' => 10006,
                'title' => 'Uprising',
                'overview' => 'Di era Dinasti Joseon, dua teman masa kecil yang terpisah oleh kelas sosial bersatu kembali sebagai musuh dalam perang saudara.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1599837565318-67429bde7162?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/zeSO9ZDWaDuG3nTcijaoJ2V4UWU.jpg',
                'vote_average' => 8.0,
                'release_date' => '2024-10-11'
            ]
        ];
    }

    /**
     * Fallback data for K-Dramas (Ongoing & Completed)
     */
    private function getMockTvShows(): array
    {
        return [
            [
                'id' => 10001,
                'name' => 'Squid Game (Season 2)',
                'overview' => 'Ratusan pemain berlomba hidup-mati untuk mendapatkan hadiah utama senilai 45,6 miliar Won.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/1QdXdRYfktUSONkl1oD5gc6Be0s.jpg',
                'vote_average' => 8.8,
                'first_air_date' => '2024-12-26',
                'kdrama_status' => 'Ongoing',
                'media_type' => 'tv',
                'genre_ids' => [18, 9648, 28]
            ],
            [
                'id' => 10002,
                'name' => 'My Demon',
                'overview' => 'Pernikahan kontrak antara pewaris chaebol dingin dan iblis tampan yang kehilangan kekuatannya.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/xBnscv5BrJREKVSvh0le61y4KDk.jpg',
                'vote_average' => 8.5,
                'first_air_date' => '2024-01-20',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10007,
                'name' => 'Queen of Tears',
                'overview' => 'Krisis pernikahan dan kembalinya cinta ajaib antara pewaris Queens Group dan anak kepala desa.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/7ZXLZ3KYL3IVvsSHBZaHjcNQzNU.jpg',
                'vote_average' => 9.0,
                'first_air_date' => '2024-03-09',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10008,
                'name' => 'Lovely Runner',
                'overview' => 'Perjalanan waktu melintasi takdir demi menyelamatkan idol favorit dari nasib tragis di masa lalu.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/adcdNzLJ8LOjWJjNFrapXGzFco3.jpg',
                'vote_average' => 8.9,
                'first_air_date' => '2024-04-08',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10090,
                'name' => 'Marry My Husband',
                'overview' => 'Wanita yang dikhianati dan dibunuh mendapat kesempatan kedua hidup 10 tahun lalu untuk membalas dendam.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/899KcBqooj8nEyPcAEU3h7AdfUo.jpg',
                'vote_average' => 8.8,
                'first_air_date' => '2024-01-01',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10073,
                'name' => 'Love Next Door',
                'overview' => 'Pertemuan kembali dua teman masa kecil yang mengetahui rahasia kelam dan masa lalu satu sama lain.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/hikbLeofw2epfaEJptSkQ6b22IV.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2024-08-17',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10079,
                'name' => 'A Shop for Killers',
                'overview' => 'Mahasiswi menjadi target pembunuh bayaran misterius setelah mengedit warisan pusat perbelanjaan pamannya.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/7yUY1HUyQuybbvkAAhLzQ7x1l9g.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2024-01-17',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [28, 9648, 18]
            ],
            [
                'id' => 10024,
                'name' => 'Gyeongseong Creature',
                'overview' => 'Di Seoul tahun 1945, seorang pengusaha kaya dan pencari jejak menghadapi monster hasil eksperimen manusia.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/5wliFAD8Pjjj0YYSmupqjOldnWt.jpg',
                'vote_average' => 8.4,
                'first_air_date' => '2024-01-05',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 27, 9648]
            ],
            [
                'id' => 10033,
                'name' => 'Doctor Slump',
                'overview' => 'Dua rival sekolah yang sama-sama mengalami titik terendah dalam karir medis bertemu dan saling menyembuhkan.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/iOWmbZEbhvrYyWm6O4W3oHf2S9B.jpg',
                'vote_average' => 8.3,
                'first_air_date' => '2024-01-27',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10084,
                'name' => 'The Judge from Hell',
                'overview' => 'Iblis dari neraka merasuki tubuh seorang hakim elit untuk menghukum para penjahat yang tidak bertobat.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/9vhLHbUiiP9HiXfJw5OUC7KoaJG.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2024-09-21',
                'kdrama_status' => 'Ongoing',
                'media_type' => 'tv',
                'genre_ids' => [10765, 9648, 18]
            ],
            [
                'id' => 10085,
                'name' => 'Jeongnyeon: The Star is Born',
                'overview' => 'Kisah emosional gadis bertalenta dari Mokpo yang bercita-cita menjadi aktris opera tradisional wanita terbaik.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/6Iyr6TCx477b8HSgG0fXF4hSuIa.jpg',
                'vote_average' => 8.8,
                'first_air_date' => '2024-10-12',
                'kdrama_status' => 'Ongoing',
                'media_type' => 'tv',
                'genre_ids' => [18, 35]
            ],
            [
                'id' => 10088,
                'name' => 'Flex X Cop',
                'overview' => 'Anak chaebol generasi ketiga yang tidak dewasa menjadi detektif dan menggunakan kekayaannya untuk menangkap penjahat.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/2eWH2RDxpbIfEs17BpJTQhcOqRs.jpg',
                'vote_average' => 8.4,
                'first_air_date' => '2024-01-26',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [28, 35, 9648]
            ],
            [
                'id' => 10089,
                'name' => 'Pyramid Game',
                'overview' => 'Di SMA khusus wanita, setiap bulan diadakan pemungutan suara rahasia yang menentukan kasta sosial dan korban perundungan.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/pBUERwWNCuO36CPxiVFsoQCPu7W.jpg',
                'vote_average' => 8.5,
                'first_air_date' => '2024-02-29',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 9648]
            ],
            [
                'id' => 10067,
                'name' => 'Welcome to Samdal-ri',
                'overview' => 'Fotografer terkenal yang kehilangan segalanya pulang ke kampung halamannya di Jeju dan bertemu cinta lamanya.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/98IvA2i0PsTY8CThoHByCKOEAjz.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2024-01-21',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10086,
                'name' => 'The Fiery Priest 2',
                'overview' => 'Pastor pemarah berpikiran tajam kembali bekerjasama dengan para detektif untuk menuntaskan kejahatan kartel narkoba.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1200',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/5EDJlbB3S1vwOpsR3h1k0NMHiHY.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2024-11-08',
                'kdrama_status' => 'Ongoing',
                'media_type' => 'tv',
                'genre_ids' => [28, 35, 18]
            ]
        ];
    }

    private function getMockActors(): array
    {
        return [
            [
                'id' => 1251581,
                'name' => 'Kim Soo-hyun',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/q24P4pmtWGhe08T7rTkoDc5EC1p.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10007, 'title' => 'Queen of Tears', 'media_type' => 'tv', 'character' => 'Baek Hyun-woo', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10045, 'title' => "It's Okay to Not Be Okay", 'media_type' => 'tv', 'character' => 'Moon Gang-tae', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10050, 'title' => 'My Love from the Star', 'media_type' => 'tv', 'character' => 'Do Min-joon', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 582130,
                'name' => 'Kim Ji-won',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/lX7W1j9kg4jV6XNn5XEE3rKsd3x.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10007, 'title' => 'Queen of Tears', 'media_type' => 'tv', 'character' => 'Hong Hae-in', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10015, 'title' => 'Lovestruck in the City', 'media_type' => 'tv', 'character' => 'Lee Eun-o', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10016, 'title' => 'Fight For My Way', 'media_type' => 'tv', 'character' => 'Choi Ae-ra', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 74421,
                'name' => 'Song Hye-kyo',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/tlAX3f82Mf5h0rznpVBVK7nD2om.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10012, 'title' => 'The Glory', 'media_type' => 'tv', 'character' => 'Moon Dong-eun', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=400'],
                    ['id' => 10011, 'title' => 'Descendants of the Sun', 'media_type' => 'tv', 'character' => 'Kang Mo-yeon', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=400'],
                    ['id' => 10017, 'title' => 'Autumn in My Heart', 'media_type' => 'tv', 'character' => 'Yoon Eun-suh', 'popularity' => 90, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400']
                ]
            ],
            [
                'id' => 86889,
                'name' => 'Son Ye-jin',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/i6fASFvO3mUceZJvipn4RiLHA44.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10010, 'title' => 'Crash Landing on You', 'media_type' => 'tv', 'character' => 'Yoon Se-ri', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?q=80&w=400'],
                    ['id' => 10018, 'title' => 'Something in the Rain', 'media_type' => 'tv', 'character' => 'Yoon Jin-ah', 'popularity' => 90, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10019, 'title' => 'Thirty-Nine', 'media_type' => 'tv', 'character' => 'Cha Mi-jo', 'popularity' => 85, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1252318,
                'name' => 'IU (Lee Ji-eun)',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/uNhKOO9lIAFXF11LM6gjCrX2CJz.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10020, 'title' => 'Hotel Del Luna', 'media_type' => 'tv', 'character' => 'Jang Man-wol', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10021, 'title' => 'My Mister', 'media_type' => 'tv', 'character' => 'Lee Ji-an', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10022, 'title' => 'Broker', 'media_type' => 'movie', 'character' => 'So-young', 'popularity' => 91, 'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400']
                ]
            ],
            [
                'id' => 1067849,
                'name' => 'Kim Go-eun',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/p7wuD6eX0YcZ5LXWhx0XjvEYZKz.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10003, 'title' => 'Exhuma', 'media_type' => 'movie', 'character' => 'Lee Hwa-rim', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400'],
                    ['id' => 10009, 'title' => 'Goblin', 'media_type' => 'tv', 'character' => 'Ji Eun-tak', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400'],
                    ['id' => 10023, 'title' => 'Little Women', 'media_type' => 'tv', 'character' => 'Oh In-joo', 'popularity' => 92, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400']
                ]
            ],
            [
                'id' => 2112859,
                'name' => 'Han So-hee',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/yTp8dDqsufHzup74gV0fzOBOLqB.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10024, 'title' => 'Gyeongseong Creature', 'media_type' => 'tv', 'character' => 'Yoon Chae-ok', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10025, 'title' => 'My Name', 'media_type' => 'tv', 'character' => 'Yoon Ji-woo', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400'],
                    ['id' => 10026, 'title' => 'The World of the Married', 'media_type' => 'tv', 'character' => 'Yeo Da-kyung', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400']
                ]
            ],
            [
                'id' => 1014784,
                'name' => 'Bae Suzy',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/39x6Dc4ELZxHbxCuOM0ddbwKh3F.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10027, 'title' => 'Doona!', 'media_type' => 'tv', 'character' => 'Lee Doo-na', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10028, 'title' => 'Start-Up', 'media_type' => 'tv', 'character' => 'Seo Dal-mi', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10029, 'title' => 'Vagabond', 'media_type' => 'tv', 'character' => 'Go Hae-ri', 'popularity' => 91, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1252045,
                'name' => 'Lim Yoon-a (Yoona)',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/JzAuAv17bWkLxVtfi9YJvQJhZl.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10030, 'title' => 'King the Land', 'media_type' => 'tv', 'character' => 'Cheon Sa-rang', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10031, 'title' => 'Big Mouth', 'media_type' => 'tv', 'character' => 'Go Mi-ho', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10032, 'title' => 'Confidential Assignment', 'media_type' => 'movie', 'character' => 'Park Min-young', 'popularity' => 91, 'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400']
                ]
            ],
            [
                'id' => 1156197,
                'name' => 'Park Shin-hye',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/5wyerOQUW0Ej7Xs8eqfwdoJgV7E.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10033, 'title' => 'Doctor Slump', 'media_type' => 'tv', 'character' => 'Nam Ha-neul', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10034, 'title' => 'The Heirs', 'media_type' => 'tv', 'character' => 'Cha Eun-sang', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10035, 'title' => 'Pinocchio', 'media_type' => 'tv', 'character' => 'Choi In-ha', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1134684,
                'name' => 'Park Eun-bin',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/2PqeVq0KV1pEUgeeSquk2ljfMR0.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10036, 'title' => 'Extraordinary Attorney Woo', 'media_type' => 'tv', 'character' => 'Woo Young-woo', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10037, 'title' => 'Castaway Diva', 'media_type' => 'tv', 'character' => 'Seo Mok-ha', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10038, 'title' => "The King's Affection", 'media_type' => 'tv', 'character' => 'Yi Hwi', 'popularity' => 92, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 116175,
                'name' => 'Shin Min-a',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/6CjxjYvZBuvbAQTcuoPb8EeVrUI.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10039, 'title' => 'Hometown Cha-Cha-Cha', 'media_type' => 'tv', 'character' => 'Yoon Hye-jin', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10040, 'title' => 'Our Blues', 'media_type' => 'tv', 'character' => 'Min Seon-a', 'popularity' => 92, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10041, 'title' => 'My Girlfriend is a Gumiho', 'media_type' => 'tv', 'character' => 'Gu Mi-ho', 'popularity' => 90, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1537768,
                'name' => 'Kim Tae-ri',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/gFofVUeVlIvBJMUv7maHQwWdfsk.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10042, 'title' => 'Twenty-Five Twenty-One', 'media_type' => 'tv', 'character' => 'Na Hee-do', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10043, 'title' => 'Revenant', 'media_type' => 'tv', 'character' => 'Gu San-yeong', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10044, 'title' => 'Mr. Sunshine', 'media_type' => 'tv', 'character' => 'Go Ae-shin', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1353829,
                'name' => 'Seo Yea-ji',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/a9moiE8WWuygU02m4KE3M2Nrrkp.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10045, 'title' => "It's Okay to Not Be Okay", 'media_type' => 'tv', 'character' => 'Ko Moon-young', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10046, 'title' => 'Eve', 'media_type' => 'tv', 'character' => 'Lee La-el', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10047, 'title' => 'Lawless Lawyer', 'media_type' => 'tv', 'character' => 'Ha Jae-yi', 'popularity' => 90, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 140335,
                'name' => 'Kim Yoo-jung',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/eFcMTDIgzSGLHZYXcsf1M42cvFt.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10002, 'title' => 'My Demon', 'media_type' => 'tv', 'character' => 'Do Do-hee', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10048, 'title' => '20th Century Girl', 'media_type' => 'movie', 'character' => 'Na Bo-ra', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400'],
                    ['id' => 10049, 'title' => 'Love in the Moonlight', 'media_type' => 'tv', 'character' => 'Hong Ra-on', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400']
                ]
            ],
            [
                'id' => 63436,
                'name' => 'Jun Ji-hyun (Gianna Jun)',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/qejOQBdIzN18e69yRcsiD0JQi4c.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10050, 'title' => 'My Love from the Star', 'media_type' => 'tv', 'character' => 'Cheon Song-yi', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400'],
                    ['id' => 10051, 'title' => 'Legend of the Blue Sea', 'media_type' => 'tv', 'character' => 'Shim Cheong', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10052, 'title' => 'Kingdom: Ashin of the North', 'media_type' => 'tv', 'character' => 'Ashin', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400']
                ]
            ],
            [
                'id' => 1252016,
                'name' => 'Moon Ga-young',
                'gender' => 1,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/sHOzgv3dvHZSDi5ijj5vbYYJwZ4.jpg',
                'known_for_department' => 'Aktris',
                'credits' => [
                    ['id' => 10053, 'title' => 'True Beauty', 'media_type' => 'tv', 'character' => 'Lim Ju-kyung', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10054, 'title' => 'The Interest of Love', 'media_type' => 'tv', 'character' => 'Ahn Su-yeong', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10055, 'title' => 'Find Me in Your Memory', 'media_type' => 'tv', 'character' => 'Yeo Ha-jin', 'popularity' => 90, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1347525,
                'name' => 'Park Seo-jun',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/k1ALgZkOApYt7PIUBkUitmknXQC.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10056, 'title' => 'Itaewon Class', 'media_type' => 'tv', 'character' => 'Park Sae-ro-yi', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10057, 'title' => "What's Wrong with Secretary Kim", 'media_type' => 'tv', 'character' => 'Lee Young-joon', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10016, 'title' => 'Fight For My Way', 'media_type' => 'tv', 'character' => 'Ko Dong-man', 'popularity' => 92, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1245104,
                'name' => 'Lee Min-ho',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/iqopuz6cKuRZRUPZQrj7lFZcWWb.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10058, 'title' => 'Boys Over Flowers', 'media_type' => 'tv', 'character' => 'Gu Jun-pyo', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10059, 'title' => 'Pachinko', 'media_type' => 'tv', 'character' => 'Koh Hansu', 'popularity' => 91, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10060, 'title' => 'The King: Eternal Monarch', 'media_type' => 'tv', 'character' => 'Lee Gon', 'popularity' => 89, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 150903,
                'name' => 'Gong Yoo',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/ocGoFb6TrK3uWGXt4WnuibUG1vD.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10009, 'title' => 'Goblin', 'media_type' => 'tv', 'character' => 'Kim Shin (Goblin)', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400'],
                    ['id' => 10014, 'title' => 'Train to Busan', 'media_type' => 'movie', 'character' => 'Seok-woo', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?q=80&w=400'],
                    ['id' => 10001, 'title' => 'Squid Game', 'media_type' => 'tv', 'character' => 'The Salesman', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400']
                ]
            ],
            [
                'id' => 2117890,
                'name' => 'Byeon Woo-seok',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/iACGQCFJZUMsy4ywEzNiPokXFB9.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10008, 'title' => 'Lovely Runner', 'media_type' => 'tv', 'character' => 'Ryu Sun-jae', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400'],
                    ['id' => 10048, 'title' => '20th Century Girl', 'media_type' => 'movie', 'character' => 'Poong Woon-ho', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400'],
                    ['id' => 10061, 'title' => 'Strong Girl Nam-soon', 'media_type' => 'tv', 'character' => 'Ryu Shi-o', 'popularity' => 90, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400']
                ]
            ],
            [
                'id' => 587634,
                'name' => 'Park Bo-gum',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/iQHhzpJnxPAeZetNLJPr3gTs1rE.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10062, 'title' => 'Reply 1988', 'media_type' => 'tv', 'character' => 'Choi Taek', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10063, 'title' => 'Encounter', 'media_type' => 'tv', 'character' => 'Kim Jin-hyuk', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10064, 'title' => 'Record of Youth', 'media_type' => 'tv', 'character' => 'Sa Hye-jun', 'popularity' => 92, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1878952,
                'name' => 'Song Kang',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/83fLAMMb1LGT8YZ4dgRI0fti3az.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10002, 'title' => 'My Demon', 'media_type' => 'tv', 'character' => 'Jung Gu-won', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10065, 'title' => 'Sweet Home', 'media_type' => 'tv', 'character' => 'Cha Hyun-soo', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400'],
                    ['id' => 10066, 'title' => 'Nevertheless', 'media_type' => 'tv', 'character' => 'Park Jae-eon', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400']
                ]
            ],
            [
                'id' => 1253391,
                'name' => 'Ji Chang-wook',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/sBmHrO5Tn27Ot5hy0yAKniROmNb.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10067, 'title' => 'Welcome to Samdal-ri', 'media_type' => 'tv', 'character' => 'Cho Yong-pil', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10068, 'title' => 'The Worst of Evil', 'media_type' => 'tv', 'character' => 'Park Jun-mo', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10069, 'title' => 'Healer', 'media_type' => 'tv', 'character' => 'Seo Jung-hoo', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1095818,
                'name' => 'Lee Jong-suk',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/eW73DbmKQrqb6xDC52oMbVehw6G.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10031, 'title' => 'Big Mouth', 'media_type' => 'tv', 'character' => 'Park Chang-ho', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10070, 'title' => 'W: Two Worlds', 'media_type' => 'tv', 'character' => 'Kang Chul', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10071, 'title' => 'While You Were Sleeping', 'media_type' => 'tv', 'character' => 'Jung Jae-chan', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1459772,
                'name' => 'Nam Joo-hyuk',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/kuLC8pR24kbEIAjfg3WpKW41Kcl.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10042, 'title' => 'Twenty-Five Twenty-One', 'media_type' => 'tv', 'character' => 'Back Yi-jin', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10028, 'title' => 'Start-Up', 'media_type' => 'tv', 'character' => 'Nam Do-san', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10072, 'title' => 'Weightlifting Fairy Kim Bok-joo', 'media_type' => 'tv', 'character' => 'Jung Joon-hyung', 'popularity' => 93, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1470763,
                'name' => 'Jung Hae-in',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/7ZMUHR2q0XTWMxuqijWSVfWQv14.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10073, 'title' => 'Love Next Door', 'media_type' => 'tv', 'character' => 'Choi Seung-hyo', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10074, 'title' => 'D.P.', 'media_type' => 'tv', 'character' => 'An Jun-ho', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10075, 'title' => 'Snowdrop', 'media_type' => 'tv', 'character' => 'Lim Soo-ho', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1571598,
                'name' => 'Ahn Hyo-seop',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/oLS5jvS5cZ7SaXNctA8VYXPp6ax.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10076, 'title' => 'Business Proposal', 'media_type' => 'tv', 'character' => 'Kang Tae-moo', 'popularity' => 99, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10077, 'title' => 'A Time Called You', 'media_type' => 'tv', 'character' => 'Koo Yeon-jun', 'popularity' => 95, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10078, 'title' => 'Dr. Romantic', 'media_type' => 'tv', 'character' => 'Seo Woo-jin', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1238592,
                'name' => 'Lee Dong-wook',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/khwUahjzIHFlrxYeKQAWrbWqsL6.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10079, 'title' => 'A Shop for Killers', 'media_type' => 'tv', 'character' => 'Jeong Jin-man', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10080, 'title' => 'Tale of the Nine Tailed', 'media_type' => 'tv', 'character' => 'Lee Yeon', 'popularity' => 97, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10009, 'title' => 'Goblin', 'media_type' => 'tv', 'character' => 'Grim Reaper', 'popularity' => 96, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ],
            [
                'id' => 1613836,
                'name' => 'Rowoon',
                'gender' => 2,
                'profile_path' => 'https://image.tmdb.org/t/p/w500/sWf7wAG4upRgIkWuDfNvUog1UHc.jpg',
                'known_for_department' => 'Aktor',
                'credits' => [
                    ['id' => 10081, 'title' => 'Destined With You', 'media_type' => 'tv', 'character' => 'Jang Shin-yu', 'popularity' => 98, 'poster_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400'],
                    ['id' => 10082, 'title' => 'The Matchmakers', 'media_type' => 'tv', 'character' => 'Sim Jung-woo', 'popularity' => 94, 'poster_path' => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'],
                    ['id' => 10083, 'title' => 'Extraordinary You', 'media_type' => 'tv', 'character' => 'Haru', 'popularity' => 92, 'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400']
                ]
            ]
        ];
    }

    /**
     * Format seconds to mm:ss format.
     */
    private function formatSeconds($seconds)
    {
        $m = floor($seconds / 60);
        $s = $seconds % 60;
        return "{$m}m " . ($s < 10 ? '0' : '') . "{$s}s";
    }

    /**
     * Display dramas (TV Shows) page.
     */
    public function dramas(Request $request)
    {
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');
        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $tvList = [];
        $isMocked = false;

        if ($hasCredentials) {
            try {
                $tv = $this->tmdbService->getKoreanContent('tv', 1);
                $tvListRaw = $tv['results'] ?? [];

                foreach ($tvListRaw as $item) {
                    $showId = $item['id'];
                    $kdramaStatus = Cache::remember("tmdb_tv_status_{$showId}", now()->addDays(3), function () use ($showId) {
                        $details = $this->tmdbService->getShowDetails('tv', $showId);
                        return $details['kdrama_status'] ?? 'Ongoing';
                    });
                    $item['kdrama_status'] = $kdramaStatus;
                    $tvList[] = $item;
                }
            } catch (\Exception $e) {
                $isMocked = true;
            }
        }

        if (!$hasCredentials || empty($tvList) || $isMocked) {
            $isMocked = true;
            $tvList = $this->getMockTvShows();
        }

        $myListIds = \App\Models\MyList::where('user_id', Auth::id())->pluck('tmdb_id')->toArray();

        return view('dramas', compact('tvList', 'isMocked', 'myListIds'));
    }

    /**
     * Display movies page.
     */
    public function movies(Request $request)
    {
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');
        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $moviesList = [];
        $isMocked = false;

        if ($hasCredentials) {
            try {
                $movies = $this->tmdbService->getKoreanContent('movie', 1);
                $moviesList = $movies['results'] ?? [];
            } catch (\Exception $e) {
                $isMocked = true;
            }
        }

        if (!$hasCredentials || empty($moviesList) || $isMocked) {
            $isMocked = true;
            $moviesList = $this->getMockMovies();
        }

        $myListIds = \App\Models\MyList::where('user_id', Auth::id())->pluck('tmdb_id')->toArray();

        return view('movies', compact('moviesList', 'isMocked', 'myListIds'));
    }

    /**
     * Display dedicated Genres page with rich interactive filters.
     */
    public function genres(Request $request)
    {
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');
        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $isMocked = false;
        $allContent = [];

        if ($hasCredentials) {
            try {
                $trending = $this->tmdbService->getTrendingNow(1, true);
                $tv = $this->tmdbService->getKoreanContent('tv', 1);
                $movies = $this->tmdbService->getKoreanContent('movie', 1);

                $tResults = $trending['results'] ?? [];
                $tvResults = $tv['results'] ?? [];
                $mResults = $movies['results'] ?? [];

                foreach ($tResults as &$t) { $t['media_type'] = $t['media_type'] ?? 'tv'; } unset($t);
                foreach ($tvResults as &$t) { $t['media_type'] = 'tv'; } unset($t);
                foreach ($mResults as &$m) { $m['media_type'] = 'movie'; } unset($m);

                $allContent = array_merge($tResults, $tvResults, $mResults);
            } catch (\Exception $e) {
                $isMocked = true;
            }
        }

        if (!$hasCredentials || empty($allContent) || $isMocked) {
            $isMocked = true;
            $allContent = array_merge(
                $this->getMockTrending(),
                $this->getMockTvShows(),
                $this->getMockMovies()
            );
        }

        // Deduplicate items by ID
        $uniqueContent = [];
        $seen = [];
        foreach ($allContent as $item) {
            if (!in_array($item['id'], $seen)) {
                $seen[] = $item['id'];
                if (!isset($item['genre_ids'])) {
                    $item['genre_ids'] = [18, 10749]; // default fallback
                }
                $uniqueContent[] = $item;
            }
        }

        $genres = [
            ['id' => 'all', 'name' => 'Semua Genre', 'icon' => 'fa-border-all', 'gradient' => 'linear-gradient(135deg, #ff2a54 0%, #ff6b3d 100%)'],
            ['id' => 10749, 'name' => 'Romantis', 'icon' => 'fa-heart', 'gradient' => 'linear-gradient(135deg, #ec4899 0%, #f43f5e 100%)'],
            ['id' => 18, 'name' => 'Drama', 'icon' => 'fa-masks-theater', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)'],
            ['id' => 28, 'name' => 'Aksi', 'icon' => 'fa-gun', 'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)'],
            ['id' => 35, 'name' => 'Komedi', 'icon' => 'fa-face-laugh-beam', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)'],
            ['id' => 9648, 'name' => 'Misteri & Thriller', 'icon' => 'fa-user-secret', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)'],
            ['id' => 10765, 'name' => 'Fantasi', 'icon' => 'fa-wand-magic-sparkles', 'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)'],
            ['id' => 27, 'name' => 'Horor', 'icon' => 'fa-ghost', 'gradient' => 'linear-gradient(135deg, #4b5563 0%, #1f2937 100%)'],
        ];

        $myListIds = \App\Models\MyList::where('user_id', Auth::id())->pluck('tmdb_id')->toArray();
        $activeGenre = $request->get('genre', 'all');

        return view('genres', compact('genres', 'uniqueContent', 'myListIds', 'isMocked', 'activeGenre'));
    }

    /**
     * Display popular actors page.
     */
    public function actors(Request $request)
    {
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');
        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $actorsList = [];
        $isMocked = false;

        if ($hasCredentials) {
            try {
                // 30 Verified Korean actors & actresses IDs:
                $actorIds = [
                    1251581, 582130, 74421, 86889, 1252318, 1067849, 2112859, 1014784, 
                    1252045, 1156197, 1134684, 116175, 1537768, 1353829, 140335, 63436, 
                    1252016, 1347525, 1245104, 150903, 2117890, 587634, 1878952, 1253391, 
                    1095818, 1459772, 1470763, 1571598, 1238592, 1613836
                ];
                foreach ($actorIds as $id) {
                    $actorData = Cache::remember("actor_profile_{$id}", now()->addDays(7), function () use ($id) {
                        return $this->tmdbService->getActorProfile($id);
                    });
                    if ($actorData) {
                        // Classify gender (1 = Female/Aktris, 2 = Male/Aktor)
                        $gender = $actorData['gender'] ?? 2;
                        $actorData['role_label'] = $gender === 1 ? 'Aktris' : 'Aktor';
                        $actorsList[] = $actorData;
                    }
                }
            } catch (\Exception $e) {
                $isMocked = true;
            }
        }

        if (!$hasCredentials || empty($actorsList) || $isMocked) {
            $isMocked = true;
            $actorsList = $this->getMockActors();
        }

        return view('actors', compact('actorsList', 'isMocked'));
    }

    /**
     * Display user's custom watchlist ("My List").
     */
    public function myList(Request $request)
    {
        $myList = \App\Models\MyList::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('mylist', compact('myList'));
    }

    /**
     * Toggle item in user's watchlist ("My List").
     */
    public function toggleMyList(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'media_type' => 'required|string|in:tv,movie',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
            'vote_average' => 'nullable|numeric'
        ]);

        $userId = Auth::id();
        $exists = \App\Models\MyList::where('user_id', $userId)
            ->where('tmdb_id', $request->tmdb_id)
            ->where('media_type', $request->media_type)
            ->first();

        if ($exists) {
            $exists->delete();
            $added = false;
            $message = 'Berhasil dihapus dari Daftar Saya.';
        } else {
            \App\Models\MyList::create([
                'user_id' => $userId,
                'tmdb_id' => $request->tmdb_id,
                'media_type' => $request->media_type,
                'title' => $request->title,
                'poster_path' => $request->poster_path,
                'vote_average' => $request->vote_average
            ]);
            $added = true;
            $message = 'Berhasil ditambahkan ke Daftar Saya.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $message
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Display user settings page.
     */
    public function settings(Request $request)
    {
        $user = Auth::user();
        $setting = \App\Models\UserSetting::firstOrCreate([
            'user_id' => $user->id
        ]);
        return view('settings', compact('user', 'setting'));
    }

    /**
     * Update user settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $setting = \App\Models\UserSetting::firstOrCreate([
            'user_id' => $user->id
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'theme_accent' => 'required|string|in:pink,blue,purple,green',
            'autoplay' => 'required|boolean',
            'playback_quality' => 'required|string|in:1080p,720p,480p',
            'language' => 'required|string|in:id,en'
        ]);

        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        // Update settings
        $setting->update([
            'theme_accent' => $request->theme_accent,
            'autoplay' => $request->autoplay,
            'playback_quality' => $request->playback_quality,
            'language' => $request->language
        ]);

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Search movies, tv dramas, and popular items.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect('/');
        }

        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');
        $hasCredentials = !empty($apiKey) && $apiKey !== 'your_api_key_here' || 
                           !empty($apiToken) && $apiToken !== 'your_bearer_token_here';

        $results = [];
        $isMocked = false;

        if ($hasCredentials) {
            try {
                $searchData = $this->tmdbService->searchMulti($query, 1);
                $resultsRaw = $searchData['results'] ?? [];
                
                // Filter only Korean media types (movie / tv)
                $results = array_values(array_filter($resultsRaw, function ($item) {
                    return isset($item['original_language']) && $item['original_language'] === 'ko'
                        && in_array($item['media_type'] ?? '', ['movie', 'tv']);
                }));
            } catch (\Exception $e) {
                $isMocked = true;
            }
        }

        if (!$hasCredentials || empty($results) || $isMocked) {
            $isMocked = true;
            
            // Search mock movies, tv shows, popular movies, popular tv shows
            $allMockContent = array_merge(
                $this->getMockMovies(), 
                $this->getMockTvShows(),
                $this->getMockPopularMovies(),
                $this->getMockPopularTvShows()
            );

            $results = array_values(array_filter($allMockContent, function ($item) use ($query) {
                $title = $item['title'] ?? ($item['name'] ?? '');
                $overview = $item['overview'] ?? '';
                return stripos($title, $query) !== false || stripos($overview, $query) !== false;
            }));

            // Format media_type for mock results
            foreach ($results as &$item) {
                if (!isset($item['media_type'])) {
                    $item['media_type'] = isset($item['name']) ? 'tv' : 'movie';
                }
            }
            unset($item);
        }

        $userId = Auth::id();
        $myListIds = \App\Models\MyList::where('user_id', $userId)->pluck('tmdb_id')->toArray();

        return view('search', compact('results', 'query', 'isMocked', 'myListIds'));
    }

    /**
     * Get mock popular TV shows
     */
    private function getMockPopularTvShows(): array
    {
        return [
            [
                'id' => 10009,
                'name' => 'Goblin: The Lonely and Great God',
                'overview' => 'Seorang jenderal dinasti Goryeo dikutuk menjadi goblin abadi. Satu-satunya cara untuk mengakhiri keabadiannya adalah dengan menemukan Pengantin Goblin.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400',
                'vote_average' => 9.2,
                'first_air_date' => '2016-12-02',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv'
            ],
            [
                'id' => 10010,
                'name' => 'Crash Landing on You',
                'overview' => 'Pewaris kaya raya Korea Selatan mengalami kecelakaan paragliding akibat angin puting beliung dan mendarat darurat di Korea Utara, di mana ia diselamatkan oleh seorang kapten militer.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?q=80&w=400',
                'vote_average' => 9.1,
                'first_air_date' => '2019-12-14',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv'
            ],
            [
                'id' => 10011,
                'name' => 'Descendants of the Sun',
                'overview' => 'Kisah cinta antara seorang kapten pasukan khusus tentara Korea dan seorang dokter ahli bedah relawan medis di negara konflik.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=400',
                'vote_average' => 8.8,
                'first_air_date' => '2016-02-24',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv'
            ],
            [
                'id' => 10012,
                'name' => 'The Glory',
                'overview' => 'Seorang mantan korban perundungan sekolah yang kejam merencanakan balas dendam yang rumit terhadap para penyiksanya bertahun-tahun kemudian.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=400',
                'vote_average' => 8.9,
                'first_air_date' => '2022-12-30',
                'kdrama_status' => 'Completed',
                'media_type' => 'tv'
            ]
        ];
    }

    /**
     * Get mock popular Movies
     */
    private function getMockPopularMovies(): array
    {
        return [
            [
                'id' => 10013,
                'title' => 'Parasite',
                'overview' => 'Keluarga Ki-taek yang menganggur dan miskin perlahan-lahan menyusup ke dalam kehidupan keluarga Park yang kaya raya dengan menyamar sebagai pekerja terpelajar.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400',
                'vote_average' => 9.3,
                'release_date' => '2019-05-30',
                'media_type' => 'movie'
            ],
            [
                'id' => 10014,
                'title' => 'Train to Busan',
                'overview' => 'Ketika virus zombi mewabah di Korea Selatan, para penumpang kereta KTX dari Seoul ke Busan harus berjuang bertahan hidup di tengah kepungan mayat hidup.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?q=80&w=400',
                'vote_average' => 9.0,
                'release_date' => '2016-07-20',
                'media_type' => 'movie'
            ],
            [
                'id' => 10015,
                'title' => 'Miracle in Cell No. 7',
                'overview' => 'Seorang ayah dengan keterbelakangan mental salah dituduh melakukan pembunuhan dan dipenjara. Teman-teman satu selnya membantu menyelundupkan putrinya yang masih kecil ke dalam penjara.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=400',
                'vote_average' => 8.9,
                'release_date' => '2013-01-23',
                'media_type' => 'movie'
            ],
            [
                'id' => 10016,
                'title' => 'Extreme Job',
                'overview' => 'Sekelompok detektif narkotika menyamar dengan membeli sebuah restoran ayam goreng untuk memata-matai geng kriminal, namun resep ayam goreng mereka malah meledak dan restoran mereka menjadi sangat terkenal.',
                'backdrop_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1200',
                'poster_path' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=400',
                'vote_average' => 8.5,
                'release_date' => '2019-01-23',
                'media_type' => 'movie'
            ]
        ];
    }

    /**
     * Get mock cast list corresponding to the show/movie ID
     */
    public function getMockCastForShow(int $id): array
    {
        $photos = [
            'Lee Jung-jae' => 'https://image.tmdb.org/t/p/w500/lx8oiTXL9lIx78KOXlrlvNfoz43.jpg',
            'Lee Byung-hun' => 'https://image.tmdb.org/t/p/w500/j7SUd9Qi8iOxgrQGb3nQyEYcXur.jpg',
            'Wi Ha-jun' => 'https://image.tmdb.org/t/p/w500/tEZuIaMESdBw4LfNq3vshGR4VlP.jpg',
            'Gong Yoo' => 'https://image.tmdb.org/t/p/w500/ocGoFb6TrK3uWGXt4WnuibUG1vD.jpg',
            'Kim Yoo-jung' => 'https://image.tmdb.org/t/p/w500/eFcMTDIgzSGLHZYXcsf1M42cvFt.jpg',
            'Song Kang' => 'https://image.tmdb.org/t/p/w500/83fLAMMb1LGT8YZ4dgRI0fti3az.jpg',
            'Lee Sang-yi' => 'https://image.tmdb.org/t/p/w500/4OSOux7D8ZIbNsA7eINSwmHM9nb.jpg',
            'Cho Hye-joo' => 'https://image.tmdb.org/t/p/w500/7nBMavFQSdTK6ARlNgKaeCmYZ89.jpg',
            'Choi Min-sik' => 'https://image.tmdb.org/t/p/w500/sd7gIA6nEkq6zumkDCfxSE0YSSV.jpg',
            'Kim Go-eun' => 'https://image.tmdb.org/t/p/w500/p7wuD6eX0YcZ5LXWhx0XjvEYZKz.jpg',
            'Yoo Hae-jin' => 'https://image.tmdb.org/t/p/w500/y6L2EsmnbnCFxCgfHR2oeL7oQoo.jpg',
            'Lee Do-hyun' => 'https://image.tmdb.org/t/p/w500/8LhhJWLGCoOPt5ygpDlrmfr1VOm.jpg',
            'Ma Dong-seok' => 'https://image.tmdb.org/t/p/w500/ckxoXz3l4mCcHEIRaqUc7oGoIFg.jpg',
            'Kim Mu-yeol' => 'https://image.tmdb.org/t/p/w500/sNOi9OsmoYgBk44N0R1mq51BHsQ.jpg',
            'Park Ji-hwan' => 'https://image.tmdb.org/t/p/w500/i5HP0n06Y5GIZfpsbibFQWAcUjV.jpg',
            'Lee Dong-hwi' => 'https://image.tmdb.org/t/p/w500/dqGCVaJ5yuGgKTPUEqCJNk8Nzbs.jpg',
            'Kim Woo-bin' => 'https://image.tmdb.org/t/p/w500/AjMMxxWbTNdafyWuY41xAHFPVou.jpg',
            'Kim Sung-kyun' => 'https://image.tmdb.org/t/p/w500/pii4fmXdkSEppsHSIhJ9jV5izX1.jpg',
            'Gang Dong-won' => 'https://image.tmdb.org/t/p/w500/xGPT8rgvWuK8Qh7BKNd7fhKs8Sk.jpg',
            'Park Jeong-min' => 'https://image.tmdb.org/t/p/w500/i5HP0n06Y5GIZfpsbibFQWAcUjV.jpg',
            'Cha Seung-won' => 'https://image.tmdb.org/t/p/w500/zT9SINDaQylXoRTWMb9OdBjLjLt.jpg',
            'Kim Shin-rock' => 'https://image.tmdb.org/t/p/w500/x4zo2mSbehnQY80PbuldAZ7ruGm.jpg',
            'Kim Soo-hyun' => 'https://image.tmdb.org/t/p/w500/q24P4pmtWGhe08T7rTkoDc5EC1p.jpg',
            'Kim Ji-won' => 'https://image.tmdb.org/t/p/w500/lX7W1j9kg4jV6XNn5XEE3rKsd3x.jpg',
            'Park Sung-hoon' => 'https://image.tmdb.org/t/p/w500/nSF8VW2IoZcAA910gKQxbu01umk.jpg',
            'Kwak Dong-yeon' => 'https://image.tmdb.org/t/p/w500/klgX8YheSFFXqEdjuCQY5rxTTGa.jpg',
            'Byeon Woo-seok' => 'https://image.tmdb.org/t/p/w500/iACGQCFJZUMsy4ywEzNiPokXFB9.jpg',
            'Kim Hye-yoon' => 'https://image.tmdb.org/t/p/w500/ujFBbc3d0UdS4qeioxvO3xRLkdl.jpg',
            'Song Geon-hee' => 'https://image.tmdb.org/t/p/w500/8rjlzhudRf3w3xtSso7R7BG8Ikq.jpg',
            'Lee Seung-hyub' => 'https://image.tmdb.org/t/p/w500/mzcPo1q5le10a03frb8NGRlRRXB.jpg',
            'Hyun Bin' => 'https://image.tmdb.org/t/p/w500/JQFzhO9j8HRiyr7leGPj6cqhvM.jpg',
            'Son Ye-jin' => 'https://image.tmdb.org/t/p/w500/i6fASFvO3mUceZJvipn4RiLHA44.jpg',
            'Seo Ji-hye' => 'https://image.tmdb.org/t/p/w500/55PUL5Yo6hoD2WJO5ysOiYULopg.jpg',
            'Kim Jung-hyun' => 'https://image.tmdb.org/t/p/w500/jL0bhHE8vIEomP1Zdz3a0ryXXgk.jpg',
            'Song Joong-ki' => 'https://image.tmdb.org/t/p/w500/kgjb5OppOVTh5tz3hhnfDVnTvDv.jpg',
            'Song Hye-kyo' => 'https://image.tmdb.org/t/p/w500/tlAX3f82Mf5h0rznpVK7nD2om.jpg',
            'Jin Goo' => 'https://image.tmdb.org/t/p/w500/jL0bhHE8vIEomP1Zdz3a0ryXXgk.jpg',
            'Lim Ji-yeon' => 'https://image.tmdb.org/t/p/w500/enVYwzEdTd7fKSZSX7DzyFUoacP.jpg',
            'Yeom Hye-ran' => 'https://image.tmdb.org/t/p/w500/5MgWM8pkUiYkj9MEaEpO0Ir1FD9.jpg',
            'Song Kang-ho' => 'https://image.tmdb.org/t/p/w500/kBM9UTPYXUA2RNk210DXhztLFns.jpg',
            'Lee Sun-kyun' => 'https://image.tmdb.org/t/p/w500/nHFBbSFohzOUOvMxPVwe3Es2nJw.jpg',
            'Cho Yeo-jeong' => 'https://image.tmdb.org/t/p/w500/5MgWM8pkUiYkj9MEaEpO0Ir1FD9.jpg',
            'Choi Woo-shik' => 'https://image.tmdb.org/t/p/w500/toFCyPac5rSpN58p0fsZ7lbogYc.jpg',
            'Jung Yu-mi' => 'https://image.tmdb.org/t/p/w500/4MGol78RZ9yakZdBAwnLUVgwO6F.jpg',
            'Kim Su-an' => 'https://image.tmdb.org/t/p/w500/hmPZhhoeUY89Rys6LrsjpTMeoEN.jpg',
            'Ryu Seung-ryong' => 'https://image.tmdb.org/t/p/w500/p0LjCRqVqgTyvlZScMmsVFCnTIt.jpg',
            'Kal So-won' => 'https://image.tmdb.org/t/p/w500/c2gH0cVq3JduUOJGV22IckMXoTU.jpg',
            'Park Shin-hye' => 'https://image.tmdb.org/t/p/w500/5wyerOQUW0Ej7Xs8eqfwdoJgV7E.jpg',
            'Oh Dal-su' => 'https://image.tmdb.org/t/p/w500/hd72oGjq0YKYdgZKMnqtV00MV28.jpg',
            'Lee Hanee' => 'https://image.tmdb.org/t/p/w500/yayAwtzM4ucEzakcVL0MGchesoc.jpg',
            'Jin Seon-kyu' => 'https://image.tmdb.org/t/p/w500/toFCyPac5rSpN58p0fsZ7lbogYc.jpg',
            'Park Seo-joon' => 'https://image.tmdb.org/t/p/w500/k1ALgZkOApYt7PIUBkUitmknXQC.jpg',
            'Han So-hee' => 'https://image.tmdb.org/t/p/w500/yTp8dDqsufHzup74gV0fzOBOLqB.jpg',
            'Claudia Kim' => 'https://image.tmdb.org/t/p/w500/7nBMavFQSdTK6ARlNgKaeCmYZ89.jpg',
            'Lee Seung-gi' => 'https://image.tmdb.org/t/p/w500/AjMMxxWbTNdafyWuY41xAHFPVou.jpg',
            'Bae Suzy' => 'https://image.tmdb.org/t/p/w500/39x6Dc4ELZxHbxCuOM0ddbwKh3F.jpg',
            'Shin Sung-rok' => 'https://image.tmdb.org/t/p/w500/6ajwIMcTc2jVFsjVOx2W0XARxpj.jpg',
            'Lee Jong-suk' => 'https://image.tmdb.org/t/p/w500/eW73DbmKQrqb6xDC52oMbVehw6G.jpg',
            'Im Yoon-ah' => 'https://image.tmdb.org/t/p/w500/fsQvLwbsFMkLpMKLXE3IuZzxSaA.jpg',
            'Kim Joo-hun' => 'https://image.tmdb.org/t/p/w500/dqGCVaJ5yuGgKTPUEqCJNk8Nzbs.jpg',
            'Park Hyung-sik' => 'https://image.tmdb.org/t/p/w500/8FHH9kQLRdByDOjdMn1oRUCEWZA.jpg',
            'Yoon Park' => 'https://image.tmdb.org/t/p/w500/rkHUsPNywWehr3g3z62nExl2M9P.jpg',
            'Shin Min-a' => 'https://image.tmdb.org/t/p/w500/6CjxjYvZBuvbAQTcuoPb8EeVrUI.jpg',
            'Kim Seon-ho' => 'https://image.tmdb.org/t/p/w500/qG8QeXHCn3iAJxeUCsPswM3Zw5l.jpg',
            'Kim Tae-ri' => 'https://image.tmdb.org/t/p/w500/gFofVUeVlIvBJMUv7maHQwWdfsk.jpg',
            'Oh Jung-se' => 'https://image.tmdb.org/t/p/w500/sd7gIA6nEkq6zumkDCfxSE0YSSV.jpg',
            'Hong Kyung' => 'https://image.tmdb.org/t/p/w500/wRXum47BB0ciPGINjCvT36rDuQE.jpg',
            'Lee Jin-wook' => 'https://image.tmdb.org/t/p/w500/4OSOux7D8ZIbNsA7eINSwmHM9nb.jpg',
            'Lee Si-young' => 'https://image.tmdb.org/t/p/w500/yTp8dDqsufHzup74gV0fzOBOLqB.jpg',
            'Ji Chang-wook' => 'https://image.tmdb.org/t/p/w500/sBmHrO5Tn27Ot5hy0yAKniROmNb.jpg',
            'Shin Hye-sun' => 'https://image.tmdb.org/t/p/w500/upYb53SXkorU7NECcNOB87xw7go.jpg',
            'Kim Mi-kyung' => 'https://image.tmdb.org/t/p/w500/5MgWM8pkUiYkj9MEaEpO0Ir1FD9.jpg',
            'Jung Hae-in' => 'https://image.tmdb.org/t/p/w500/7ZMUHR2q0XTWMxuqijWSVfWQv14.jpg',
            'Jung So-min' => 'https://image.tmdb.org/t/p/w500/cv8i4ALEabYkwnu68p5AdRTrMG9.jpg',
            'Kim Ji-eun' => 'https://image.tmdb.org/t/p/w500/eKDb8JtrO4SPySd0OwzttW1uvVw.jpg',
            'Yun Ji-on' => 'https://image.tmdb.org/t/p/w500/lm6SziV6Mm954EW32xyiSc5jzif.jpg',
            'Koo Kyo-hwan' => 'https://image.tmdb.org/t/p/w500/aFwCpSKPGFqoiTStY6ljXnD5CwH.jpg',
            'Son Suk-ku' => 'https://image.tmdb.org/t/p/w500/lx8oiTXL9lIx78KOXlrlvNfoz43.jpg',
            'Ahn Hyo-seop' => 'https://image.tmdb.org/t/p/w500/oLS5jvS5cZ7SaXNctA8VYXPp6ax.jpg',
            'Kim Se-jeong' => 'https://image.tmdb.org/t/p/w500/xGlsWq7iKB2DDNs04CaJEyQV3Vl.jpg',
            'Kim Min-kyu' => 'https://image.tmdb.org/t/p/w500/klgX8YheSFFXqEdjuCQY5rxTTGa.jpg',
            'Seol In-ah' => 'https://image.tmdb.org/t/p/w500/nfHLot4nBfqWQKwcXykeol4Owjb.jpg',
            'Lee Dong-wook' => 'https://image.tmdb.org/t/p/w500/khwUahjzIHFlrxYeKQAWrbWqsL6.jpg',
            'Kim Hye-jun' => 'https://image.tmdb.org/t/p/w500/9ptrnFQm0uBBogrHLmRb91usDo0.jpg',
            'Seo Hyun-woo' => 'https://image.tmdb.org/t/p/w500/i5HP0n06Y5GIZfpsbibFQWAcUjV.jpg',
            'Kim Jae-young' => 'https://image.tmdb.org/t/p/w500/s6qcQ7rvKOjMZOsxcl1KgYLHIiz.jpg',
            'Kim In-kwon' => 'https://image.tmdb.org/t/p/w500/vMPHVFE0FhItp2uMMgGRYq5z5Bj.jpg',
            'Shin Ye-eun' => 'https://image.tmdb.org/t/p/w500/dGJRM6rEdk9NLdryDUg172XK5ip.jpg',
            'Ra Mi-ran' => 'https://image.tmdb.org/t/p/w500/45jkWK6vQpQo32FsbGMSDrZig62.jpg',
            'Jung Eun-chae' => 'https://image.tmdb.org/t/p/w500/pXooI6fxvTN7Qt9B9ur2TIlgkvM.jpg',
            'Kim Nam-gil' => 'https://image.tmdb.org/t/p/w500/jTMr4gpGMXfHZUeoPyaa2Rq9ZU4.jpg',
            'Honey Lee' => 'https://image.tmdb.org/t/p/w500/5V6XtzHEMYfVzMvyMpzpbImpbp9.jpg',
            'Bibi' => 'https://image.tmdb.org/t/p/w500/5VMlbVp3U8K1eOF86lgbkAqFQoV.jpg',
            'Ahn Bo-hyun' => 'https://image.tmdb.org/t/p/w500/2pNNCnjI1TwxjyP3Y4cJln1wOKE.jpg',
            'Park Ji-hyun' => 'https://image.tmdb.org/t/p/w500/A3sGA6JjvkQbIvTjxhdukpIi8jC.jpg',
            'Kang Sang-jun' => 'https://image.tmdb.org/t/p/w500/ntVm8JzE6eXvfDsvDOY9hQQbEmP.jpg',
            'Kim Shin-bi' => 'https://image.tmdb.org/t/p/w500/or6KtPM9qvgBuiXsNImpCUBjMit.jpg',
            'Bona' => 'https://image.tmdb.org/t/p/w500/z3L2wtjfJ2RoT2fmoUJgsMAXnLZ.jpg',
            'Jang Da-ah' => 'https://image.tmdb.org/t/p/w500/iBrT9gdTRl1jb4Ql9EgE9c2X2CR.jpg',
            'Ryu Da-in' => 'https://image.tmdb.org/t/p/w500/u3SPlNQEu1JKJhUnByX2ibpqP7.jpg',
            'Shin Seul-ki' => 'https://image.tmdb.org/t/p/w500/aeB01IaWrZllQPqBiYGoEnx0cYF.jpg',
            'Park Min-young' => 'https://image.tmdb.org/t/p/w500/5Op0Gx0DULFLHtGdWVdqnYdwkAS.jpg',
            'Na In-woo' => 'https://image.tmdb.org/t/p/w500/A0ibLEz3mwzUljcIzLoadxhcDOd.jpg',
            'Lee Yi-kyung' => 'https://image.tmdb.org/t/p/w500/arIQjEhzxihRJvkOJjIksPA5EZM.jpg',
            'Song Ha-yoon' => 'https://image.tmdb.org/t/p/w500/pPn8m1cjEtMoSdC3w50TbaMl6h9.jpg',
            'Han Hyo-joo' => 'https://image.tmdb.org/t/p/w500/4E7NuJsR7AnMYAefFSHkj4cftdf.jpg',
            'Zo In-sung' => 'https://image.tmdb.org/t/p/w500/69gbyFI0ET0l0dyjKChlx1Zx269.jpg',
            'Cha Tae-hyun' => 'https://image.tmdb.org/t/p/w500/fzMKBBQaBeK6V0dJnPMWHFaJx8T.jpg'
        ];

        $getCast = function($list) use ($photos) {
            return array_map(function($actor) use ($photos) {
                if (isset($photos[$actor['name']])) {
                    $actor['profile_path'] = $photos[$actor['name']];
                } else {
                    $actor['profile_path'] = 'https://image.tmdb.org/t/p/w500/lx8oiTXL9lIx78KOXlrlvNfoz43.jpg';
                }
                return $actor;
            }, $list);
        };

        switch ($id) {
            case 10001: // Squid Game
                return $getCast([
                    ['name' => 'Lee Jung-jae', 'character' => 'Seong Gi-hun'],
                    ['name' => 'Lee Byung-hun', 'character' => 'Front Man'],
                    ['name' => 'Wi Ha-jun', 'character' => 'Hwang Jun-ho'],
                    ['name' => 'Gong Yoo', 'character' => 'Recruiter']
                ]);
            case 10002: // My Demon
                return $getCast([
                    ['name' => 'Kim Yoo-jung', 'character' => 'Do Do-hee'],
                    ['name' => 'Song Kang', 'character' => 'Jung Gu-won'],
                    ['name' => 'Lee Sang-yi', 'character' => 'Joo Seok-hoon'],
                    ['name' => 'Cho Hye-joo', 'character' => 'Jin Ga-young']
                ]);
            case 10003: // Exhuma
                return $getCast([
                    ['name' => 'Choi Min-sik', 'character' => 'Kim Sang-deok'],
                    ['name' => 'Kim Go-eun', 'character' => 'Lee Hwa-rim'],
                    ['name' => 'Yoo Hae-jin', 'character' => 'Yeong-geun'],
                    ['name' => 'Lee Do-hyun', 'character' => 'Yoon Bong-gil']
                ]);
            case 10004: // The Roundup: Punishment
                return $getCast([
                    ['name' => 'Ma Dong-seok', 'character' => 'Ma Seok-do'],
                    ['name' => 'Kim Mu-yeol', 'character' => 'Baek Chang-gi'],
                    ['name' => 'Park Ji-hwan', 'character' => 'Jang Yi-soo'],
                    ['name' => 'Lee Dong-hwi', 'character' => 'Chang Dong-cheol']
                ]);
            case 10005: // Officer Black Belt
                return $getCast([
                    ['name' => 'Kim Woo-bin', 'character' => 'Lee Jung-do'],
                    ['name' => 'Kim Sung-kyun', 'character' => 'Jo Min-woo']
                ]);
            case 10006: // Uprising
                return $getCast([
                    ['name' => 'Gang Dong-won', 'character' => 'Cheon-yeong'],
                    ['name' => 'Park Jeong-min', 'character' => 'Jong-ryeo'],
                    ['name' => 'Cha Seung-won', 'character' => 'King Seonjo'],
                    ['name' => 'Kim Shin-rock', 'character' => 'Beom-dong']
                ]);
            case 10007: // Queen of Tears
                return $getCast([
                    ['name' => 'Kim Soo-hyun', 'character' => 'Baek Hyun-woo'],
                    ['name' => 'Kim Ji-won', 'character' => 'Hong Hae-in'],
                    ['name' => 'Park Sung-hoon', 'character' => 'Yoon Eun-sung'],
                    ['name' => 'Kwak Dong-yeon', 'character' => 'Hong Soo-cheol']
                ]);
            case 10008: // Lovely Runner
                return $getCast([
                    ['name' => 'Byeon Woo-seok', 'character' => 'Ryu Sun-jae'],
                    ['name' => 'Kim Hye-yoon', 'character' => 'Im Sol'],
                    ['name' => 'Song Geon-hee', 'character' => 'Kim Tae-sung'],
                    ['name' => 'Lee Seung-hyub', 'character' => 'Baek In-hyuk']
                ]);
            case 10009: // Goblin
                return $getCast([
                    ['name' => 'Gong Yoo', 'character' => 'Kim Shin (Goblin)'],
                    ['name' => 'Kim Go-eun', 'character' => 'Ji Eun-tak'],
                    ['name' => 'Lee Dong-wook', 'character' => 'Grim Reaper'],
                    ['name' => 'Yoo In-na', 'character' => 'Sunny']
                ]);
            case 10010: // Crash Landing on You
                return $getCast([
                    ['name' => 'Hyun Bin', 'character' => 'Ri Jeong-hyeok'],
                    ['name' => 'Son Ye-jin', 'character' => 'Yoon Se-ri'],
                    ['name' => 'Seo Ji-hye', 'character' => 'Seo Dan'],
                    ['name' => 'Kim Jung-hyun', 'character' => 'Gu Seung-jun']
                ]);
            case 10011: // Descendants of the Sun
                return $getCast([
                    ['name' => 'Song Joong-ki', 'character' => 'Yoo Si-jin'],
                    ['name' => 'Song Hye-kyo', 'character' => 'Kang Mo-yeon'],
                    ['name' => 'Jin Goo', 'character' => 'Seo Dae-young'],
                    ['name' => 'Kim Ji-won', 'character' => 'Yoon Myung-ju']
                ]);
            case 10012: // The Glory
                return $getCast([
                    ['name' => 'Song Hye-kyo', 'character' => 'Moon Dong-eun'],
                    ['name' => 'Lee Do-hyun', 'character' => 'Joo Yeo-jeong'],
                    ['name' => 'Lim Ji-yeon', 'character' => 'Park Yeon-jin'],
                    ['name' => 'Yeom Hye-ran', 'character' => 'Kang Hyeon-nam']
                ]);
            case 10013: // Parasite
                return $getCast([
                    ['name' => 'Song Kang-ho', 'character' => 'Ki-taek'],
                    ['name' => 'Lee Sun-kyun', 'character' => 'Mr. Park'],
                    ['name' => 'Cho Yeo-jeong', 'character' => 'Mrs. Park'],
                    ['name' => 'Choi Woo-shik', 'character' => 'Ki-woo']
                ]);
            case 10014: // Train to Busan
                return $getCast([
                    ['name' => 'Gong Yoo', 'character' => 'Seok-woo'],
                    ['name' => 'Ma Dong-seok', 'character' => 'Sang-hwa'],
                    ['name' => 'Jung Yu-mi', 'character' => 'Seong-kyeong'],
                    ['name' => 'Kim Su-an', 'character' => 'Soo-an']
                ]);
            case 10015: // Miracle in Cell No. 7
                return $getCast([
                    ['name' => 'Ryu Seung-ryong', 'character' => 'Lee Yong-gu'],
                    ['name' => 'Kal So-won', 'character' => 'Ye-sung (child)'],
                    ['name' => 'Park Shin-hye', 'character' => 'Ye-sung (adult)'],
                    ['name' => 'Oh Dal-su', 'character' => 'So Yang-ho']
                ]);
            case 10016: // Extreme Job
                return $getCast([
                    ['name' => 'Ryu Seung-ryong', 'character' => 'Chief Go'],
                    ['name' => 'Lee Hanee', 'character' => 'Detective Jang'],
                    ['name' => 'Jin Seon-kyu', 'character' => 'Detective Ma'],
                    ['name' => 'Lee Dong-hwi', 'character' => 'Detective Young-ho']
                ]);
            case 10024: // Gyeongseong Creature
                return $getCast([
                    ['name' => 'Park Seo-joon', 'character' => 'Jang Tae-sang'],
                    ['name' => 'Han So-hee', 'character' => 'Yoon Chae-ok'],
                    ['name' => 'Claudia Kim', 'character' => 'Yukiko Maeda'],
                    ['name' => 'Wi Ha-jun', 'character' => 'Kwon Jun-taek']
                ]);
            case 10029: // Vagabond
                return $getCast([
                    ['name' => 'Lee Seung-gi', 'character' => 'Cha Dal-gun'],
                    ['name' => 'Bae Suzy', 'character' => 'Go Hae-ri'],
                    ['name' => 'Shin Sung-rok', 'character' => 'Ki Tae-ung']
                ]);
            case 10031: // Big Mouth
                return $getCast([
                    ['name' => 'Lee Jong-suk', 'character' => 'Park Chang-ho'],
                    ['name' => 'Im Yoon-ah', 'character' => 'Go Mi-ho'],
                    ['name' => 'Kim Joo-hun', 'character' => 'Choi Do-ha']
                ]);
            case 10033: // Doctor Slump
                return $getCast([
                    ['name' => 'Park Hyung-sik', 'character' => 'Yeo Jeong-woo'],
                    ['name' => 'Park Shin-hye', 'character' => 'Nam Ha-neul'],
                    ['name' => 'Yoon Park', 'character' => 'Bin Dae-yeong']
                ]);
            case 10039: // Hometown Cha-Cha-Cha
                return $getCast([
                    ['name' => 'Shin Min-a', 'character' => 'Yoon Hye-jin'],
                    ['name' => 'Kim Seon-ho', 'character' => 'Hong Du-sik'],
                    ['name' => 'Lee Sang-yi', 'character' => 'Ji Seong-hyun']
                ]);
            case 10043: // Revenant
                return $getCast([
                    ['name' => 'Kim Tae-ri', 'character' => 'Gu San-yeong'],
                    ['name' => 'Oh Jung-se', 'character' => 'Yeom Hae-sang'],
                    ['name' => 'Hong Kyung', 'character' => 'Lee Hong-sae']
                ]);
            case 10065: // Sweet Home
                return $getCast([
                    ['name' => 'Song Kang', 'character' => 'Cha Hyun-su'],
                    ['name' => 'Lee Jin-wook', 'character' => 'Pyeon Sang-wook'],
                    ['name' => 'Lee Si-young', 'character' => 'Seo Yi-kyung'],
                    ['name' => 'Lee Do-hyun', 'character' => 'Lee Eun-hyuk']
                ]);
            case 10067: // Welcome to Samdal-ri
                return $getCast([
                    ['name' => 'Ji Chang-wook', 'character' => 'Cho Yong-pil'],
                    ['name' => 'Shin Hye-sun', 'character' => 'Cho Sam-dal'],
                    ['name' => 'Kim Mi-kyung', 'character' => 'Go Mi-ja']
                ]);
            case 10073: // Love Next Door
                return $getCast([
                    ['name' => 'Jung Hae-in', 'character' => 'Choi Seung-hyo'],
                    ['name' => 'Jung So-min', 'character' => 'Bae Seok-ryu'],
                    ['name' => 'Kim Ji-eun', 'character' => 'Jeong Mo-eum'],
                    ['name' => 'Yun Ji-on', 'character' => 'Kang Dan-ho']
                ]);
            case 10074: // D.P.
                return $getCast([
                    ['name' => 'Jung Hae-in', 'character' => 'An Jun-ho'],
                    ['name' => 'Koo Kyo-hwan', 'character' => 'Han Ho-yeol'],
                    ['name' => 'Kim Sung-kyun', 'character' => 'Park Beom-gu'],
                    ['name' => 'Son Suk-ku', 'character' => 'Im Ji-seop']
                ]);
            case 10076: // Business Proposal
                return $getCast([
                    ['name' => 'Ahn Hyo-seop', 'character' => 'Kang Tae-moo'],
                    ['name' => 'Kim Se-jeong', 'character' => 'Shin Ha-ri'],
                    ['name' => 'Kim Min-kyu', 'character' => 'Cha Sung-hoon'],
                    ['name' => 'Seol In-ah', 'character' => 'Jin Young-seo']
                ]);
            case 10079: // A Shop for Killers
                return $getCast([
                    ['name' => 'Lee Dong-wook', 'character' => 'Jeong Jin-man'],
                    ['name' => 'Kim Hye-jun', 'character' => 'Jeong Ji-an'],
                    ['name' => 'Seo Hyun-woo', 'character' => 'Lee Seong-jo']
                ]);
            case 10084: // The Judge from Hell
                return $getCast([
                    ['name' => 'Park Shin-hye', 'character' => 'Kang Bit-na'],
                    ['name' => 'Kim Jae-young', 'character' => 'Han Da-on'],
                    ['name' => 'Kim In-kwon', 'character' => 'Valak / Goo Man-do']
                ]);
            case 10085: // Jeongnyeon: The Star is Born
                return $getCast([
                    ['name' => 'Kim Tae-ri', 'character' => 'Yoon Jeong-nyeon'],
                    ['name' => 'Shin Ye-eun', 'character' => 'Heo Young-seo'],
                    ['name' => 'Ra Mi-ran', 'character' => 'Kang So-bok'],
                    ['name' => 'Jung Eun-chae', 'character' => 'Moon Ok-gyeong']
                ]);
            case 10086: // The Fiery Priest 2
                return $getCast([
                    ['name' => 'Kim Nam-gil', 'character' => 'Kim Hae-il'],
                    ['name' => 'Honey Lee', 'character' => 'Park Kyung-sun'],
                    ['name' => 'Kim Sung-kyun', 'character' => 'Goo Dae-young'],
                    ['name' => 'Bibi', 'character' => 'Chae Do-woo']
                ]);
            case 10088: // Flex X Cop
                return $getCast([
                    ['name' => 'Ahn Bo-hyun', 'character' => 'Jin Yi-soo'],
                    ['name' => 'Park Ji-hyun', 'character' => 'Lee Kang-hyun'],
                    ['name' => 'Kang Sang-jun', 'character' => 'Yoo Joong-young'],
                    ['name' => 'Kim Shin-bi', 'character' => 'Choi Kyung-jin']
                ]);
            case 10089: // Pyramid Game
                return $getCast([
                    ['name' => 'Bona', 'character' => 'Seong Su-ji'],
                    ['name' => 'Jang Da-ah', 'character' => 'Baek Ha-rin'],
                    ['name' => 'Ryu Da-in', 'character' => 'Myeong Ja-eun'],
                    ['name' => 'Shin Seul-ki', 'character' => 'Seo Do-ah']
                ]);
            case 10090: // Marry My Husband
                return $getCast([
                    ['name' => 'Park Min-young', 'character' => 'Kang Ji-won'],
                    ['name' => 'Na In-woo', 'character' => 'Yoo Ji-hyuk'],
                    ['name' => 'Lee Yi-kyung', 'character' => 'Park Min-hwan'],
                    ['name' => 'Song Ha-yoon', 'character' => 'Jung Soo-min']
                ]);
            case 10091: // Moving
                return $getCast([
                    ['name' => 'Ryu Seung-ryong', 'character' => 'Jang Ju-won'],
                    ['name' => 'Han Hyo-joo', 'character' => 'Lee Mi-hyun'],
                    ['name' => 'Zo In-sung', 'character' => 'Kim Doo-shik'],
                    ['name' => 'Cha Tae-hyun', 'character' => 'Jeon Kye-do']
                ]);
            default:
                return $getCast([
                    ['name' => 'Lee Jung-jae', 'character' => 'Pemeran Utama']
                ]);
        }
    }

    private function getCuratedThrillerHorror(): array
    {
        return [
            [
                'id' => 10003,
                'title' => 'Exhuma',
                'media_type' => 'movie',
                'overview' => 'Proses penggalian kuburan leluhur kaya raya melepas entitas jahat misterius.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/6dasJ58GGFcC62H9KuukAryltUp.jpg',
                'vote_average' => 8.2,
                'release_date' => '2024-02-22',
                'genre_ids' => [27, 9648]
            ],
            [
                'id' => 10024,
                'name' => 'Gyeongseong Creature',
                'media_type' => 'tv',
                'overview' => 'Di Seoul 1945, dua pemuda menghadapi monster ganas hasil eksperimen rahasia.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/5wliFAD8Pjjj0YYSmupqjOldnWt.jpg',
                'vote_average' => 8.4,
                'first_air_date' => '2024-01-05',
                'genre_ids' => [18, 27, 9648]
            ],
            [
                'id' => 10065,
                'name' => 'Sweet Home (Season 3)',
                'media_type' => 'tv',
                'overview' => 'Manusia berubah menjadi monster cerminan hasrat terdalam mereka.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/zcugNxDg59YwIf3dUHsrHmO7pc1.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2024-07-19',
                'genre_ids' => [27, 10765, 18]
            ],
            [
                'id' => 10043,
                'name' => 'Revenant',
                'media_type' => 'tv',
                'overview' => 'Wanita yang dirasuki roh jahat dan profesor pengusir setan membongkar bunuh diri misterius.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/o2Sk7VEZpR5WOCjtjO6ClGBKSji.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2023-06-23',
                'genre_ids' => [27, 9648, 18]
            ],
            [
                'id' => 10079,
                'name' => 'A Shop for Killers',
                'media_type' => 'tv',
                'overview' => 'Gadis muda dikepung pembunuh bayaran tingkat tinggi di toko senjata pamannya.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/7yUY1HUyQuybbvkAAhLzQ7x1l9g.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2024-01-17',
                'genre_ids' => [28, 9648, 18]
            ],
            [
                'id' => 10014,
                'title' => 'Train to Busan',
                'media_type' => 'movie',
                'overview' => 'Wabah zombi mengerikan menyerang penumpang kereta cepat dari Seoul menuju Busan.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/vNVFt6dtcqnI7hqa6LFBUibuFiw.jpg',
                'vote_average' => 8.9,
                'release_date' => '2016-07-20',
                'genre_ids' => [27, 28, 18]
            ]
        ];
    }

    private function getCuratedRomance(): array
    {
        return [
            [
                'id' => 10008,
                'name' => 'Lovely Runner',
                'media_type' => 'tv',
                'overview' => 'Perjalanan waktu melintasi takdir demi menyelamatkan idol favorit dari nasib tragis.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/adcdNzLJ8LOjWJjNFrapXGzFco3.jpg',
                'vote_average' => 8.9,
                'first_air_date' => '2024-04-08',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10007,
                'name' => 'Queen of Tears',
                'media_type' => 'tv',
                'overview' => 'Krisis pernikahan dan kembalinya cinta ajaib antara pewaris konglomerat dan pria desa.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/7ZXLZ3KYL3IVvsSHBZaHjcNQzNU.jpg',
                'vote_average' => 9.0,
                'first_air_date' => '2024-03-09',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10002,
                'name' => 'My Demon',
                'media_type' => 'tv',
                'overview' => 'Pernikahan kontrak antara pewaris dingin dan iblis tampan yang hilang kekuatannya.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/xBnscv5BrJREKVSvh0le61y4KDk.jpg',
                'vote_average' => 8.5,
                'first_air_date' => '2024-01-20',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10090,
                'name' => 'Marry My Husband',
                'media_type' => 'tv',
                'overview' => 'Wanita yang dikhianati mendapat kesempatan hidup 10 tahun lalu untuk merajut masa depan baru.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/899KcBqooj8nEyPcAEU3h7AdfUo.jpg',
                'vote_average' => 8.8,
                'first_air_date' => '2024-01-01',
                'genre_ids' => [18, 10749, 10765]
            ],
            [
                'id' => 10073,
                'name' => 'Love Next Door',
                'media_type' => 'tv',
                'overview' => 'Pertemuan kembali dua teman masa kecil yang tahu masa lalu satu sama lain.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/hikbLeofw2epfaEJptSkQ6b22IV.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2024-08-17',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10010,
                'name' => 'Crash Landing on You',
                'media_type' => 'tv',
                'overview' => 'Pewaris kaya mendarat darurat di Korea Utara dan diselamatkan oleh seorang tentara.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/fgBNLPr6mC8pxuR79ENAJY4nBmj.jpg',
                'vote_average' => 9.1,
                'first_air_date' => '2019-12-14',
                'genre_ids' => [18, 10749, 35]
            ]
        ];
    }

    private function getCuratedMystery(): array
    {
        return [
            [
                'id' => 10001,
                'name' => 'Squid Game (Season 2)',
                'media_type' => 'tv',
                'overview' => 'Ratusan pemain terlilit utang mempertaruhkan nyawa dalam permainan teka-teki mematikan.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/1QdXdRYfktUSONkl1oD5gc6Be0s.jpg',
                'vote_average' => 8.8,
                'first_air_date' => '2024-12-26',
                'genre_ids' => [18, 9648, 28]
            ],
            [
                'id' => 10012,
                'name' => 'The Glory',
                'media_type' => 'tv',
                'overview' => 'Rencana balas dendam yang sangat matang dan terstruktur penuh plot twist mengagumkan.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/uUM4LVlPgIrww07OoEKrGWlS1Ej.jpg',
                'vote_average' => 8.9,
                'first_air_date' => '2022-12-30',
                'genre_ids' => [18, 9648]
            ],
            [
                'id' => 10089,
                'name' => 'Pyramid Game',
                'media_type' => 'tv',
                'overview' => 'Sistem peringkat rahasia sekolah menentukan nasib para siswi secara kejam.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/pBUERwWNCuO36CPxiVFsoQCPu7W.jpg',
                'vote_average' => 8.5,
                'first_air_date' => '2024-02-29',
                'genre_ids' => [18, 9648]
            ],
            [
                'id' => 10084,
                'name' => 'The Judge from Hell',
                'media_type' => 'tv',
                'overview' => 'Hakim wanita yang meresahkan membongkar kebenaran tersembunyi para penjahat.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/9vhLHbUiiP9HiXfJw5OUC7KoaJG.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2024-09-21',
                'genre_ids' => [10765, 9648, 18]
            ],
            [
                'id' => 10031,
                'name' => 'Big Mouth',
                'media_type' => 'tv',
                'overview' => 'Pengacara kelas bawah dijebak sebagai penjahat jenius misterius bernama Big Mouse.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/1Zio9w1tAd3r5Gu4d9AzTSx2hnT.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2022-07-29',
                'genre_ids' => [18, 9648, 28]
            ],
            [
                'id' => 10013,
                'title' => 'Parasite',
                'media_type' => 'movie',
                'overview' => 'Keluarga miskin menyusup ke kediaman keluarga kaya hingga terungkapnya rahasia ruang bawah tanah.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
                'vote_average' => 9.3,
                'release_date' => '2019-05-30',
                'genre_ids' => [18, 9648, 35]
            ]
        ];
    }

    private function getCuratedComedy(): array
    {
        return [
            [
                'id' => 10033,
                'name' => 'Doctor Slump',
                'media_type' => 'tv',
                'overview' => 'Dua dokter rival masa sekolah bertemu kembali saat titik terendah dalam karir mereka.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/iOWmbZEbhvrYyWm6O4W3oHf2S9B.jpg',
                'vote_average' => 8.3,
                'first_air_date' => '2024-01-27',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10086,
                'name' => 'The Fiery Priest 2',
                'media_type' => 'tv',
                'overview' => 'Pastor pemarah dan kocak kembali memimpin aksi pemberantasan kejahatan.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/5EDJlbB3S1vwOpsR3h1k0NMHiHY.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2024-11-08',
                'genre_ids' => [28, 35, 18]
            ],
            [
                'id' => 10088,
                'name' => 'Flex X Cop',
                'media_type' => 'tv',
                'overview' => 'Detektif kaya raya menggunakan uang dan relasinya untuk menangkap penjahat dengan cara lucu.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/2eWH2RDxpbIfEs17BpJTQhcOqRs.jpg',
                'vote_average' => 8.4,
                'first_air_date' => '2024-01-26',
                'genre_ids' => [28, 35, 9648]
            ],
            [
                'id' => 10076,
                'name' => 'Business Proposal',
                'media_type' => 'tv',
                'overview' => 'Kencan buta palsu membawa hubungan penuh komedi kocak antara bos dan karyawannya.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/iLh7L8ZuvgdxFaM9sImyv2iKYLe.jpg',
                'vote_average' => 8.9,
                'first_air_date' => '2022-02-28',
                'genre_ids' => [35, 10749, 18]
            ],
            [
                'id' => 10067,
                'name' => 'Welcome to Samdal-ri',
                'media_type' => 'tv',
                'overview' => 'Kehidupan warga desa Jeju yang ramah dan konyol menyambut kepulangan sang fotografer.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/98IvA2i0PsTY8CThoHByCKOEAjz.jpg',
                'vote_average' => 8.6,
                'first_air_date' => '2024-01-21',
                'genre_ids' => [18, 10749, 35]
            ],
            [
                'id' => 10016,
                'name' => 'Fight For My Way',
                'media_type' => 'tv',
                'overview' => 'Kisah perjuangan orang biasa menggapai impian diwarnai kekonyolan persahabatan.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/55w33JzFKBkz1evjBjqeF0enDd1.jpg',
                'vote_average' => 8.5,
                'first_air_date' => '2017-05-22',
                'genre_ids' => [35, 18, 10749]
            ]
        ];
    }

    private function getCuratedAction(): array
    {
        return [
            [
                'id' => 10004,
                'title' => 'The Roundup: Punishment',
                'media_type' => 'movie',
                'overview' => 'Detektif Ma Seok-do menindak tegas kartel kejahatan judi dan siber internasional.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/kfdVbOzwsKy65eaizDnBfxAJ95p.jpg',
                'vote_average' => 7.9,
                'release_date' => '2024-04-24',
                'genre_ids' => [28, 35]
            ],
            [
                'id' => 10005,
                'title' => 'Officer Black Belt',
                'media_type' => 'movie',
                'overview' => 'Ahli bela diri sabuk hitam bekerjasama melumpuhkan penjahat berbahaya.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/pWOiCearczq7zmWo4ASbUIF6HYS.jpg',
                'vote_average' => 8.1,
                'release_date' => '2024-09-13',
                'genre_ids' => [28, 35]
            ],
            [
                'id' => 10006,
                'title' => 'Uprising',
                'media_type' => 'movie',
                'overview' => 'Dua sahabat masa kecil berhadapan dalam perang sengit era Joseon.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/zeSO9ZDWaDuG3nTcijaoJ2V4UWU.jpg',
                'vote_average' => 8.0,
                'release_date' => '2024-10-11',
                'genre_ids' => [28, 18]
            ],
            [
                'id' => 10029,
                'name' => 'Vagabond',
                'media_type' => 'tv',
                'overview' => 'Pemeran pengganti terlibat dalam konspirasi besar kecelakaan pesawat.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/8Sud9wqG7WZMXesZhPZ4lergRvp.jpg',
                'vote_average' => 8.7,
                'first_air_date' => '2019-09-20',
                'genre_ids' => [28, 9648, 18]
            ],
            [
                'id' => 10074,
                'name' => 'D.P.',
                'media_type' => 'tv',
                'overview' => 'Unit khusus polisi militer bertugas menangkap para tentara pembangkang.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/akvPsqe1u1NpIQ8Ln2Vt06uhOv9.jpg',
                'vote_average' => 8.8,
                'first_air_date' => '2023-07-28',
                'genre_ids' => [28, 18]
            ],
            [
                'id' => 10091,
                'name' => 'Moving',
                'media_type' => 'tv',
                'overview' => 'Remaja dengan kekuatan super rahasia dan orang tua mereka berjuang melindungi diri dari ancaman gelap.',
                'poster_path' => 'https://image.tmdb.org/t/p/w500/4eb1lb2lkaMi5DLqxZQwHW1D6bg.jpg',
                'vote_average' => 9.1,
                'first_air_date' => '2023-08-09',
                'genre_ids' => [28, 10765, 18]
            ]
        ];
    }
}
