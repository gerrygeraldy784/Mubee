<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details['title_english'] ?? 'Detail Konten' }} - Mubee</title>
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #08080a;
            --card-bg: rgba(20, 20, 25, 0.6);
            --primary: #ff2a54;
            --primary-gradient: linear-gradient(135deg, #ff2a54 0%, #ff6b3d 100%);
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
            --ongoing-color: #00e676;
            --completed-color: #2979ff;
            --glass-border: rgba(255, 255, 255, 0.08);
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --heading-family: 'Outfit', sans-serif;
        }

        /* Dynamic User Accent Theme */
        @if(auth()->check() && auth()->user()->setting)
            @php
                $accent = auth()->user()->setting->theme_accent;
                $colorMap = [
                    'pink' => ['#ff2a54', '#ff6b3d'],
                    'blue' => ['#2979ff', '#00e5ff'],
                    'purple' => ['#a020f0', '#ff007f'],
                    'green' => ['#00e676', '#b2ff59'],
                ];
                $colors = $colorMap[$accent] ?? $colorMap['pink'];
            @endphp
            :root {
                --primary: {{ $colors[0] }};
                --primary-gradient: linear-gradient(135deg, {{ $colors[0] }} 0%, {{ $colors[1] }} 100%);
            }
        @endif

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow-x: hidden;
            line-height: 1.5;
        }

        /* Header Navigation */
        header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to bottom, rgba(8, 8, 10, 0.8) 0%, rgba(8, 8, 10, 0) 100%);
        }

        .logo {
            font-family: var(--heading-family);
            font-weight: 800;
            font-size: 28px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn {
            color: var(--text-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--glass-border);
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.2s ease;
            backdrop-filter: blur(5px);
        }

        .back-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateX(-4px);
        }

        /* Detail Hero Section Responsive & Zoom Support */
        .detail-hero {
            position: relative;
            min-height: clamp(380px, 60vh, 650px);
            display: flex;
            align-items: flex-end;
            padding: clamp(80px, 10vh, 120px) clamp(16px, 5vw, 48px) clamp(30px, 5vh, 60px) clamp(16px, 5vw, 48px);
            overflow: hidden;
        }

        .detail-hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            filter: brightness(0.35) contrast(1.05);
        }

        .detail-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(8,8,10,1) 0%, rgba(8,8,10,0.8) 50%, rgba(8,8,10,0.2) 100%);
            z-index: -1;
        }

        .detail-header-content {
            display: flex;
            gap: clamp(16px, 3vw, 40px);
            width: 100%;
            align-items: flex-end;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out;
        }

        .detail-poster-wrapper {
            flex: 0 0 clamp(160px, 20vw, 240px);
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
            display: none; /* Show on wider screens */
        }

        @media(min-width: 768px) {
            .detail-poster-wrapper {
                display: block;
            }
        }

        .detail-poster {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .detail-info {
            flex: 1;
            min-width: 0;
        }

        .detail-title {
            font-family: var(--heading-family);
            font-size: clamp(26px, 5vw, 48px);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            word-break: break-word;
        }

        .detail-original-title {
            font-size: clamp(14px, 2vw, 18px);
            font-style: italic;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .detail-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .rating-badge {
            color: #ffd700;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-badge {
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-ongoing {
            background-color: var(--ongoing-color);
            color: #033012;
        }

        .badge-completed {
            background-color: var(--completed-color);
            color: #06193b;
        }

        .genre-pill {
            background: rgba(255,255,255,0.08);
            border: 1px solid var(--glass-border);
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 42, 84, 0.6);
        }

        /* Detail Body content */
        .detail-body {
            padding: 40px 6% 10px 6%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }

        @media(min-width: 992px) {
            .detail-body {
                grid-template-columns: 2fr 1fr;
            }
        }

        .synopsis-box h3 {
            font-family: var(--heading-family);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            padding-left: 12px;
        }

        .synopsis-box h3::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 4px;
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        .synopsis-text {
            color: var(--text-muted);
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Cast Profile Grid */
        .cast-section h3 {
            font-family: var(--heading-family);
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .cast-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 15px;
        }

        .cast-card {
            text-align: center;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 10px;
        }

        .cast-avatar-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 10px auto;
            border: 2px solid rgba(255,255,255,0.1);
        }

        .cast-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cast-name {
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cast-character {
            font-size: 10px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Recommendations Carousel */
        .section-container {
            padding: 40px 6% 20px 6%;
        }

        .section-title {
            font-family: var(--heading-family);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
            position: relative;
            padding-left: 14px;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            height: 70%;
            width: 4px;
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        .carousel-row {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 15px;
        }

        .carousel-row::-webkit-scrollbar {
            height: 6px;
        }

        .show-card {
            flex: 0 0 220px;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .show-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 42, 84, 0.5);
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
        }

        .card-img-container {
            width: 100%;
            height: 300px;
            position: relative;
            overflow: hidden;
            background: #111;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .show-card:hover .card-img {
            transform: scale(1.08);
        }

        .card-details {
            padding: 16px;
        }

        .card-title {
            font-family: var(--heading-family);
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .badge-rating {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(8, 8, 10, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffd700;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            backdrop-filter: blur(4px);
            z-index: 5;
        }

        /* -------------------------------------------------------------
           VIDEO PLAYER STYLING
        ---------------------------------------------------------------- */
        .video-player-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #000;
            z-index: 3000;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            animation: fadeIn 0.3s ease;
        }

        .video-player-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #ytPlayer {
            width: 100%;
            height: 100%;
            border: none;
        }

        .close-player {
            position: absolute;
            top: 30px;
            right: 40px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(0,0,0,0.6);
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            z-index: 3100;
            transition: all 0.2s ease;
        }

        .close-player:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: scale(1.1);
        }

        .player-btn {
            position: absolute;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            z-index: 3100;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            display: none; /* Hidden by default */
        }

        .skip-intro-btn {
            bottom: 80px;
            right: 40px;
            background: white;
            color: black;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .skip-intro-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }

        .next-episode-btn {
            bottom: 80px;
            right: 40px;
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .next-episode-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 42, 84, 0.6);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        footer {
            border-top: 1px solid var(--glass-border);
            padding: 40px 6% 30px 6%;
            background-color: #050507;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 50px;
        }

        footer .logo {
            justify-content: center;
            margin-bottom: 15px;
            font-size: 22px;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <a href="/" class="logo">
            <i class="fa-solid fa-play"></i> Mubee
        </a>
        <div style="display: flex; gap: 15px; align-items: center;">
            <a href="/" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="color: var(--text-color); font-weight: 600; font-size: 15px; background: rgba(255,255,255,0.06); border: 1px solid var(--glass-border); padding: 8px 16px; border-radius: 20px; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Detail Hero Section -->
    <section class="detail-hero">
        @php
            $backdrop = isset($details['backdrop_path']) && !str_starts_with($details['backdrop_path'], 'http')
                ? "https://image.tmdb.org/t/p/original" . $details['backdrop_path']
                : ($details['backdrop_path'] ?? 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=1200');
            
            $poster = isset($details['poster_path']) && !str_starts_with($details['poster_path'], 'http')
                ? "https://image.tmdb.org/t/p/w500" . $details['poster_path']
                : ($details['poster_path'] ?? 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400');
        @endphp
        <img src="{{ $backdrop }}" class="detail-hero-bg" alt="Backdrop">
        <div class="detail-hero-overlay"></div>
        
        <div class="detail-header-content">
            <div class="detail-poster-wrapper">
                <img src="{{ $poster }}" class="detail-poster" alt="Poster">
            </div>
            
            <div class="detail-info">
                <h1 class="detail-title">{{ $details['title_english'] ?? 'K-Content Detail' }}</h1>
                <div class="detail-original-title">{{ $details['title_original'] ?? '' }}</div>
                
                <div class="detail-meta">
                    <span class="rating-badge">
                        <i class="fa-solid fa-star"></i> {{ number_format($details['rating'] ?? 8.0, 1) }} IMDB
                    </span>
                    <span class="hero-year">
                        {{ substr($details['release_date'] ?? $details['first_air_date'] ?? '2024', 0, 4) }}
                    </span>
                    <span class="views-badge" style="background: rgba(255, 42, 84, 0.15); border: 1px solid rgba(255, 42, 84, 0.3); color: #ff2a54; padding: 3px 12px; border-radius: 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-regular fa-eye"></i> Ditonton: {{ $viewsCount }} kali
                    </span>
                    
                    @if(isset($details['kdrama_status']))
                        @if($details['kdrama_status'] === 'Ongoing')
                            <span class="status-badge badge-ongoing">Ongoing</span>
                        @else
                            <span class="status-badge badge-completed">Completed</span>
                        @endif
                    @endif

                    @foreach(array_slice($details['genres'] ?? [], 0, 3) as $genre)
                        <span class="genre-pill">{{ $genre['name'] }}</span>
                    @endforeach
                </div>
                
                <button class="btn btn-primary" onclick="playMovie('{{ $details['media_type'] ?? 'tv' }}', {{ $details['id'] }})">
                    <i class="fa-solid fa-play"></i> Putar Sekarang
                </button>
                <button type="button" class="btn btn-secondary"
                        data-added="{{ $inMyList ? 'true' : 'false' }}"
                        style="{{ $inMyList ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                        onclick="toggleMyListDetail(this, {{ $details['id'] }}, '{{ $details['media_type'] ?? 'movie' }}', '{{ addslashes($details['title_english'] ?? $details['title'] ?? 'K-Content') }}', '{{ $details['poster_path'] ?? null }}', {{ $details['rating'] ?? 0 }})">
                    @if($inMyList)
                        <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                    @else
                        <i class="fa-solid fa-plus"></i> Tambah ke Daftar Tontonan
                    @endif
                </button>
            </div>
        </div>
    </section>

    <!-- Detail Body Section -->
    <section class="detail-body">
        <!-- Left Side: Synopsis & Info -->
        <div class="synopsis-box">
            <h3>Sinopsis Cerita</h3>
            <p class="synopsis-text">
                {{ $details['synopsis_default'] ?? 'Plot sinopsis konten ini belum tersedia.' }}
            </p>

            @if(isset($details['translations']['id']))
                <h3>Sinopsis Bahasa Indonesia</h3>
                <p class="synopsis-text">
                    {{ $details['translations']['id']['data']['overview'] ?? $details['translations']['id']['overview'] ?? 'Sinopsis tidak tersedia dalam Bahasa Indonesia.' }}
                </p>
            @endif
        </div>

        <!-- Right Side: Top Cast (Oppa/Eonni) -->
        <div class="cast-section">
            <h3>Daftar Pemeran Utama</h3>
            <div class="cast-grid">
                @foreach(array_slice($details['cast'] ?? [], 0, 6) as $actor)
                    @php
                        $avatar = isset($actor['profile_path']) && !str_starts_with($actor['profile_path'], 'http')
                            ? "https://image.tmdb.org/t/p/w500" . $actor['profile_path']
                            : ($actor['profile_path'] ?? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=400');
                    @endphp
                    <div class="cast-card">
                        <div class="cast-avatar-wrapper">
                            <img src="{{ $avatar }}" class="cast-avatar" alt="{{ $actor['name'] }}">
                        </div>
                        <div class="cast-name">{{ $actor['name'] }}</div>
                        <div class="cast-character">{{ $actor['character'] ?? 'Pemeran' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- TV Series Episodes Grid Section -->
    @if(($details['media_type'] ?? null) === 'tv' && isset($seasonDetails))
        <section class="section-container" style="margin-top: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px;">
                <h2 class="section-title">Daftar Episode</h2>
                
                <form method="GET" action="/shows/tv/{{ $details['id'] }}">
                    <select name="season" onchange="this.form.submit()" style="background: #111; border: 1px solid var(--glass-border); color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer; outline: none;">
                        @for($s = 1; $s <= ($details['number_of_seasons'] ?? 1); $s++)
                            <option value="{{ $s }}" {{ $selectedSeasonNumber == $s ? 'selected' : '' }}>Season {{ $s }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @foreach($seasonDetails['episodes'] ?? [] as $episode)
                    @php
                        $epStill = !empty($episode['still_path'])
                            ? 'https://image.tmdb.org/t/p/w500' . $episode['still_path']
                            : 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=500';
                    @endphp
                    <div class="show-card" style="display: flex; flex-direction: row; gap: 15px; padding: 12px; height: auto;" 
                         onclick="location.href='/tv/{{ $details['id'] }}/watch/{{ $selectedSeasonNumber }}/{{ $episode['episode_number'] }}'">
                        <div style="width: 100px; height: 60px; flex-shrink: 0; border-radius: 6px; overflow: hidden; position: relative;">
                            <img src="{{ $epStill }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Episode {{ $episode['episode_number'] }}">
                            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3);">
                                <i class="fa-solid fa-play" style="color: white; font-size: 14px;"></i>
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="color: var(--primary); font-size: 11px; font-weight: 700;">EPISODE {{ $episode['episode_number'] }}</div>
                            <div style="font-weight: 700; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">{{ $episode['name'] ?? 'Episode ' . $episode['episode_number'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Recommendations Carousel Section -->
    <section class="section-container">
        <h2 class="section-title">Rekomendasi Konten Sejenis</h2>
        <div class="carousel-row">
            @if(empty($recommendations))
                <div style="text-align: center; width: 100%; color: var(--text-muted); padding: 30px;">
                    Tidak ada rekomendasi konten serupa yang tersedia saat ini.
                </div>
            @else
                @foreach($recommendations as $rec)
                    @php
                        $recPoster = isset($rec['poster_path']) && !str_starts_with($rec['poster_path'], 'http')
                            ? "https://image.tmdb.org/t/p/w500" . $rec['poster_path']
                            : ($rec['poster_path'] ?? 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400');
                        $recType = $rec['media_type'] ?? ($details['media_type'] ?? 'movie');
                    @endphp
                    <a href="/shows/{{ $recType }}/{{ $rec['id'] }}" class="show-card">
                        <div class="badge-rating">
                            <i class="fa-solid fa-star"></i> {{ number_format($rec['vote_average'], 1) }}
                        </div>
                        <div class="card-img-container">
                            <img src="{{ $recPoster }}" class="card-img" alt="Poster">
                        </div>
                        <div class="card-details">
                            <div class="card-title">{{ $rec['title'] ?? $rec['name'] ?? 'K-Content' }}</div>
                            <div class="card-meta">
                                <span>{{ substr($rec['release_date'] ?? $rec['first_air_date'] ?? '2024', 0, 4) }}</span>
                                <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 3000) }}k</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </section>

    <!-- Fullscreen Video Player Modal -->
    <div id="videoPlayerOverlay" class="video-player-overlay">
        <div class="video-player-container">
            <div class="close-player" onclick="closePlayer()">
                <i class="fa-solid fa-xmark"></i>
            </div>
            
            <div id="ytPlayer"></div>
            
            <button id="skipIntroBtn" class="player-btn skip-intro-btn">
                <i class="fa-solid fa-angles-right"></i> Skip Intro
            </button>
            
            <button id="nextEpisodeBtn" class="player-btn next-episode-btn">
                <i class="fa-solid fa-forward-step"></i> Next Episode
            </button>
        </div>
    </div>

    <footer>
        <div class="logo">
            <i class="fa-solid fa-play"></i> Mubee
        </div>
        <p>© 2026 Mubee. Dibuat dengan cinta untuk seluruh pencinta K-Drama di Indonesia.</p>
        <p style="font-size: 11px; margin-top: 5px; opacity: 0.6;">Didukung oleh TMDB API Integration.</p>
    </footer>

    <!-- YouTube IFrame Player API Script (Asynchronous Load) -->
    <script src="https://www.youtube.com/iframe_api"></script>

    <!-- Client-side Scripts -->
    <script>
        let player = null;
        let progressInterval = null;
        let activeShow = {
            id: null,
            type: null,
            episodeId: null,
            youtubeKey: null,
            introStart: 10,
            introEnd: 40,
            nextEpisodeId: null,
            duration: 0
        };

        function onYouTubeIframeAPIReady() {
            console.log("YouTube Player API Ready.");
        }

        // Increment views in database via AJAX
        function incrementView(tmdbId, episodeId = null) {
            const payload = {
                tmdb_id: tmdbId,
                episode_id: episodeId
            };

            return fetch('/api/views/increment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                console.log(`Views updated. Total views: ${data.data.views_count}`);
            })
            .catch(err => {
                console.error("Gagal menambah data view:", err);
            });
        }

        // Initialize and Play movie or series episode
        function playMovie(type, id, episodeId = null) {
            incrementView(id, episodeId);

            if (type === 'tv') {
                let season = 1;
                let episode = 1;
                if (episodeId) {
                    const match = episodeId.toString().match(/S(\d+)E(\d+)/i);
                    if (match) {
                        season = parseInt(match[1]);
                        episode = parseInt(match[2]);
                    } else if (!isNaN(parseInt(episodeId))) {
                        episode = parseInt(episodeId);
                    }
                }
                window.location.href = `/tv/${id}/watch/${season}/${episode}`;
            } else {
                window.location.href = `/movies/${id}`;
            }
        }

        function initPlayer(youtubeKey, startSeconds) {
            document.getElementById('videoPlayerOverlay').style.display = 'flex';

            if (player) {
                player.destroy();
            }

            player = new YT.Player('ytPlayer', {
                videoId: youtubeKey,
                playerVars: {
                    'autoplay': 1,
                    'controls': 1,
                    'rel': 0,
                    'showinfo': 0,
                    'start': startSeconds
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            activeShow.duration = player.getDuration();
            
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            progressInterval = setInterval(trackPlayback, 1000);
        }

        function onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                saveProgress(activeShow.duration, true);
                if (activeShow.nextEpisodeId) {
                    playNextEpisode();
                } else {
                    closePlayer();
                }
            }
        }

        function trackPlayback() {
            if (!player || typeof player.getCurrentTime !== 'function') return;
            
            const currentTime = Math.floor(player.getCurrentTime());
            activeShow.duration = player.getDuration();

            // A. Manage Skip Intro Button
            const skipBtn = document.getElementById('skipIntroBtn');
            if (currentTime >= activeShow.introStart && currentTime <= activeShow.introEnd) {
                skipBtn.style.display = 'flex';
                skipBtn.onclick = function() {
                    player.seekTo(activeShow.introEnd, true);
                    skipBtn.style.display = 'none';
                };
            } else {
                skipBtn.style.display = 'none';
            }

            // B. Manage Next Episode Button
            const nextBtn = document.getElementById('nextEpisodeBtn');
            if (activeShow.type === 'tv' && activeShow.nextEpisodeId && (activeShow.duration - currentTime <= 20)) {
                nextBtn.style.display = 'flex';
                nextBtn.onclick = playNextEpisode;
            } else {
                nextBtn.style.display = 'none';
            }

            // C. Auto-save progress every 5 seconds
            if (currentTime % 5 === 0 && currentTime > 0) {
                saveProgress(currentTime, false);
            }
        }

        function saveProgress(seconds, isFinished = false) {
            const payload = {
                tmdb_id: activeShow.id,
                episode_id: activeShow.episodeId,
                last_position_seconds: seconds,
                is_finished: isFinished,
                media_type: activeShow.type
            };

            fetch('/api/video-progress/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                console.log("Auto-saved position:", data);
            })
            .catch(err => {
                console.error("Gagal menyimpan progress:", err);
            });
        }

        function playNextEpisode() {
            alert(`Memutar episode berikutnya: ${activeShow.nextEpisodeId}`);
            playMovie(activeShow.type, activeShow.id, activeShow.nextEpisodeId);
        }

        function closePlayer() {
            if (player && typeof player.getCurrentTime === 'function') {
                const currentTime = Math.floor(player.getCurrentTime());
                saveProgress(currentTime, false);
                player.stopVideo();
            }

            clearInterval(progressInterval);
            document.getElementById('videoPlayerOverlay').style.display = 'none';
        }

        function formatSeconds(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        }

        function toggleMyListDetail(element, tmdbId, mediaType, title, posterPath, voteAverage) {
            const isAdded = element.getAttribute('data-added') === 'true';
            
            fetch("/my-list/toggle", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tmdb_id: tmdbId,
                    media_type: mediaType,
                    title: title,
                    poster_path: posterPath,
                    vote_average: voteAverage
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.added) {
                        element.setAttribute('data-added', 'true');
                        element.innerHTML = '<i class="fa-solid fa-check"></i> Di Daftar Tontonan';
                        element.style.borderColor = 'var(--primary)';
                        element.style.color = 'var(--primary)';
                    } else {
                        element.setAttribute('data-added', 'false');
                        element.innerHTML = '<i class="fa-solid fa-plus"></i> Tambah ke Daftar Tontonan';
                        element.style.borderColor = '';
                        element.style.color = '';
                    }
                }
            })
            .catch(err => console.error("Error toggling watchlist: ", err));
        }
    </script>
</body>
</html>
