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
        Schema::create('view_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id');
            $table->string('episode_id')->nullable(); // Nullable for movies, contains episode identifier for TV series
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();

            // Indexes for fast updates and retrieval of view stats
            $table->index(['tmdb_id', 'episode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_counts');
    }
};
