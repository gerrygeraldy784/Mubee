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
        Schema::create('video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('tmdb_id');
            $table->string('episode_id')->nullable(); // Nullable for movies, represents episode code (e.g. S01E01) or TMDB episode ID for series
            $table->integer('last_position_seconds')->default(0);
            $table->boolean('is_finished')->default(false);
            $table->timestamps(); // Provides created_at and updated_at automatically

            // Add index for fast querying of real-time progress
            $table->index(['user_id', 'tmdb_id', 'episode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_progress');
    }
};
