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
        Schema::create('my_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('tmdb_id');
            $table->string('media_type'); // tv or movie
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->double('vote_average', 3, 1)->nullable();
            $table->timestamps();

            // Prevent duplicate entries for the same user and show
            $table->unique(['user_id', 'tmdb_id', 'media_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_lists');
    }
};
