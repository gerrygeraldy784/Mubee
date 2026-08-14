<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Bookmarks table
        if (!Schema::hasTable('bookmarks')) {
            Schema::create('bookmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedBigInteger('tmdb_id');
                $table->string('type'); // 'movie' or 'tv'
                $table->string('title');
                $table->string('poster_path')->nullable();
                $table->timestamps();
            });
        }

        // 2. Modify watch_history table
        Schema::table('watch_history', function (Blueprint $table) {
            if (!Schema::hasColumn('watch_history', 'season_number')) {
                $table->integer('season_number')->nullable()->after('media_type');
            }
            if (!Schema::hasColumn('watch_history', 'episode_number')) {
                $table->integer('episode_number')->nullable()->after('season_number');
            }
            if (!Schema::hasColumn('watch_history', 'last_watched_at')) {
                $table->timestamp('last_watched_at')->nullable()->useCurrentOnUpdate()->after('episode_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');

        Schema::table('watch_history', function (Blueprint $table) {
            $table->dropColumn(['season_number', 'episode_number', 'last_watched_at']);
        });
    }
};
