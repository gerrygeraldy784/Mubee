<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TMDBService;

class TestTMDBCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-tmdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test TMDB Service integrations';

    /**
     * Execute the console command.
     *
     * @param TMDBService $tmdbService
     * @return void
     */
    public function handle(TMDBService $tmdbService)
    {
        $this->info('Starting TMDB Service Tests...');

        // 1. Check credentials configuration
        $apiKey = config('services.tmdb.api_key');
        $apiToken = config('services.tmdb.api_token');

        if (($apiKey === 'your_api_key_here' || empty($apiKey)) && 
            ($apiToken === 'your_bearer_token_here' || empty($apiToken))) {
            $this->warn('Warning: Neither TMDB_API_KEY nor TMDB_API_TOKEN is properly configured in your .env file.');
            $this->warn('Please acquire an API Key or Read Access Bearer Token from themoviedb.org and configure it.');
            return;
        }

        $this->info('Credentials found. Executing sample TMDB requests...');

        // 2. Test Trending Now (trending/all/week filtered for Korean language)
        $this->info("\n--- Testing Trending Now (Korean Only) ---");
        $trending = $tmdbService->getTrendingNow(1, true);
        if ($trending && isset($trending['results'])) {
            $count = count($trending['results']);
            $this->info("Found {$count} trending Korean items this week.");
            foreach (array_slice($trending['results'], 0, 3) as $item) {
                $title = $item['title'] ?? $item['name'] ?? $item['original_name'] ?? 'Unknown';
                $mediaType = $item['media_type'] ?? 'unknown';
                $this->line("- [{$mediaType}] {$title} (Popularity: {$item['popularity']})");
            }
        } else {
            $this->error('Failed to retrieve trending items. Check your API credentials or network connection.');
        }

        // 3. Test Korean Content Search (discover/movie with language original = ko)
        $this->info("\n--- Testing Korean Content (Movie) ---");
        $kContent = $tmdbService->getKoreanContent('movie', 1);
        if ($kContent && isset($kContent['results'])) {
            $count = count($kContent['results']);
            $this->info("Found {$count} Korean movies on page 1.");
            foreach (array_slice($kContent['results'], 0, 3) as $item) {
                $title = $item['title'] ?? $item['original_title'] ?? 'Unknown';
                $this->line("- {$title} (Release: " . ($item['release_date'] ?? 'N/A') . ")");
            }
        } else {
            $this->error('Failed to retrieve Korean movies.');
        }

        // 4. Test Oppa/Eonni Cast Filter (Example Actor Lee Min-ho: TMDB ID 1251581)
        $this->info("\n--- Testing Oppa/Eonni Actor Profile (Kim Soo-hyun ID: 1251581) ---");
        $actorId = 1251581;
        $profile = $tmdbService->getActorProfile($actorId);
        if ($profile) {
            $this->info("Actor Name: {$profile['name']}");
            $this->line("Place of Birth: " . ($profile['place_of_birth'] ?? 'N/A'));
            $creditsCount = count($profile['credits'] ?? []);
            $this->info("Starred in {$creditsCount} featured works (displaying top 3 sorted by popularity):");
            foreach (array_slice($profile['credits'] ?? [], 0, 3) as $credit) {
                $title = $credit['title'] ?? $credit['name'] ?? 'Unknown';
                $char = $credit['character'] ?? 'Unknown';
                $this->line("  * [{$credit['media_type']}] {$title} as '{$char}'");
            }
        } else {
            $this->error('Failed to retrieve actor profile.');
        }

        // 5. Test TV Show Detail mapping (Status label Ongoing vs Completed)
        // Crash Course in Romance (TMDB ID 210875) - Status: Ended (Should map to Completed)
        $this->info("\n--- Testing KDrama Detail Label (Crash Course in Romance TV ID: 210875) ---");
        $showDetails = $tmdbService->getShowDetails('tv', 210875);
        if ($showDetails) {
            $this->info("Drama Title: {$showDetails['title_english']}");
            $this->line("Original Title: {$showDetails['title_original']}");
            $this->line("TMDB Status: {$showDetails['status']}");
            $this->info("Mapped Platform Label: {$showDetails['kdrama_status']}"); // Should be 'Completed'
        } else {
            $this->error('Failed to retrieve TV Show details.');
        }

        $this->info("\nTMDB Service test run completed.");
    }
}
