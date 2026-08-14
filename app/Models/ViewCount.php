<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewCount extends Model
{
    // Table is standard plural view_counts
    protected $table = 'view_counts';

    protected $fillable = [
        'tmdb_id',
        'episode_id',
        'views_count',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'tmdb_id' => 'integer',
    ];
}
