<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mubee - Movies & TV Streaming</title>
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
<body class="text-gray-100 font-sans leading-normal tracking-normal">

    <!-- Header -->
    <header class="border-b border-gray-800 bg-gray-950 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-red-600 font-extrabold text-3xl tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-play"></i> MUBEE
            </a>
            <nav class="flex items-center gap-6">
                <a href="/" class="hover:text-red-500 transition-colors font-medium">Home</a>
                <a href="/dramas" class="hover:text-red-500 transition-colors font-medium">Dramas</a>
                <a href="/movies" class="hover:text-red-500 transition-colors font-medium text-red-500">Movies</a>
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="relative h-[65vh] w-full bg-cover bg-center flex items-center justify-start px-8 md:px-16" 
             style="background-image: linear-gradient(to right, rgba(11, 12, 16, 0.95) 30%, rgba(11, 12, 16, 0.5) 60%, rgba(0,0,0,0) 100%), 
                                    url('https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=1200');">
        <div class="max-w-2xl">
            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">TRENDING ON MUBEE</span>
            <h1 class="text-4xl md:text-6xl font-black mt-4 leading-tight">Jelajahi K-Drama & Film Terbaik</h1>
            <p class="text-gray-400 mt-4 text-base md:text-lg">Streaming film dan serial drama Korea terpopuler lengkap dengan subtitle bahasa Indonesia. Mulai menonton secara gratis sekarang.</p>
            <div class="mt-8 flex gap-4">
                <a href="#movies" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-lg shadow-lg hover:shadow-red-600/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-film"></i> Telusuri Film
                </a>
                <a href="#tv" class="bg-gray-800 hover:bg-gray-700 text-white font-bold px-6 py-3 rounded-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-tv"></i> Telusuri Drama
                </a>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <main class="container mx-auto px-4 py-12 space-y-16">
        
        <!-- Trending Movies Row -->
        <section id="movies" class="space-y-6">
            <div class="border-l-4 border-red-600 pl-3">
                <h2 class="text-2xl font-extrabold tracking-wide uppercase">Film Populer Pekan Ini</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($trendingMovies as $movie)
                    @php
                        $poster = $movie['poster_path'] 
                            ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] 
                            : 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400';
                    @endphp
                    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-red-600/10 hover:border-red-600/50 hover:-translate-y-2 transition-all cursor-pointer group"
                         onclick="location.href='/movies/{{ $movie['id'] }}'">
                        <div class="relative overflow-hidden aspect-[2/3]">
                            <img src="{{ $poster }}" alt="{{ $movie['title'] ?? 'Movie' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-2 left-2 bg-black/85 border border-gray-700/50 text-yellow-400 text-xs font-bold px-2 py-1 rounded flex items-center gap-1">
                                <i class="fa-solid fa-star"></i> {{ number_format($movie['vote_average'] ?? 0, 1) }}
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm line-clamp-1 group-hover:text-red-500 transition-colors">{{ $movie['title'] ?? 'Movie Title' }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ substr($movie['release_date'] ?? '2024', 0, 4) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Trending TV Shows/Dramas Row -->
        <section id="tv" class="space-y-6">
            <div class="border-l-4 border-red-600 pl-3">
                <h2 class="text-2xl font-extrabold tracking-wide uppercase">Serial TV & Drama Populer</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($trendingTv as $tv)
                    @php
                        $poster = $tv['poster_path'] 
                            ? 'https://image.tmdb.org/t/p/w500' . $tv['poster_path'] 
                            : 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400';
                    @endphp
                    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-red-600/10 hover:border-red-600/50 hover:-translate-y-2 transition-all cursor-pointer group"
                         onclick="location.href='/tv/{{ $tv['id'] }}'">
                        <div class="relative overflow-hidden aspect-[2/3]">
                            <img src="{{ $poster }}" alt="{{ $tv['name'] ?? 'TV Show' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-2 left-2 bg-black/85 border border-gray-700/50 text-yellow-400 text-xs font-bold px-2 py-1 rounded flex items-center gap-1">
                                <i class="fa-solid fa-star"></i> {{ number_format($tv['vote_average'] ?? 0, 1) }}
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm line-clamp-1 group-hover:text-red-500 transition-colors">{{ $tv['name'] ?? 'Show Name' }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ substr($tv['first_air_date'] ?? '2024', 0, 4) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 py-8 text-center text-gray-500 text-sm">
        <p class="mb-2">© 2026 Mubee. Semua Hak Cipta Dilindungi.</p>
        <p class="text-xs text-gray-600">Didukung oleh TMDB API dan Embed Server VidSrc.</p>
    </footer>

</body>
</html>
