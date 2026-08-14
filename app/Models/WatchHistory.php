<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    // Explicitly define custom singular table name
    protected $table = 'watch_history';

    // Disable standard updated_at/created_at as we use watched_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tmdb_id',
        'media_type',
        'season_number',
        'episode_number',
        'last_watched_at',
        'watched_at',
    ];

    protected $casts = [
        'watched_at' => 'datetime',
        'last_watched_at' => 'datetime',
        'tmdb_id' => 'integer',
        'season_number' => 'integer',
        'episode_number' => 'integer',
    ];

    /**
     * Get the user that owns the watch history record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
