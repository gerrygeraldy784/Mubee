<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tvShow['name'] }} S{{ $season }}E{{ $episode }} - Mubee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b0c10; }
        .ep-card { transition: all 0.2s ease; }
        .ep-card:hover:not(.ep-disabled) { background-color: #1f2937; border-color: rgba(239,68,68,0.4); }
        .ep-card.active { border-color: #ef4444; background-color: rgba(239,68,68,0.1); }
        .ep-disabled { opacity: 0.4; cursor: not-allowed; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #111827; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #374151; border-radius: 3px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #ef4444; }
        .server-btn { transition: all 0.2s ease; }
        .server-btn.active-server { background-color: #dc2626; border-color: #dc2626; color: white; }
    </style>
</head>
<body class="text-gray-100 font-sans">

    <!-- Header -->
    <header class="border-b border-gray-800 bg-gray-950 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-red-600 font-extrabold text-2xl tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-play"></i> MUBEE
            </a>
            <div class="flex items-center gap-3">
                <a href="/tv/{{ $tvShow['id'] }}" class="hover:bg-gray-800 border border-gray-700 text-white font-semibold px-4 py-2 rounded-lg transition-all flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-list"></i> Daftar Episode
                </a>
                <a href="/" class="hover:bg-gray-800 border border-gray-700 text-white px-3 py-2 rounded-lg transition-all flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-house"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Watch Layout -->
    <div class="flex flex-col xl:flex-row min-h-[calc(100vh-65px)]">

        <!-- LEFT: Video Player + Info -->
        <div class="flex-1 flex flex-col">

            <!-- Server Selector -->
            <div class="bg-gray-950 border-b border-gray-800 px-4 py-4">
                <div class="max-w-5xl mx-auto">
                    
                    <!-- Server Selector -->
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-server text-red-500"></i> Pilih Server Video
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="switchServer(1)" id="server-1"
                                    class="server-btn active-server text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 1 (VidSrc - Dub Korea & Sub Indo)
                            </button>
                            <button onclick="switchServer(2)" id="server-2"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 2 (VidLink VIP - Fast Sub Indo)
                            </button>
                            <button onclick="switchServer(3)" id="server-3"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 3 (Embed.su - Dub Korea & Multi Sub)
                            </button>
                            <button onclick="switchServer(4)" id="server-4"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 4 (NontonGo K-Drama Sub Indo)
                            </button>
                            <button onclick="switchServer(5)" id="server-5"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 5 (SmashyStream - Multi Audio)
                            </button>
                            <button onclick="switchServer(6)" id="server-6"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 6 (MultiEmbed)
                            </button>
                            <button onclick="switchServer(7)" id="server-7"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 7 (AutoEmbed)
                            </button>
                            <button onclick="switchServer(8)" id="server-8"
                                    class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                                <i class="fa-solid fa-play-circle text-red-500"></i> Server 8 (VidSrc.icu VIP)
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Video Player (Responsive) -->
            <div class="w-full bg-black">
                <div class="relative w-full" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                    <iframe id="videoPlayer"
                            src="{{ $embedUrl }}?sub_info=true&ds_lang=id"
                            class="absolute top-0 left-0 w-full h-full"
                            allowfullscreen
                            referrerpolicy="origin"
                            scrolling="no"
                            frameborder="0">
                    </iframe>
                </div>
            </div>

            <!-- Server & Subtitle Notice Banner -->
            <div class="bg-gradient-to-r from-red-950/40 via-gray-900 to-yellow-950/40 border-t border-gray-800 px-6 py-3">
                <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-2 text-xs">
                    <p class="text-gray-300 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-yellow-400 text-sm"></i>
                        <span><strong>Petunjuk Subtitle & Dubbing:</strong> Anda juga bisa mengatur teks Subtitle/Dubbing langsung melalui ikon <strong class="text-yellow-300">💬 CC</strong> atau <strong class="text-yellow-300">⚙️ Settings</strong> di kontrol player video.</span>
                    </p>
                    <span id="sub-status-badge" class="bg-red-500/20 border border-red-500/40 text-red-400 px-2.5 py-1 rounded font-bold whitespace-nowrap">
                        SUB INDO ACTIVE
                    </span>
                </div>
            </div>

            <!-- Episode Title & Navigation Controls -->
            <div class="bg-gray-950 border-t border-gray-900 px-6 py-5">
                <div class="max-w-5xl mx-auto space-y-4">
                    
                    <!-- Show Title -->
                    <div>
                        <a href="/tv/{{ $tvShow['id'] }}" class="text-red-500 text-sm font-bold hover:text-red-400 transition-colors">
                            {{ $tvShow['name'] }}
                        </a>
                        <h1 class="text-xl md:text-2xl font-black text-white mt-1">
                            S{{ str_pad($season, 2, '0', STR_PAD_LEFT) }}E{{ str_pad($episode, 2, '0', STR_PAD_LEFT) }} — {{ $currentEpisode['name'] ?? 'Episode ' . $episode }}
                        </h1>
                        @if(!empty($currentEpisode['air_date']))
                            <p class="text-xs text-gray-500 mt-1">Tayang: {{ $currentEpisode['air_date'] }}</p>
                        @endif
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        @if($prevEpisode)
                            <a href="/tv/{{ $tvShow['id'] }}/watch/{{ $season }}/{{ $prevEpisode }}"
                               class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white font-bold px-5 py-2.5 rounded-lg transition-all text-sm">
                                <i class="fa-solid fa-backward-step"></i> Episode Sebelumnya
                            </a>
                        @elseif($prevSeason)
                            <a href="/tv/{{ $tvShow['id'] }}?season={{ $prevSeason }}"
                               class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white font-bold px-5 py-2.5 rounded-lg transition-all text-sm">
                                <i class="fa-solid fa-backward-fast"></i> Season {{ $prevSeason }}
                            </a>
                        @else
                            <span class="flex items-center gap-2 bg-gray-900 border border-gray-800 text-gray-600 font-bold px-5 py-2.5 rounded-lg text-sm cursor-not-allowed">
                                <i class="fa-solid fa-backward-step"></i> Episode Pertama
                            </span>
                        @endif

                        <a href="/tv/{{ $tvShow['id'] }}"
                           class="flex items-center gap-2 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-300 font-semibold px-5 py-2.5 rounded-lg transition-all text-sm">
                            <i class="fa-solid fa-list-ul"></i> Semua Episode
                        </a>

                        @if($nextEpisode)
                            <a href="/tv/{{ $tvShow['id'] }}/watch/{{ $season }}/{{ $nextEpisode }}"
                               class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-lg transition-all shadow-lg hover:shadow-red-600/30 text-sm">
                                Episode Berikutnya <i class="fa-solid fa-forward-step"></i>
                            </a>
                        @elseif($nextSeason)
                            <a href="/tv/{{ $tvShow['id'] }}/watch/{{ $nextSeason }}/1"
                               class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-lg transition-all shadow-lg hover:shadow-red-600/30 text-sm">
                                Season {{ $nextSeason }} EP 1 <i class="fa-solid fa-forward-fast"></i>
                            </a>
                        @else
                            <span class="flex items-center gap-2 bg-gray-900 border border-gray-800 text-gray-600 font-bold px-5 py-2.5 rounded-lg text-sm cursor-not-allowed">
                                Episode Terakhir <i class="fa-solid fa-forward-step"></i>
                            </span>
                        @endif
                    </div>

                    <!-- Episode Overview -->
                    @if(!empty($currentEpisode['overview']))
                        <div class="pt-2 border-t border-gray-800">
                            <p class="text-gray-400 text-sm leading-relaxed">{{ $currentEpisode['overview'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR: Episode List Panel -->
        <div class="w-full xl:w-80 bg-gray-950 border-t xl:border-t-0 xl:border-l border-gray-800 flex flex-col">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-800 flex items-center justify-between sticky top-[65px] bg-gray-950 z-10">
                <div>
                    <p class="text-xs text-red-500 font-bold uppercase tracking-wider">Daftar Episode</p>
                    <p class="text-sm font-bold text-gray-200 mt-0.5">
                        {{ $seasonDetails['name'] ?? 'Season ' . $season }}
                    </p>
                </div>
                @if(($tvShow['number_of_seasons'] ?? 1) > 1)
                    <a href="/tv/{{ $tvShow['id'] }}?season={{ $season }}"
                       class="text-xs bg-gray-800 hover:bg-gray-700 border border-gray-700 px-3 py-1.5 rounded-lg text-gray-300 transition-all">
                        Ganti Season
                    </a>
                @endif
            </div>

            <!-- Episodes Scroll List -->
            <div class="flex-1 overflow-y-auto scrollbar-thin max-h-[calc(100vh-200px)]">
                @foreach($seasonDetails['episodes'] ?? [] as $ep)
                    @php
                        $epStill = $ep['still_path']
                            ? 'https://image.tmdb.org/t/p/w300' . $ep['still_path']
                            : 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=300';
                        $isCurrentEp = (int)$ep['episode_number'] === (int)$episode;
                        $isUpcoming = $ep['is_upcoming'] ?? false;
                    @endphp

                    @if($isUpcoming)
                        {{-- Disabled upcoming episode in sidebar --}}
                        <div class="ep-card ep-disabled flex gap-3 p-3 border-b border-gray-800/70">
                            <div class="flex-shrink-0 w-24 h-14 rounded-lg overflow-hidden relative bg-gray-800">
                                <img src="{{ $epStill }}" alt="EP{{ $ep['episode_number'] }}" class="w-full h-full object-cover grayscale">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/60">
                                    <i class="fa-solid fa-lock text-gray-500 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs text-gray-600 font-bold">EP {{ $ep['episode_number'] }}</p>
                                    <span class="text-[9px] bg-yellow-600/20 border border-yellow-600/30 text-yellow-600 px-1 py-0.5 rounded font-bold">BELUM TAYANG</span>
                                </div>
                                <p class="text-xs font-semibold text-gray-600 line-clamp-1 leading-snug mt-0.5">
                                    {{ $ep['name'] ?? 'Episode ' . $ep['episode_number'] }}
                                </p>
                                @if(!empty($ep['air_date']))
                                    <p class="text-[10px] text-gray-700 mt-1"><i class="fa-regular fa-calendar"></i> {{ $ep['air_date'] }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- Normal playable episode in sidebar --}}
                        <a href="/tv/{{ $tvShow['id'] }}/watch/{{ $season }}/{{ $ep['episode_number'] }}"
                           class="ep-card flex gap-3 p-3 border-b border-gray-800/70 {{ $isCurrentEp ? 'active' : '' }}">
                            <div class="flex-shrink-0 w-24 h-14 rounded-lg overflow-hidden relative bg-gray-800">
                                <img src="{{ $epStill }}" alt="EP{{ $ep['episode_number'] }}" class="w-full h-full object-cover">
                                @if($isCurrentEp)
                                    <div class="absolute inset-0 flex items-center justify-center bg-red-600/50">
                                        <i class="fa-solid fa-circle-play text-white text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs {{ $isCurrentEp ? 'text-red-500' : 'text-gray-500' }} font-bold">
                                    EP {{ $ep['episode_number'] }}
                                </p>
                                <p class="text-xs font-semibold text-gray-200 line-clamp-2 leading-snug mt-0.5">
                                    {{ $ep['name'] ?? 'Episode ' . $ep['episode_number'] }}
                                </p>
                                @if($ep['runtime'] ?? null)
                                    <p class="text-xs text-gray-600 mt-1">{{ $ep['runtime'] }} mnt</p>
                                @endif
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 py-5 text-center text-gray-600 text-xs">
        <p>© 2026 Mubee &nbsp;|&nbsp; Multi-Server Embed Player</p>
    </footer>

    <!-- Multi-Server, Subtitle & Dubbing Switcher Script -->
    <script>
        const servers = {
            1: 'https://vidsrc.cc/v2/embed/{{ $embedType }}/{{ $embedId }}/{{ $embedSeason }}/{{ $embedEpisode }}?sub_info=true&ds_lang=id',
            2: 'https://vidlink.pro/tv/{{ $embedId }}/{{ $embedSeason }}/{{ $embedEpisode }}?sub_info=true&ds_lang=id',
            3: 'https://embed.su/embed/{{ $embedType }}/{{ $embedId }}/{{ $embedSeason }}/{{ $embedEpisode }}',
            4: 'https://www.NontonGo.win/embed/tv/{{ $embedId }}/{{ $embedSeason }}/{{ $embedEpisode }}',
            5: 'https://embed.smashystream.com/playere.php?tmdb={{ $embedId }}&season={{ $embedSeason }}&episode={{ $embedEpisode }}',
            6: 'https://multiembed.mov/?video_id={{ $embedId }}&tmdb=1&s={{ $embedSeason }}&e={{ $embedEpisode }}',
            7: 'https://player.autoembed.cc/embed/{{ $embedType }}/{{ $embedId }}/{{ $embedSeason }}/{{ $embedEpisode }}',
            8: 'https://vidsrc.icu/embed/tv/{{ $embedId }}/{{ $embedSeason }}/{{ $embedEpisode }}'
        };

        let currentServer = 1;

        function switchServer(serverNum) {
            if (serverNum === currentServer) return;

            const iframe = document.getElementById('videoPlayer');
            const newUrl = servers[serverNum];

            if (newUrl) {
                iframe.src = newUrl;
                currentServer = serverNum;

                document.querySelectorAll('.server-btn').forEach(btn => {
                    btn.classList.remove('active-server');
                });
                const activeBtn = document.getElementById('server-' + serverNum);
                if (activeBtn) activeBtn.classList.add('active-server');
            }
        }


        // Auto-scroll active episode into view in sidebar
        document.addEventListener('DOMContentLoaded', function () {
            const activeEp = document.querySelector('.ep-card.active');
            if (activeEp) {
                activeEp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>

</body>
</html>
