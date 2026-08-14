@extends('layouts.app')

@section('title', 'Mubee - Premium K-Drama & K-Movie Streaming')

@section('styles')
    /* Hero Banner Responsive & Zoom Resilience */
    .hero {
    position: relative;
    min-height: clamp(420px, 65vh, 650px);
    height: auto;
    width: 100%;
    display: flex;
    align-items: center;
    padding: clamp(30px, 6vh, 60px) clamp(16px, 5vw, 48px);
    overflow: hidden;
    margin-bottom: 40px;
    margin-top: -100px; /* pull up under transparent header */
    }

    .hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -2;
    transform: scale(1.05);
    filter: brightness(0.45);
    }

    .hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(8,8,10,0.95) 30%, rgba(8,8,10,0.5) 60%, rgba(8,8,10,0) 100%),
    linear-gradient(to top, rgba(8,8,10,1) 0%, rgba(8,8,10,0) 25%);
    z-index: -1;
    }

    .hero-content {
    max-width: 650px;
    width: 100%;
    z-index: 10;
    animation: fadeInUp 0.8s ease-out;
    padding-top: 60px;
    }

    .hero-badge {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-border);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    backdrop-filter: blur(5px);
    }

    .hero-badge i {
    color: #ffd700;
    }

    .hero-title {
    font-family: var(--heading-family);
    font-size: clamp(28px, 5vw, 56px);
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 20px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    word-break: break-word;
    }

    .hero-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    font-size: 14px;
    }

    .hero-rating {
    color: #ffd700;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 5px;
    }

    .hero-year {
    opacity: 0.8;
    font-weight: 500;
    }

    .hero-desc {
    font-size: clamp(14px, 1.5vw, 16px);
    color: var(--text-muted);
    margin-bottom: 30px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-shadow: 0 1px 4px rgba(0,0,0,0.4);
    }

    .hero-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    }

    .btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    }

    .btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
    }

    .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 42, 84, 0.6);
    }

    .btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    backdrop-filter: blur(5px);
    border: 1px solid var(--glass-border);
    }

    .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    }

    .section-container {
    padding: 0 clamp(16px, 4vw, 48px);
    margin-bottom: 45px;
    }

    .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
    }

    .section-title {
    font-family: var(--heading-family);
    font-size: clamp(18px, 3vw, 24px);
    font-weight: 700;
    position: relative;
    padding-left: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
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
    gap: 16px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding-bottom: 15px;
    -webkit-overflow-scrolling: touch;
    }

    .carousel-row::-webkit-scrollbar {
    height: 6px;
    }

    .show-card {
    flex: 0 0 clamp(160px, 20vw, 220px);
    }

    .top10-rank-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--primary-gradient);
    color: #ffffff;
    font-family: var(--heading-family);
    font-weight: 800;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 6px;
    z-index: 5;
    box-shadow: 0 4px 12px rgba(255, 42, 84, 0.5);
    display: flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.5px;
    }

    /* Single Genre Dropdown Styling */
    .genre-dropdown-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 260px;
    }

    .genre-dropdown-icon {
    position: absolute;
    left: 16px;
    color: var(--primary);
    font-size: 15px;
    pointer-events: none;
    z-index: 2;
    }

    .genre-dropdown-arrow {
    position: absolute;
    right: 16px;
    color: var(--text-muted);
    font-size: 13px;
    pointer-events: none;
    z-index: 2;
    transition: transform 0.2s ease;
    }

    .genre-select {
    width: 100%;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 12px 40px 12px 42px;
    color: var(--text-color);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    }

    .genre-select:hover, .genre-select:focus {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 42, 84, 0.5);
    box-shadow: 0 0 15px rgba(255, 42, 84, 0.2);
    }

    .genre-select option {
    background: #141419;
    color: #f3f4f6;
    padding: 10px;
    }

    /* Oppa/Eonni Cast Filter Section */
    .actors-row {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding-bottom: 10px;
    }

    .actor-card {
    flex: 0 0 160px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    }

    .actor-img-container {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 12px auto;
    border: 3px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .actor-card:hover .actor-img-container {
    border-color: var(--primary);
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(255, 42, 84, 0.4);
    }

    .actor-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    }

    .actor-name {
    font-family: var(--heading-family);
    font-weight: 600;
    font-size: 14px;
    color: var(--text-color);
    }

    .actor-role {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 2px;
    }

    /* Continue Watching Box */
    .continue-watching-box {
    background: linear-gradient(135deg, rgba(30, 20, 25, 0.8) 0%, rgba(15, 15, 20, 0.8) 100%);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    backdrop-filter: blur(10px);
    margin-top: 10px;
    }

    .cw-left {
    display: flex;
    align-items: center;
    gap: 15px;
    }

    .cw-thumb {
    width: 100px;
    height: 56px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid var(--glass-border);
    }

    .cw-info h4 {
    font-family: var(--heading-family);
    font-weight: 700;
    font-size: 15px;
    margin-bottom: 2px;
    }

    .cw-info p {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 8px;
    }

    .progress-bar-container {
    width: 250px;
    height: 5px;
    background: rgba(255,255,255,0.1);
    border-radius: 5px;
    overflow: hidden;
    }

    .progress-bar {
    height: 100%;
    width: 65%;
    background: var(--primary-gradient);
    border-radius: 5px;
    }

    .cw-play-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: white;
    color: black;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .cw-play-btn:hover {
    transform: scale(1.1);
    background: var(--primary);
    color: white;
    }

    /* Starred Works Overlay Modal */
    .actor-works-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(15px);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 30px;
    }

    .overlay-box {
    background: #101015;
    border: 1px solid var(--glass-border);
    width: 100%;
    max-width: 800px;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: 85vh;
    box-shadow: 0 15px 40px rgba(0,0,0,0.7);
    }

    .overlay-header {
    padding: 24px;
    border-bottom: 1px solid var(--glass-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    .overlay-header h3 {
    font-family: var(--heading-family);
    font-size: 22px;
    font-weight: 700;
    }

    .close-overlay {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: all 0.2s ease;
    }

    .close-overlay:hover {
    background: var(--primary);
    border-color: var(--primary);
    }

    .overlay-body {
    padding: 24px;
    overflow-y: auto;
    }

    .credits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
    }

    .credits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .credit-item {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
    }

    .credit-item:hover {
        border-color: var(--primary);
        background: rgba(255, 42, 84, 0.08);
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(255, 42, 84, 0.25);
    }

    .credit-poster-wrapper {
        width: 100%;
        height: 150px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 12px;
        position: relative;
        background: rgba(0, 0, 0, 0.3);
    }

    .credit-poster-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .credit-item:hover .credit-poster-img {
        transform: scale(1.08);
    }

    .credit-type-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(10, 10, 15, 0.85);
        color: var(--primary);
        border: 1px solid rgba(255, 42, 84, 0.4);
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        backdrop-filter: blur(4px);
    }

    .credit-play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .credit-item:hover .credit-play-overlay {
        opacity: 1;
    }

    .credit-play-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 15px;
        box-shadow: 0 4px 15px rgba(255, 42, 84, 0.5);
    }

    .credit-info {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .credit-title {
        font-family: var(--heading-family);
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 4px;
        color: #ffffff;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .credit-character {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .credit-action-btn {
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 8px 12px;
        background: rgba(255, 42, 84, 0.12);
        color: var(--primary);
        border: 1px solid rgba(255, 42, 84, 0.3);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .credit-item:hover .credit-action-btn {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Video Player Overlay */
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
    display: none;
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
    transform: translateY(-2px);
    }

    .next-episode-btn {
    bottom: 80px;
    right: 180px;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    gap: 8px;
    }

    .next-episode-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 42, 84, 0.5);
    }

    /* Pull headers styling down specifically since page-padding is added globally */
    header {
    background: linear-gradient(to bottom, rgba(8, 8, 10, 0.95) 0%, rgba(8, 8, 10, 0) 100%) !important;
    border-bottom: none !important;
    backdrop-filter: none !important;
    }
@endsection

@section('content')
    <!-- Hero Banner Section -->
    @if($heroItem)
        <section class="hero">
            @php
                $backdrop = isset($heroItem['backdrop_path']) && !str_starts_with($heroItem['backdrop_path'], 'http')
                    ? "https://image.tmdb.org/t/p/original" . $heroItem['backdrop_path']
                    : ($heroItem['backdrop_path'] ?? 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=1200');
                $heroMType = $heroItem['media_type'] ?? 'movie';
                $heroIsAdded = in_array($heroItem['id'], $myListIds);
            @endphp
            <img src="{{ $backdrop }}" class="hero-bg" alt="Hero Background">
            <div class="hero-overlay"></div>

            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fa-solid fa-fire"></i> Trending #1 pekan ini
                </div>
                <h1 class="hero-title">{{ $heroItem['title'] ?? $heroItem['name'] ?? 'K-Show' }}</h1>
                <div class="hero-meta">
                    <span class="hero-rating"><i class="fa-solid fa-star"></i> {{ number_format($heroItem['vote_average'], 1) }}
                        IMDB</span>
                    <span
                        class="hero-year">{{ substr($heroItem['release_date'] ?? $heroItem['first_air_date'] ?? '2024', 0, 4) }}</span>
                    <span class="hero-badge" style="margin-bottom:0px; padding: 2px 8px; font-size:10px;">KR</span>
                </div>
                <p class="hero-desc">{{ $heroItem['overview'] ?? 'Deskripsi tidak tersedia.' }}</p>
                <div class="hero-btns">
                    <button class="btn btn-primary" onclick="playMovie('{{ $heroMType }}', {{ $heroItem['id'] }})">
                        <i class="fa-solid fa-play"></i> Putar Sekarang
                    </button>
                    <a href="/shows/{{ $heroMType }}/{{ $heroItem['id'] }}" class="btn btn-secondary">
                        <i class="fa-solid fa-circle-info"></i> Info Film
                    </a>
                    <button type="button" class="btn btn-secondary" data-added="{{ $heroIsAdded ? 'true' : 'false' }}"
                        style="{{ $heroIsAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                        onclick="toggleMyListAjax(this, {{ $heroItem['id'] }}, '{{ $heroMType }}', '{{ addslashes($heroItem['title'] ?? $heroItem['name'] ?? 'K-Content') }}', '{{ $heroItem['poster_path'] ?? null }}', {{ $heroItem['vote_average'] ?? 0 }})">
                        @if($heroIsAdded)
                            <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                        @else
                            <i class="fa-solid fa-plus"></i> Daftar Tontonan
                        @endif
                    </button>
                </div>
            </div>
        </section>
    @endif

    <!-- API Connection Warning Banner -->
    @if($isMocked)
        <div class="api-warning">
            <div class="warning-content">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div class="warning-text">
                    <h4>Koneksi TMDB API Belum Dikonfigurasi</h4>
                    <p>Halaman saat ini menampilkan data demonstrasi (mock). Harap lengkapi kunci API di file <code>.env</code>
                        Anda untuk memuat konten nyata secara real-time.</p>
                </div>
            </div>
            <a href="#" class="btn btn-secondary" style="padding: 6px 14px; font-size: 12px; border-radius: 6px;">
                Cara Konfigurasi
            </a>
        </div>
    @endif

    <!-- Continue Watching Section -->
    @if($continueWatching)
        <section class="section-container">
            <div class="section-title">Lanjutkan Menonton</div>
            <div class="continue-watching-box" style="flex-direction: column; align-items: stretch; gap: 15px;">
                <div
                    style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; width: 100%;">
                    <div class="cw-left" style="flex: 1; min-width: 0;">
                        @php
                            $cwBackdrop = isset($continueWatching['backdrop']) && !str_starts_with($continueWatching['backdrop'], 'http')
                                ? "https://image.tmdb.org/t/p/w500" . $continueWatching['backdrop']
                                : ($continueWatching['backdrop'] ?? 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400');
                        @endphp
                        <img src="{{ $cwBackdrop }}" class="cw-thumb" alt="Continue Watching Thumbnail">
                        <div class="cw-info" style="flex: 1; min-width: 0;">
                            <h4>{{ $continueWatching['title'] }}</h4>
                            <p
                                style="color: var(--primary); font-weight: 700; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                @if($continueWatching['episode_id'])
                                    Episode {{ $continueWatching['episode_id'] }}:
                                    {{ $continueWatching['episode_name'] ?? 'Episode Baru' }}
                                @else
                                    Film (Movie)
                                @endif
                                <span style="color: var(--text-muted); font-weight: 500;">• Progress:
                                    {{ $continueWatching['last_position_formatted'] }}</span>
                            </p>
                            <div class="progress-bar-container" style="margin-top: 8px;">
                                <div class="progress-bar" style="width: {{ $continueWatching['percentage'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="cw-play-btn"
                        onclick="playMovie('{{ $continueWatching['type'] }}', {{ $continueWatching['tmdb_id'] }}, '{{ $continueWatching['episode_id'] }}')">
                        <i class="fa-solid fa-play"></i>
                    </div>
                </div>

                @if(!empty($continueWatching['episode_overview']) || !empty($continueWatching['overview']))
                    <div style="border-top: 1px solid var(--glass-border); padding-top: 12px;">
                        <span
                            style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary); font-weight: 700; display: block; margin-bottom: 4px;">Sinopsis
                            Episode/Film</span>
                        <p
                            style="font-size: 13px; color: var(--text-muted); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $continueWatching['episode_overview'] ?? $continueWatching['overview'] }}
                        </p>
                    </div>
                @endif
            </div>
        </section>
    @endif


    <!-- Top 10 Trending Row -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">
                <i class="fa-solid fa-fire" style="color: var(--primary);"></i> Top 10 Film & Drama Tren Hari Ini
            </div>
        </div>
        <div class="carousel-row">
            @foreach(array_slice($trendingList, 0, 10) as $index => $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="top10-rank-badge">
                        #{{ $index + 1 }} TOP 10
                    </div>
                    <div class="badge-rating" style="left: auto; right: 12px;">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 3000) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Riwayat Tontonan Section (Terakhir Ditonton) -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Riwayat Tontonan (Terakhir Ditonton)
            </div>
        </div>
        <div class="carousel-row">
            @if(empty($watchHistoryList))
                <div style="width: 100%; padding: 35px 20px; text-align: center; background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%); border: 1px dashed var(--glass-border); border-radius: 12px; color: var(--text-muted);">
                    <i class="fa-solid fa-film" style="font-size: 32px; margin-bottom: 12px; color: var(--primary); display: block;"></i>
                    <h4 style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 6px;">Belum Ada Riwayat Tontonan</h4>
                    <p style="font-size: 13px; margin-bottom: 0;">Mulai tonton film atau drama korea favoritmu untuk mencatat riwayat di sini.</p>
                </div>
            @else
                @foreach($watchHistoryList as $item)
                    @php
                        $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                            ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                            : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1627856013091-fed6e4e30025?q=80&w=400');
                        $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                        $isAdded = in_array($item['id'], $myListIds);
                        $genreStr = implode(',', $item['genre_ids'] ?? []);
                        $epStr = ($mType === 'tv' && !empty($item['episode_number'])) 
                            ? 'S' . sprintf('%02d', $item['season_number'] ?? 1) . 'E' . sprintf('%02d', $item['episode_number']) 
                            : null;
                    @endphp
                    <div class="show-card" data-genres="{{ $genreStr }}">
                        <div class="badge-rating" style="left: auto; right: 12px;">
                            <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                        </div>

                        <div class="card-badge" style="background: rgba(8, 8, 10, 0.85); backdrop-filter: blur(6px); border: 1px solid rgba(255, 42, 84, 0.5); color: #ff2a54;">
                            @if($mType === 'tv' && !empty($item['episode_number']))
                                S{{ $item['season_number'] ?? 1 }} E{{ $item['episode_number'] }}
                            @elseif($mType === 'tv')
                                Drama
                            @else
                                Movie
                            @endif
                        </div>

                        <div class="card-img-container" onclick="playMovie('{{ $mType }}', {{ $item['id'] }}, '{{ $epStr }}')">
                            <img src="{{ $poster }}" class="card-img" alt="Poster">
                        </div>
                        <div class="card-details">
                            <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                                {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}
                            </div>
                            <div class="card-meta">
                                <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                                <span class="card-views" style="color: var(--primary); font-weight: 600;">
                                    <i class="fa-solid fa-clock-rotate-left"></i> {{ $item['last_watched_formatted'] ?? 'Terakhir Ditonton' }}
                                </span>
                            </div>
                            <div class="card-actions">
                                <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                    style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                    onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                    @if($isAdded)
                                        <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                    @else
                                        <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    <!-- Aktris & Aktor Korea Cast Filters Section -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">Aktris & Aktor Korea Favoritmu</div>
        </div>
        <div class="actors-row">
            @foreach($actorsList as $actor)
                @php
                    $profile = isset($actor['profile_path']) && !str_starts_with($actor['profile_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $actor['profile_path']
                        : ($actor['profile_path'] ?? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=400');
                @endphp
                <div class="actor-card"
                    onclick="showActorCredits('{{ $actor['name'] }}', {{ json_encode($actor['credits'] ?? []) }})">
                    <div class="actor-img-container">
                        <img src="{{ $profile }}" class="actor-img" alt="{{ $actor['name'] }}">
                    </div>
                    <div class="actor-name">{{ $actor['name'] }}</div>
                    <div class="actor-role">{{ $actor['known_for_department'] ?? 'Cast' }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- K-Dramas (TV Shows) Row -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">Serial Drama Korea (Rilis 2024-2025)</div>
        </div>
        <div class="carousel-row">
            @foreach($tvList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>

                    @if(isset($item['kdrama_status']))
                        @if($item['kdrama_status'] === 'Ongoing')
                            <div class="card-badge badge-ongoing">Ongoing</div>
                        @else
                            <div class="card-badge badge-completed">Completed</div>
                        @endif
                    @endif

                    <div class="card-img-container" onclick="location.href='/shows/tv/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/tv/{{ $item['id'] }}'">
                            {{ $item['name'] ?? 'K-Drama' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 3500) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, 'tv', '{{ addslashes($item['name'] ?? 'K-Drama') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- K-Dramas Terpopuler Sepanjang Masa Row -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">Drama Korea Terpopuler Sepanjang Masa</div>
        </div>
        <div class="carousel-row">
            @foreach($popularTvList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>

                    @if(isset($item['kdrama_status']))
                        @if($item['kdrama_status'] === 'Ongoing')
                            <div class="card-badge badge-ongoing">Ongoing</div>
                        @else
                            <div class="card-badge badge-completed">Completed</div>
                        @endif
                    @endif

                    <div class="card-img-container" onclick="location.href='/shows/tv/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/tv/{{ $item['id'] }}'">
                            {{ $item['name'] ?? 'K-Drama' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1000, 9999) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, 'tv', '{{ addslashes($item['name'] ?? 'K-Drama') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- K-Movies Row -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">Film Korea Pilihan (Rilis 2024-2025)</div>
        </div>
        <div class="carousel-row">
            @foreach($moviesList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/movie/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/movie/{{ $item['id'] }}'">
                            {{ $item['title'] ?? 'K-Movie' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 2500) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, 'movie', '{{ addslashes($item['title'] ?? 'K-Movie') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- K-Movies Terpopuler Row -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">Film Korea Terfavorit Pilihan Penonton</div>
        </div>
        <div class="carousel-row">
            @foreach($popularMoviesList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/movie/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/movie/{{ $item['id'] }}'">
                            {{ $item['title'] ?? 'K-Movie' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1000, 8999) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, 'movie', '{{ addslashes($item['title'] ?? 'K-Movie') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 😱 Bikin Jantungan: Thriller & Horor -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">😱 Bikin Jantungan: Thriller & Horor</div>
        </div>
        <div class="carousel-row">
            @foreach($thrillerHorrorList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1500, 9500) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 🥰 Bikin Baper: Romansa Manis -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">🥰 Bikin Baper: Romansa Manis</div>
        </div>
        <div class="carousel-row">
            @foreach($romanceList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1800, 9900) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 🧠 Plot Twist Terbaik: Misteri & Teka-Teki -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">🧠 Plot Twist Terbaik: Misteri & Teka-Teki</div>
        </div>
        <div class="carousel-row">
            @foreach($mysteryList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1400, 9200) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 🤣 Tawa Tanpa Henti: Komedi Pilihan -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">🤣 Tawa Tanpa Henti: Komedi Pilihan</div>
        </div>
        <div class="carousel-row">
            @foreach($comedyList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1100, 8700) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 💥 Aksi Penuh Adrenalin (Action & Sci-Fi) -->
    <section class="section-container">
        <div class="section-header">
            <div class="section-title">💥 Aksi Penuh Adrenalin (Action & Sci-Fi)</div>
        </div>
        <div class="carousel-row">
            @foreach($actionList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                    $genreStr = implode(',', $item['genre_ids'] ?? []);
                @endphp
                <div class="show-card" data-genres="{{ $genreStr }}">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>
                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(1600, 9400) }}k</span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($item['title'] ?? $item['name'] ?? 'K-Content') }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
                                @if($isAdded)
                                    <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                                @else
                                    <i class="fa-solid fa-plus"></i> Daftar Tontonan
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Starred Works Overlay Modal -->
    <div id="actorWorksOverlay" class="actor-works-overlay" onclick="closeActorOverlay(event)">
        <div class="overlay-box" onclick="event.stopPropagation()">
            <div class="overlay-header">
                <h3 id="overlayActorName">Film & Drama Aktor/Aktris</h3>
                <div class="close-overlay" onclick="document.getElementById('actorWorksOverlay').style.display = 'none'">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
            <div class="overlay-body">
                <div class="credits-grid" id="overlayCreditsContainer">
                    <!-- Credits dynamically added here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Video Player Modal -->
    <div id="videoPlayerOverlay" class="video-player-overlay">
        <div class="video-player-container">
            <div class="close-player" onclick="closePlayer()">
                <i class="fa-solid fa-xmark"></i>
            </div>

            <!-- YouTube Iframe API container -->
            <div id="ytPlayer"></div>

            <!-- Floating skip intro button -->
            <button id="skipIntroBtn" class="player-btn skip-intro-btn">
                <i class="fa-solid fa-angles-right"></i> Skip Intro
            </button>

            <!-- Floating next episode button -->
            <button id="nextEpisodeBtn" class="player-btn next-episode-btn">
                <i class="fa-solid fa-forward-step"></i> Next Episode
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- YouTube IFrame Player API Script (Asynchronous Load) -->
    <script src="https://www.youtube.com/iframe_api"></script>
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

        function incrementView(tmdbId, episodeId = null) {
            const payload = { tmdb_id: tmdbId, episode_id: episodeId };
            return fetch('/api/views/increment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            }).catch(err => console.error("Gagal menambah data view: ", err));
        }

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
            if (player) { player.destroy(); }

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
            if (progressInterval) { clearInterval(progressInterval); }
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

            const skipBtn = document.getElementById('skipIntroBtn');
            if (currentTime >= activeShow.introStart && currentTime <= activeShow.introEnd) {
                skipBtn.style.display = 'flex';
                skipBtn.onclick = function () {
                    player.seekTo(activeShow.introEnd, true);
                    skipBtn.style.display = 'none';
                };
            } else {
                skipBtn.style.display = 'none';
            }

            const nextBtn = document.getElementById('nextEpisodeBtn');
            if (activeShow.type === 'tv' && activeShow.nextEpisodeId && (activeShow.duration - currentTime <= 20)) {
                nextBtn.style.display = 'flex';
                nextBtn.onclick = playNextEpisode;
            } else {
                nextBtn.style.display = 'none';
            }

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
            }).catch(err => console.error("Gagal menyimpan progress tontonan:", err));
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

        function showActorCredits(actorName, credits) {
            document.getElementById('overlayActorName').innerText = `Daftar Film & Drama: ${actorName}`;
            const container = document.getElementById('overlayCreditsContainer');
            container.innerHTML = '';

            if (credits && credits.length > 0) {
                credits.forEach(work => {
                    const title = work.title || work.name || 'Judul Tidak Diketahui';
                    const mediaType = work.media_type || (work.title ? 'movie' : 'tv');
                    const typeLabel = mediaType === 'movie' ? 'Film' : 'Drama';
                    const character = work.character || 'Pemeran Utama';

                    let posterUrl = 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400';
                    if (work.poster_path) {
                        posterUrl = work.poster_path.startsWith('http') 
                            ? work.poster_path 
                            : 'https://image.tmdb.org/t/p/w500' + work.poster_path;
                    } else if (work.backdrop_path) {
                        posterUrl = work.backdrop_path.startsWith('http')
                            ? work.backdrop_path
                            : 'https://image.tmdb.org/t/p/w500' + work.backdrop_path;
                    }

                    let targetUrl = '#';
                    if (work.id) {
                        targetUrl = `/shows/${mediaType}/${work.id}`;
                    } else {
                        targetUrl = `/search?q=${encodeURIComponent(title)}`;
                    }

                    const item = document.createElement('a');
                    item.href = targetUrl;
                    item.className = 'credit-item';
                    item.title = `Lihat ${title}`;
                    
                    item.innerHTML = `
                        <div class="credit-poster-wrapper">
                            <img src="${posterUrl}" class="credit-poster-img" alt="${title}" onerror="this.src='https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400'">
                            <span class="credit-type-badge">${typeLabel}</span>
                            <div class="credit-play-overlay">
                                <div class="credit-play-icon">
                                    <i class="fa-solid fa-play"></i>
                                </div>
                            </div>
                        </div>
                        <div class="credit-info">
                            <div class="credit-title">${title}</div>
                            <div class="credit-character">sebagai <strong>${character}</strong></div>
                        </div>
                        <div class="credit-action-btn">
                            <i class="fa-solid fa-circle-play"></i> Tonton Sekarang
                        </div>
                    `;

                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">Tidak ada daftar film/drama untuk aktor ini.</div>';
            }
            document.getElementById('actorWorksOverlay').style.display = 'flex';
        }

        function closeActorOverlay(event) {
            document.getElementById('actorWorksOverlay').style.display = 'none';
        }

        function filterGenreBySelect(genreId) {
            const cards = document.querySelectorAll('.show-card');
            cards.forEach(card => {
                const cardGenres = (card.getAttribute('data-genres') || '').split(',');
                if (genreId === 'all' || cardGenres.includes(genreId.toString())) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.3s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
@endsection