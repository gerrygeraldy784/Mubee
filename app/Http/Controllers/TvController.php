<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use App\Models\Bookmark;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TvController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Display TV Series / Drama details, season list, and episode list of Season 1 (or selected season).
     */
    public function show(Request $request, $id)
    {
        $isCustomMockId = ($id >= 10000 && $id <= 10100);
        $tvShow = null;
        $selectedSeasonNumber = (int) $request->get('season', 1);
        $seasonDetails = null;

        if (!$isCustomMockId) {
            try {
                $tvShow = $this->tmdbService->getTvShowDetails($id);
                $seasonDetails = $this->tmdbService->getTvSeasonDetails($id, $selectedSeasonNumber);
            } catch (\Exception $e) {
                $tvShow = null;
            }
        }

        if (!$tvShow || $isCustomMockId) {
            $dashboardCtrl = new DashboardController($this->tmdbService);
            $tvShow = $dashboardCtrl->getMockDetails('tv', $id);
            $tvShow['name'] = $tvShow['name'] ?? $tvShow['title'] ?? 'K-Drama';

            $episodes = [];
            for ($ep = 1; $ep <= 16; $ep++) {
                $episodes[] = [
                    'episode_number' => $ep,
                    'name' => "Episode {$ep}: Alur Cerita Utama",
                    'overview' => "Kisah kelanjutan episode {$ep} untuk drama {$tvShow['name']}.",
                    'still_path' => null,
                    'air_date' => '2024-01-01',
                    'runtime' => 60
                ];
            }
            $seasonDetails = [
                'name' => "Season {$selectedSeasonNumber}",
                'episodes' => $episodes
            ];
        }

        // Flag upcoming episodes based on air_date
        $today = Carbon::today()->toDateString();
        if ($seasonDetails && isset($seasonDetails['episodes'])) {
            foreach ($seasonDetails['episodes'] as &$ep) {
                $airDate = $ep['air_date'] ?? null;
                $ep['is_upcoming'] = $airDate ? ($airDate > $today) : false;
            }
            unset($ep);
        }

        $userId = Auth::id();
        $isBookmarked = false;

        if ($userId) {
            $isBookmarked = Bookmark::where('user_id', $userId)
                ->where('tmdb_id', $id)
                ->where('type', 'tv')
                ->exists();
        }

        return view('tv.show', compact('tvShow', 'seasonDetails', 'selectedSeasonNumber', 'isBookmarked'));
    }

    /**
     * Display the embed player for a specific episode and provide Next/Prev controls.
     */
    public function watchEpisode($id, $season, $episode)
    {
        $isCustomMockId = ($id >= 10000 && $id <= 10100);
        $tvShow = null;
        $seasonDetails = null;
        $season = (int) $season;
        $episode = (int) $episode;

        if (!$isCustomMockId) {
            try {
                $tvShow = $this->tmdbService->getTvShowDetails($id);
                $seasonDetails = $this->tmdbService->getTvSeasonDetails($id, $season);
            } catch (\Exception $e) {
                $tvShow = null;
            }
        }

        if (!$tvShow || !$seasonDetails || $isCustomMockId) {
            $dashboardCtrl = new DashboardController($this->tmdbService);
            $tvShow = $dashboardCtrl->getMockDetails('tv', $id);
            $tvShow['name'] = $tvShow['name'] ?? $tvShow['title'] ?? 'K-Drama';

            $episodes = [];
            for ($ep = 1; $ep <= 16; $ep++) {
                $episodes[] = [
                    'episode_number' => $ep,
                    'name' => "Episode {$ep}: Alur Cerita Utama",
                    'overview' => "Kisah kelanjutan episode {$ep} untuk drama {$tvShow['name']}.",
                    'still_path' => null,
                    'air_date' => '2024-01-01',
                    'runtime' => 60
                ];
            }
            $seasonDetails = [
                'name' => "Season {$season}",
                'episodes' => $episodes
            ];
        }

        $episodesCount = count($seasonDetails['episodes'] ?? []);

        // Flag upcoming episodes based on air_date
        $today = Carbon::today()->toDateString();
        if (isset($seasonDetails['episodes'])) {
            foreach ($seasonDetails['episodes'] as &$ep) {
                $airDate = $ep['air_date'] ?? null;
                $ep['is_upcoming'] = $airDate ? ($airDate > $today) : false;
            }
            unset($ep);
        }

        // Find the current episode metadata
        $currentEpisode = null;
        foreach ($seasonDetails['episodes'] ?? [] as $ep) {
            if ((int) $ep['episode_number'] === $episode) {
                $currentEpisode = $ep;
                break;
            }
        }

        if (!$currentEpisode) {
            abort(404, 'Episode not found in this season');
        }

        // Setup Prev / Next navigation
        $prevEpisode = ($episode > 1) ? $episode - 1 : null;
        $nextEpisode = ($episode < $episodesCount) ? $episode + 1 : null;

        // Check if there are other seasons
        $totalSeasons = (int) ($tvShow['number_of_seasons'] ?? 1);
        $prevSeason = null;
        $nextSeason = null;

        if (!$nextEpisode && $season < $totalSeasons) {
            $nextSeason = $season + 1;
        }
        if (!$prevEpisode && $season > 1) {
            $prevSeason = $season - 1;
        }

        $userId = Auth::id();
        if ($userId) {
            // Save or update watch history
            WatchHistory::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tmdb_id' => $id,
                    'media_type' => 'tv',
                ],
                [
                    'season_number' => $season,
                    'episode_number' => $episode,
                    'last_watched_at' => now(),
                    'watched_at' => now(),
                ]
            );
        }

        // Pass embed components for multi-server switching in the view
        $embedType = 'tv';
        $embedId = $id;
        $embedSeason = $season;
        $embedEpisode = $episode;

        // Default embed URL (Server 1)
        $embedUrl = "https://vidsrc.cc/v2/embed/tv/{$id}/{$season}/{$episode}";

        return view('tv.watch', compact(
            'tvShow',
            'seasonDetails',
            'currentEpisode',
            'season',
            'episode',
            'prevEpisode',
            'nextEpisode',
            'prevSeason',
            'nextSeason',
            'embedUrl',
            'embedType',
            'embedId',
            'embedSeason',
            'embedEpisode'
        ));
    }
}
