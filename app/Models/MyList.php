<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyList extends Model
{
    use HasFactory;

    protected $table = 'my_lists';

    protected $fillable = [
        'user_id',
        'tmdb_id',
        'media_type',
        'title',
        'poster_path',
        'vote_average',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
