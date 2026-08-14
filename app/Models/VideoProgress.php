<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoProgress extends Model
{
    // Explicitly define custom singular table name
    protected $table = 'video_progress';

    protected $fillable = [
        'user_id',
        'tmdb_id',
        'episode_id',
        'last_position_seconds',
        'is_finished',
    ];

    protected $casts = [
        'is_finished' => 'boolean',
        'last_position_seconds' => 'integer',
        'tmdb_id' => 'integer',
    ];

    /**
     * Get the user that owns the video progress.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
