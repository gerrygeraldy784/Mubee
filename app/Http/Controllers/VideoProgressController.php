<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoProgress;
use App\Models\WatchHistory;
use App\Services\TMDBService;

class VideoProgressController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Save the playback progress of a movie or episode in real-time.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'episode_id' => 'nullable|string',
            'last_position_seconds' => 'required|integer',
            'is_finished' => 'nullable|boolean',
            'media_type' => 'nullable|string|in:movie,tv'
        ]);

        // Get authenticated user, fallback to demo user ID 1
        $user = $request->user();
        $userId = $user ? $user->id : null;

        if (!$userId) {
            // Find or create a demo user for testing/visualization purposes
            $demoUser = \App\Models\User::firstOrCreate(
                ['email' => 'demo@mubee.com'],
                [
                    'name' => 'Demo User',
                    'password' => bcrypt('password_demo_123'),
                ]
            );
            $userId = $demoUser->id;
        }

        // Save progress to video_progress table
        $progress = VideoProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'tmdb_id' => $validated['tmdb_id'],
                'episode_id' => $validated['episode_id'] ?? null,
            ],
            [
                'last_position_seconds' => $validated['last_position_seconds'],
                'is_finished' => $validated['is_finished'] ?? false,
            ]
        );

        // Auto-record watch history when watching begins or updates
        $seasonNum = null;
        $episodeNum = null;
        if (!empty($validated['episode_id'])) {
            if (preg_match('/S(\d+)E(\d+)/i', $validated['episode_id'], $matches)) {
                $seasonNum = (int)$matches[1];
                $episodeNum = (int)$matches[2];
            } elseif (is_numeric($validated['episode_id'])) {
                $seasonNum = 1;
                $episodeNum = (int)$validated['episode_id'];
            }
        }

        WatchHistory::updateOrCreate(
            [
                'user_id' => $userId,
                'tmdb_id' => $validated['tmdb_id'],
                'media_type' => $validated['media_type'] ?? ($validated['episode_id'] ? 'tv' : 'movie'),
            ],
            [
                'season_number' => $seasonNum,
                'episode_number' => $episodeNum,
                'last_watched_at' => now(),
                'watched_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully.',
            'data' => [
                'user_id' => $progress->user_id,
                'tmdb_id' => $progress->tmdb_id,
                'episode_id' => $progress->episode_id,
                'last_position_seconds' => $progress->last_position_seconds,
                'is_finished' => $progress->is_finished,
            ]
        ]);
    }

    /**
     * Resume playback from the last watched position.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resume(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'episode_id' => 'nullable|string',
        ]);

        $user = $request->user();
        $userId = $user ? $user->id : null;

        if (!$userId) {
            $demoUser = \App\Models\User::where('email', 'demo@mubee.com')->first();
            $userId = $demoUser ? $demoUser->id : 1;
        }

        $progress = VideoProgress::where('user_id', $userId)
            ->where('tmdb_id', $validated['tmdb_id'])
            ->where('episode_id', $validated['episode_id'] ?? null)
            ->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'No prior progress found.',
                'data' => [
                    'last_position_seconds' => 0,
                    'is_finished' => false,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'last_position_seconds' => $progress->last_position_seconds,
                'is_finished' => $progress->is_finished,
            ]
        ]);
    }

    /**
     * Get player metadata including the real trailer video key from TMDB.
     *
     * @param string $type
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideoMetadata(string $type, int $id)
    {
        $videos = $this->tmdbService->getShowVideos($type, $id);
        
        // Extract YouTube trailer key
        $videoKey = null;
        if ($videos && isset($videos['results'])) {
            foreach ($videos['results'] as $video) {
                if (isset($video['site']) && strtolower($video['site']) === 'youtube' && 
                    isset($video['type']) && in_array(strtolower($video['type']), ['trailer', 'teaser'])) {
                    $videoKey = $video['key'];
                    break;
                }
            }
            // Fallback to any YouTube video
            if (!$videoKey && !empty($videos['results'])) {
                foreach ($videos['results'] as $video) {
                    if (isset($video['site']) && strtolower($video['site']) === 'youtube') {
                        $videoKey = $video['key'];
                        break;
                    }
                }
            }
        }

        // Fallback YouTube trailer key if none found
        if (!$videoKey) {
            $videoKey = '2s4s71Z_G3g'; // Squid Game Season 2 trailer
        }

        return response()->json([
            'success' => true,
            'tmdb_id' => $id,
            'media_type' => $type,
            'youtube_key' => $videoKey,
            'intro_start_seconds' => 10,
            'intro_end_seconds' => 40,
            'next_episode_id' => $type === 'tv' ? 'S01E05' : null,
        ]);
    }
}
