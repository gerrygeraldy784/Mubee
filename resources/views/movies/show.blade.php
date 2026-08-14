<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $movie['title'] }} - Mubee</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0b0c10;
        }
    </style>
</head>
<body class="text-gray-100 font-sans">

    <!-- Header -->
    <header class="border-b border-gray-800 bg-gray-950">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-red-600 font-extrabold text-3xl tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-play"></i> MUBEE
            </a>
            <a href="/" class="bg-gray-850 hover:bg-gray-800 border border-gray-700 text-white font-semibold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container mx-auto px-4 py-8 max-w-6xl space-y-8">
        
        <!-- Video Player Section -->
        <section class="bg-black rounded-2xl overflow-hidden border border-gray-850 shadow-2xl">
            <!-- Multi-Server & Subtitle/Dub Selector -->
            <div class="bg-gray-950 border-b border-gray-800 p-4 space-y-4">
                <!-- Server Selector -->
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-server text-red-500"></i> Pilih Server Video
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="switchServer(1)" id="server-1" class="server-btn active-server text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 1 (VidSrc - Dub Korea & Sub Indo)
                        </button>
                        <button onclick="switchServer(2)" id="server-2" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 2 (VidLink VIP - Sub Indo)
                        </button>
                        <button onclick="switchServer(3)" id="server-3" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 3 (Embed.su - Multi Sub & Dub)
                        </button>
                        <button onclick="switchServer(4)" id="server-4" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 4 (NontonGo Sub Indo)
                        </button>
                        <button onclick="switchServer(5)" id="server-5" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 5 (SmashyStream)
                        </button>
                        <button onclick="switchServer(6)" id="server-6" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 6 (MultiEmbed)
                        </button>
                        <button onclick="switchServer(7)" id="server-7" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 7 (AutoEmbed)
                        </button>
                        <button onclick="switchServer(8)" id="server-8" class="server-btn text-xs font-bold px-3.5 py-2 rounded-lg border border-gray-700 bg-gray-800 text-gray-300 hover:border-red-500 hover:text-white">
                            <i class="fa-solid fa-play-circle text-red-500"></i> Server 8 (VidSrc.icu VIP)
                        </button>
                    </div>
                </div>

                <!-- Subtitle & Dubbing Control Bar -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 border-t border-gray-850">
                    <!-- Subtitle Language -->
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-closed-captioning text-yellow-400"></i> Pilihan Subtitle (Teks)
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="changeSubtitle('id')" id="sub-id" class="sub-btn active-sub text-xs font-semibold px-3 py-1.5 rounded-md border border-yellow-500/40 bg-yellow-500/10 text-yellow-300 hover:bg-yellow-500/20">
                                🇮🇩 Bahasa Indonesia (Sub Indo)
                            </button>
                            <button onclick="changeSubtitle('en')" id="sub-en" class="sub-btn text-xs font-semibold px-3 py-1.5 rounded-md border border-gray-700 bg-gray-800 text-gray-300 hover:bg-gray-700">
                                🇬🇧 English (Sub Eng)
                            </button>
                            <button onclick="changeSubtitle('ko')" id="sub-ko" class="sub-btn text-xs font-semibold px-3 py-1.5 rounded-md border border-gray-700 bg-gray-800 text-gray-300 hover:bg-gray-700">
                                🇰🇷 Korea (Original)
                            </button>
                            <button onclick="changeSubtitle('off')" id="sub-off" class="sub-btn text-xs font-semibold px-3 py-1.5 rounded-md border border-gray-700 bg-gray-800 text-gray-300 hover:bg-gray-700">
                                Matikan Subtitle
                            </button>
                        </div>
                    </div>

                    <!-- Audio / Dubbing -->
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-language text-cyan-400"></i> Pilihan Audio / Dubbing (Suara)
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="changeDubbing('ko')" id="dub-ko" class="dub-btn active-dub text-xs font-semibold px-3 py-1.5 rounded-md border border-cyan-500/40 bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20">
                                🇰🇷 Audio Asli Korea (Original)
                            </button>
                            <button onclick="changeDubbing('id')" id="dub-id" class="dub-btn text-xs font-semibold px-3 py-1.5 rounded-md border border-gray-700 bg-gray-800 text-gray-300 hover:bg-gray-700">
                                🇮🇩 Dubbing Indonesia (Dub Indo)
                            </button>
                            <button onclick="changeDubbing('en')" id="dub-en" class="dub-btn text-xs font-semibold px-3 py-1.5 rounded-md border border-gray-700 bg-gray-800 text-gray-300 hover:bg-gray-700">
                                🇬🇧 Dubbing English
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative aspect-video w-full">
                <!-- Video Player Iframe -->
                <iframe id="videoPlayer"
                        src="{{ $embedUrl }}?sub_info=true&ds_lang=id" 
                        class="absolute top-0 left-0 w-full h-full" 
                        allowfullscreen 
                        referrerpolicy="origin"
                        scrolling="no"
                        frameborder="0">
                </iframe>
            </div>
            
            <div class="bg-gray-950 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-t border-gray-900">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-white">{{ $movie['title'] }}</h1>
                    <p class="text-gray-400 text-sm mt-1">Status: Released &nbsp;|&nbsp; Subtitle: Bahasa Indonesia &nbsp;|&nbsp; Audio: Korea Original</p>
                </div>
                <!-- Bookmark Button -->
                <button id="bookmark-btn" 
                        onclick="toggleBookmark({{ $movie['id'] }}, 'movie', '{{ addslashes($movie['title']) }}', '{{ $movie['poster_path'] }}')"
                        class="bg-gray-800 hover:bg-gray-700 text-white font-bold px-6 py-3 rounded-lg shadow transition-all flex items-center gap-2 border border-gray-750 {{ $isBookmarked ? 'text-yellow-400 border-yellow-500/30' : '' }}">
                    <i class="fa-solid {{ $isBookmarked ? 'fa-star text-yellow-400' : 'fa-star-o' }}"></i> 
                    <span id="bookmark-text">{{ $isBookmarked ? 'Tersimpan di Favorit' : 'Tambah ke Favorit' }}</span>
                </button>
            </div>
        </section>

        <!-- Movie Details Info Section -->
        <section class="bg-gray-950 border border-gray-900 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-64 flex-shrink-0">
                @php
                    $poster = $movie['poster_path'] 
                        ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] 
                        : 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400';
                @endphp
                <img src="{{ $poster }}" alt="{{ $movie['title'] }}" class="w-full rounded-xl border border-gray-800 shadow-lg">
            </div>
            <div class="flex-1 space-y-6">
                <div>
                    <h2 class="text-xl font-extrabold border-b border-gray-800 pb-3 uppercase text-red-500">Sinopsis Film</h2>
                    <p class="text-gray-300 mt-4 leading-relaxed text-base">{{ $movie['overview'] ?? 'Sinopsis tidak tersedia untuk film ini.' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 block">Rating IMDB</span>
                        <span class="font-semibold text-yellow-400 flex items-center gap-1 mt-1">
                            <i class="fa-solid fa-star"></i> {{ number_format($movie['vote_average'] ?? 0, 1) }} / 10
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Rilis Tahun</span>
                        <span class="font-semibold text-white mt-1 block">{{ substr($movie['release_date'] ?? '2024', 0, 4) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Durasi Film</span>
                        <span class="font-semibold text-white mt-1 block">{{ $movie['runtime'] ?? 'N/A' }} Menit</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Kategori / Genre</span>
                        <span class="font-semibold text-white mt-1 block">
                            @foreach($movie['genres'] ?? [] as $genre)
                                <span class="bg-gray-800 text-xs px-2 py-1 rounded mr-1 inline-block mt-1">{{ $genre['name'] }}</span>
                            @endforeach
                        </span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 py-8 text-center text-gray-500 text-sm mt-16">
        <p class="mb-2">© 2026 Mubee. Semua Hak Cipta Dilindungi.</p>
        <p class="text-xs text-gray-600">Video Server: VidSrc Embed Player.</p>
    </footer>

    <!-- Multi-Server, Subtitle & Dubbing Switcher Script -->
    <script>
        const movieServers = {
            1: 'https://vidsrc.cc/v2/embed/movie/{{ $movie["id"] }}?sub_info=true&ds_lang=id',
            2: 'https://vidlink.pro/movie/{{ $movie["id"] }}?sub_info=true&ds_lang=id',
            3: 'https://embed.su/embed/movie/{{ $movie["id"] }}',
            4: 'https://nontongo.biz/embed/movie/{{ $movie["id"] }}',
            5: 'https://embed.smashystream.com/playere.php?tmdb={{ $movie["id"] }}',
            6: 'https://multiembed.mov/?video_id={{ $movie["id"] }}&tmdb=1',
            7: 'https://player.autoembed.cc/embed/movie/{{ $movie["id"] }}',
            8: 'https://vidsrc.icu/embed/movie/{{ $movie["id"] }}'
        };

        let currentServer = 1;

        function switchServer(serverNum) {
            if (serverNum === currentServer) return;

            const iframe = document.getElementById('videoPlayer');
            const newUrl = movieServers[serverNum];

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

        function changeSubtitle(langCode) {
            document.querySelectorAll('.sub-btn').forEach(btn => {
                btn.classList.remove('active-sub', 'border-yellow-500/40', 'bg-yellow-500/10', 'text-yellow-300');
                btn.classList.add('border-gray-700', 'bg-gray-800', 'text-gray-300');
            });
            const selectedBtn = document.getElementById('sub-' + langCode);
            if (selectedBtn) {
                selectedBtn.classList.remove('border-gray-700', 'bg-gray-800', 'text-gray-300');
                selectedBtn.classList.add('active-sub', 'border-yellow-500/40', 'bg-yellow-500/10', 'text-yellow-300');
            }

            const iframe = document.getElementById('videoPlayer');
            let baseUrl = (movieServers[currentServer] || movieServers[1]).split('?')[0];

            if (langCode !== 'off') {
                iframe.src = `${baseUrl}?ds_lang=${langCode}&sub_info=true&sub=${langCode}`;
            } else {
                iframe.src = baseUrl;
            }
        }

        function changeDubbing(dubLang) {
            document.querySelectorAll('.dub-btn').forEach(btn => {
                btn.classList.remove('active-dub', 'border-cyan-500/40', 'bg-cyan-500/10', 'text-cyan-300');
                btn.classList.add('border-gray-700', 'bg-gray-800', 'text-gray-300');
            });
            const selectedBtn = document.getElementById('dub-' + dubLang);
            if (selectedBtn) {
                selectedBtn.classList.remove('border-gray-700', 'bg-gray-800', 'text-gray-300');
                selectedBtn.classList.add('active-dub', 'border-cyan-500/40', 'bg-cyan-500/10', 'text-cyan-300');
            }

            if (dubLang === 'id' || dubLang === 'en') {
                switchServer(2);
                alert(`Pengisian suara / Dubbing [${dubLang.toUpperCase()}] diaktifkan. Anda juga dapat memilih trek suara Dubbing langsung di tombol ⚙️ (Pengaturan Player).`);
            } else {
                switchServer(1);
            }
        }

        function toggleBookmark(tmdbId, type, title, posterPath) {
            fetch('/bookmarks/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tmdb_id: tmdbId,
                    type: type,
                    title: title,
                    poster_path: posterPath
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const btn = document.getElementById('bookmark-btn');
                    const text = document.getElementById('bookmark-text');
                    if (data.bookmarked) {
                        btn.classList.add('text-yellow-400', 'border-yellow-500/30');
                        btn.querySelector('i').className = 'fa-solid fa-star text-yellow-400';
                        text.innerText = 'Tersimpan di Favorit';
                    } else {
                        btn.classList.remove('text-yellow-400', 'border-yellow-500/30');
                        btn.querySelector('i').className = 'fa-solid fa-star-o';
                        text.innerText = 'Tambah ke Favorit';
                    }
                }
            })
            .catch(err => console.error("Error toggling bookmark: ", err));
        }
    </script>

</body>
</html>
