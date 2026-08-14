<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tvShow['name'] }} - Mubee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b0c10; }
        .episode-card:hover { background-color: #1f2937; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #111827; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #ef4444; }
    </style>
</head>
<body class="text-gray-100 font-sans">

    <!-- Header -->
    <header class="border-b border-gray-800 bg-gray-950 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-red-600 font-extrabold text-3xl tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-play"></i> MUBEE
            </a>
            <a href="/" class="hover:bg-gray-800 border border-gray-700 text-white font-semibold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </header>

    <!-- Hero Backdrop -->
    @php
        $backdrop = $tvShow['backdrop_path']
            ? 'https://image.tmdb.org/t/p/original' . $tvShow['backdrop_path']
            : 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1200';
        $poster = $tvShow['poster_path']
            ? 'https://image.tmdb.org/t/p/w500' . $tvShow['poster_path']
            : 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400';
    @endphp
    <section class="relative h-64 md:h-80 w-full overflow-hidden">
        <img src="{{ $backdrop }}" alt="{{ $tvShow['name'] }}" class="w-full h-full object-cover object-top">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b0c10] via-[#0b0c10]/70 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b0c10]/80 to-transparent"></div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto px-4 max-w-6xl -mt-20 relative z-10">
        
        <!-- Show Info Card -->
        <div class="flex flex-col md:flex-row gap-8 mb-10">
            
            <!-- Poster -->
            <div class="flex-shrink-0 w-40 md:w-52">
                <img src="{{ $poster }}" alt="{{ $tvShow['name'] }}" 
                     class="w-full rounded-xl border-2 border-gray-800 shadow-2xl shadow-black/80">
            </div>

            <!-- Info -->
            <div class="flex-1 pt-4 md:pt-8 space-y-4">
                <!-- Title & Bookmark -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-white leading-tight">{{ $tvShow['name'] }}</h1>
                        @if(!empty($tvShow['original_name']) && $tvShow['original_name'] !== $tvShow['name'])
                            <p class="text-gray-500 italic text-sm mt-1">{{ $tvShow['original_name'] }}</p>
                        @endif
                    </div>
                    <button id="bookmark-btn"
                            onclick="toggleBookmark({{ $tvShow['id'] }}, 'tv', '{{ addslashes($tvShow['name']) }}', '{{ $tvShow['poster_path'] }}')"
                            class="flex-shrink-0 border border-gray-700 hover:border-yellow-500/50 text-white font-bold px-5 py-2 rounded-lg shadow transition-all flex items-center gap-2 text-sm bg-gray-900 hover:bg-gray-800 {{ $isBookmarked ? 'text-yellow-400' : '' }}">
                        <i id="bookmark-icon" class="fa-solid fa-star {{ $isBookmarked ? 'text-yellow-400' : 'text-gray-500' }}"></i>
                        <span id="bookmark-text">{{ $isBookmarked ? 'Disimpan' : 'Simpan' }}</span>
                    </button>
                </div>

                <!-- Meta Badges -->
                <div class="flex flex-wrap gap-2 items-center text-sm">
                    <span class="flex items-center gap-1 text-yellow-400 font-bold">
                        <i class="fa-solid fa-star"></i> {{ number_format($tvShow['vote_average'] ?? 0, 1) }}
                    </span>
                    <span class="bg-gray-800 border border-gray-700 px-2 py-0.5 rounded text-gray-300">
                        {{ substr($tvShow['first_air_date'] ?? '2024', 0, 4) }}
                    </span>
                    <span class="bg-gray-800 border border-gray-700 px-2 py-0.5 rounded text-gray-300">
                        {{ $tvShow['number_of_seasons'] ?? 1 }} Season
                    </span>
                    <span class="bg-gray-800 border border-gray-700 px-2 py-0.5 rounded text-gray-300">
                        {{ $tvShow['number_of_episodes'] ?? '?' }} Episode
                    </span>
                    @if(!empty($tvShow['status']))
                        <span class="px-2 py-0.5 rounded font-semibold text-xs
                            {{ $tvShow['status'] === 'Returning Series' ? 'bg-green-900/50 border border-green-700 text-green-400' : 'bg-blue-900/50 border border-blue-700 text-blue-400' }}">
                            {{ $tvShow['status'] === 'Returning Series' ? 'Ongoing' : 'Completed' }}
                        </span>
                    @endif
                </div>

                <!-- Genres -->
                <div class="flex flex-wrap gap-2">
                    @foreach($tvShow['genres'] ?? [] as $genre)
                        <span class="bg-red-900/30 border border-red-700/30 text-red-400 text-xs px-3 py-1 rounded-full">{{ $genre['name'] }}</span>
                    @endforeach
                </div>

                <!-- Overview -->
                <p class="text-gray-400 leading-relaxed text-sm line-clamp-4">
                    {{ $tvShow['overview'] ?? 'Deskripsi tidak tersedia.' }}
                </p>
            </div>
        </div>

        <!-- Season Selector & Episodes Section -->
        <section class="space-y-6 mb-16">
            <!-- Season Picker Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-800 pb-4">
                <div class="border-l-4 border-red-600 pl-3">
                    <h2 class="text-xl font-extrabold uppercase tracking-wide">Pilih Season & Episode</h2>
                </div>

                <!-- Season Dropdown -->
                <form method="GET" action="/tv/{{ $tvShow['id'] }}" id="season-form">
                    <select name="season" onchange="this.form.submit()"
                            class="bg-gray-900 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-red-600 cursor-pointer text-sm">
                        @for($s = 1; $s <= ($tvShow['number_of_seasons'] ?? 1); $s++)
                            <option value="{{ $s }}" {{ $selectedSeasonNumber == $s ? 'selected' : '' }}>
                                Season {{ $s }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>

            <!-- Season Info -->
            @if($seasonDetails)
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <i class="fa-solid fa-circle-info text-gray-600"></i>
                    <span>{{ $seasonDetails['name'] ?? "Season {$selectedSeasonNumber}" }}</span>
                    <span class="text-gray-700">•</span>
                    <span>{{ count($seasonDetails['episodes'] ?? []) }} Episode</span>
                    @if(!empty($seasonDetails['air_date']))
                        <span class="text-gray-700">•</span>
                        <span>Tayang: {{ substr($seasonDetails['air_date'], 0, 4) }}</span>
                    @endif
                </div>

                <!-- Episodes Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($seasonDetails['episodes'] ?? [] as $episode)
                        @php
                            $epStill = $episode['still_path']
                                ? 'https://image.tmdb.org/t/p/w500' . $episode['still_path']
                                : 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=500';
                            $isUpcoming = $episode['is_upcoming'] ?? false;
                        @endphp

                        @if($isUpcoming)
                            {{-- Disabled upcoming episode card --}}
                            <div class="episode-card flex gap-4 bg-gray-900/50 border border-gray-800/50 rounded-xl p-3 opacity-50 cursor-not-allowed relative">
                                <div class="flex-shrink-0 w-32 h-20 rounded-lg overflow-hidden relative bg-gray-800">
                                    <img src="{{ $epStill }}" alt="Episode {{ $episode['episode_number'] }}" class="w-full h-full object-cover grayscale">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/60">
                                        <i class="fa-solid fa-lock text-gray-400 text-xl"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="text-xs text-gray-500 font-bold">EP {{ $episode['episode_number'] }}</p>
                                        <span class="text-[10px] bg-yellow-600/20 border border-yellow-600/30 text-yellow-500 px-1.5 py-0.5 rounded font-bold">BELUM TAYANG</span>
                                    </div>
                                    <h3 class="font-bold text-sm line-clamp-2 text-gray-500">
                                        {{ $episode['name'] ?? 'Episode ' . $episode['episode_number'] }}
                                    </h3>
                                    @if(!empty($episode['air_date']))
                                        <p class="text-xs text-gray-600 mt-1">
                                            <i class="fa-regular fa-calendar"></i> Tayang: {{ $episode['air_date'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Normal playable episode card --}}
                            <a href="/tv/{{ $tvShow['id'] }}/watch/{{ $selectedSeasonNumber }}/{{ $episode['episode_number'] }}"
                               class="episode-card flex gap-4 bg-gray-900 border border-gray-800 rounded-xl p-3 hover:border-red-600/50 transition-all group">
                                <div class="flex-shrink-0 w-32 h-20 rounded-lg overflow-hidden relative bg-gray-800">
                                    <img src="{{ $epStill }}" alt="Episode {{ $episode['episode_number'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fa-solid fa-circle-play text-white text-3xl drop-shadow-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-red-500 font-bold mb-0.5">EP {{ $episode['episode_number'] }}</p>
                                    <h3 class="font-bold text-sm line-clamp-2 text-gray-100 group-hover:text-white transition-colors">
                                        {{ $episode['name'] ?? 'Episode ' . $episode['episode_number'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $episode['overview'] ?? 'Deskripsi episode tidak tersedia.' }}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        @if($episode['runtime'] ?? null)
                                            {{ $episode['runtime'] }} mnt
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 text-gray-600">
                    <i class="fa-solid fa-circle-exclamation text-4xl mb-4"></i>
                    <p class="text-lg">Data season tidak tersedia.</p>
                </div>
            @endif
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 py-8 text-center text-gray-500 text-sm">
        <p>© 2026 Mubee. Semua Hak Cipta Dilindungi.</p>
    </footer>

    <script>
        function toggleBookmark(tmdbId, type, title, posterPath) {
            fetch('/bookmarks/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tmdb_id: tmdbId, type: type, title: title, poster_path: posterPath })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = document.getElementById('bookmark-icon');
                    const text = document.getElementById('bookmark-text');
                    const btn  = document.getElementById('bookmark-btn');
                    if (data.bookmarked) {
                        icon.className = 'fa-solid fa-star text-yellow-400';
                        text.innerText = 'Disimpan';
                        btn.classList.add('text-yellow-400');
                    } else {
                        icon.className = 'fa-solid fa-star text-gray-500';
                        text.innerText = 'Simpan';
                        btn.classList.remove('text-yellow-400');
                    }
                }
            })
            .catch(err => console.error("Error toggling bookmark:", err));
        }
    </script>

</body>
</html>
