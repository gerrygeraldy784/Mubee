@extends('layouts.app')

@section('title', 'Popular Actors & Actresses - Mubee')

@section('styles')
    /* Actors Page Custom Styling */
    .actors-hero {
        position: relative;
        background: linear-gradient(135deg, rgba(20, 20, 28, 0.9) 0%, rgba(10, 10, 15, 0.95) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 18px;
        padding: clamp(20px, 4vw, 35px) clamp(20px, 4vw, 40px);
        margin: 0 clamp(16px, 4vw, 48px) 30px clamp(16px, 4vw, 48px);
        backdrop-filter: blur(15px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        overflow: hidden;
    }

    .actors-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 42, 84, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }

    .actors-hero-text h1 {
        font-family: var(--heading-family);
        font-size: clamp(24px, 4vw, 34px);
        font-weight: 800;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .actors-hero-text p {
        color: var(--text-muted);
        font-size: 15px;
        max-width: 550px;
    }

    .actors-filter-bar {
        padding: 0 clamp(16px, 4vw, 48px);
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .actor-pills-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .actor-pill-btn {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        color: var(--text-color);
        padding: 10px 22px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .actor-pill-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 42, 84, 0.5);
        transform: translateY(-2px);
    }

    .actor-pill-btn.active {
        background: var(--primary-gradient);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
    }

    .actor-search-box {
        position: relative;
        width: 280px;
    }

    .actor-search-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 10px 18px 10px 42px;
        color: white;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    .actor-search-input:focus {
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 15px rgba(255, 42, 84, 0.2);
    }

    .actor-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 14px;
    }

    .actors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 25px;
        padding: 0 6%;
        margin-bottom: 50px;
    }

    .actor-card {
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 24px 18px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        position: relative;
    }

    .actor-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: rgba(255, 42, 84, 0.5);
        box-shadow: 0 12px 30px rgba(0,0,0,0.6), 0 0 20px rgba(255, 42, 84, 0.15);
    }

    .actor-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    .badge-aktris {
        background: rgba(236, 72, 153, 0.2);
        color: #f472b6;
        border: 1px solid rgba(236, 72, 153, 0.4);
    }

    .badge-aktor {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.4);
    }

    .actor-img-container {
        width: 125px;
        height: 125px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 16px auto;
        border: 3px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    .actor-card:hover .actor-img-container {
        border-color: var(--primary);
        transform: scale(1.06);
    }

    .actor-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .actor-name {
        font-family: var(--heading-family);
        font-weight: 700;
        font-size: 16px;
        color: var(--text-color);
        margin-bottom: 4px;
    }

    .actor-role {
        font-size: 12px;
        color: var(--text-muted);
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
        border-radius: 18px;
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
@endsection

@section('content')

    <!-- Hero Header Banner -->
    <div class="actors-hero">
        <div class="actors-hero-text">
            <h1><i class="fa-solid fa-star" style="color: var(--primary);"></i> Bintang Utama K-Drama & K-Movie</h1>
            <p>Jelajahi jajaran Aktris & Aktor populer Korea Selatan favorit Anda beserta daftar karya film dan drama terbaiknya.</p>
        </div>
    </div>

    <!-- API Warning Banner -->
    @if($isMocked)
        <div class="api-warning">
            <div class="warning-content">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div class="warning-text">
                    <h4>Koneksi TMDB API Belum Dikonfigurasi</h4>
                    <p>Menampilkan data demonstrasi (mock) untuk Aktor & Aktris Populer Korea.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Category Pills & Search Filter Bar -->
    <div class="actors-filter-bar">
        <div class="actor-pills-group">
            <button type="button" class="actor-pill-btn active" onclick="filterActorCategory('all', this)">
                <i class="fa-solid fa-users"></i> Semua Bintang ({{ count($actorsList) }})
            </button>
            <button type="button" class="actor-pill-btn" onclick="filterActorCategory('female', this)">
                <i class="fa-solid fa-person-dress"></i> Aktris Korea
            </button>
            <button type="button" class="actor-pill-btn" onclick="filterActorCategory('male', this)">
                <i class="fa-solid fa-person"></i> Aktor Korea
            </button>
        </div>
        <div class="actor-search-box">
            <i class="fa-solid fa-magnifying-glass actor-search-icon"></i>
            <input type="text" class="actor-search-input" placeholder="Cari nama aktor / aktris..." oninput="searchActorByName(this.value)">
        </div>
    </div>

    <!-- Actors Grid Showcase -->
    <div class="actors-grid" id="actorsGrid">
        @foreach($actorsList as $actor)
            @php
                $profile = isset($actor['profile_path']) && !str_starts_with($actor['profile_path'], 'http')
                    ? "https://image.tmdb.org/t/p/w500" . $actor['profile_path']
                    : ($actor['profile_path'] ?? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=400');
                $gender = $actor['gender'] ?? 2;
                $genderKey = $gender == 1 ? 'female' : 'male';
                $roleLabel = $gender == 1 ? 'Aktris' : 'Aktor';
            @endphp
            <div class="actor-card" 
                 data-gender="{{ $genderKey }}" 
                 data-name="{{ strtolower($actor['name']) }}"
                 onclick="showActorCredits('{{ addslashes($actor['name']) }}', {{ json_encode($actor['credits'] ?? []) }})">
                
                <div class="actor-badge {{ $gender == 1 ? 'badge-aktris' : 'badge-aktor' }}">
                    {{ $roleLabel }}
                </div>

                <div class="actor-img-container">
                    <img src="{{ $profile }}" class="actor-img" alt="{{ $actor['name'] }}">
                </div>
                <div class="actor-name">{{ $actor['name'] }}</div>
                <div class="actor-role">{{ $actor['known_for_department'] ?? ($gender == 1 ? 'Aktris Korea' : 'Aktor Korea') }}</div>
            </div>
        @endforeach
    </div>

    <!-- Starred Works Overlay Modal -->
    <div id="actorWorksOverlay" class="actor-works-overlay" onclick="closeActorOverlay(event)">
        <div class="overlay-box" onclick="event.stopPropagation()">
            <div class="overlay-header">
                <h3 id="overlayActorName">Karya Terbaik Aktor/Aktris</h3>
                <div class="close-overlay" onclick="document.getElementById('actorWorksOverlay').style.display = 'none'">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
            <div class="overlay-body">
                <div class="credits-grid" id="overlayCreditsContainer">
                    <!-- Credits dynamically inserted -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentGenderCategory = 'all';
        let currentSearchQuery = '';

        function filterActorCategory(category, btn) {
            document.querySelectorAll('.actor-pill-btn').forEach(el => el.classList.remove('active'));
            if (btn) btn.classList.add('active');

            currentGenderCategory = category;
            applyActorFilters();
        }

        function searchActorByName(query) {
            currentSearchQuery = query.toLowerCase().trim();
            applyActorFilters();
        }

        function applyActorFilters() {
            const cards = document.querySelectorAll('#actorsGrid .actor-card');
            cards.forEach(card => {
                const cardGender = card.getAttribute('data-gender');
                const cardName = card.getAttribute('data-name');

                const matchesGender = (currentGenderCategory === 'all' || cardGender === currentGenderCategory);
                const matchesSearch = (!currentSearchQuery || cardName.includes(currentSearchQuery));

                if (matchesGender && matchesSearch) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.3s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function showActorCredits(name, credits) {
            document.getElementById('overlayActorName').innerText = `Karya Terbaik ${name}`;
            const container = document.getElementById('overlayCreditsContainer');
            container.innerHTML = '';

            if (!credits || credits.length === 0) {
                container.innerHTML = '<p style="color: var(--text-muted); grid-column: 1/-1; text-align: center;">Tidak ada informasi karya tersedia.</p>';
            } else {
                credits.forEach(work => {
                    const title = work.title || work.name || 'K-Content';
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
            }

            document.getElementById('actorWorksOverlay').style.display = 'flex';
        }

        function closeActorOverlay(event) {
            document.getElementById('actorWorksOverlay').style.display = 'none';
        }
    </script>
@endsection
