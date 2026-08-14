@extends('layouts.app')

@section('title', 'Kategori Genre - Mubee')

@section('styles')
    /* Genres Page Custom Styling */
    .genres-hero {
        position: relative;
        background: linear-gradient(135deg, rgba(20, 20, 28, 0.9) 0%, rgba(10, 10, 15, 0.95) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 18px;
        padding: clamp(20px, 4vw, 35px) clamp(20px, 4vw, 40px);
        margin: 0 clamp(16px, 4vw, 48px) 35px clamp(16px, 4vw, 48px);
        backdrop-filter: blur(15px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        overflow: hidden;
    }

    .genres-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 42, 84, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }

    .genres-hero-text h1 {
        font-family: var(--heading-family);
        font-size: clamp(24px, 4vw, 36px);
        font-weight: 800;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .genres-hero-text p {
        color: var(--text-muted);
        font-size: 15px;
        max-width: 550px;
    }

    .genre-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 180px), 1fr));
        gap: 20px;
        padding: 0 clamp(16px, 4vw, 48px);
        margin-bottom: 35px;
    }

    .genre-card-box {
        position: relative;
        height: 120px;
        border-radius: 16px;
        overflow: hidden;
        padding: 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }

    .genre-card-box:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 12px 30px rgba(0,0,0,0.7);
        border-color: rgba(255,255,255,0.3);
    }

    .genre-card-box.active-genre {
        outline: 3px solid var(--primary);
        box-shadow: 0 0 25px rgba(255, 42, 84, 0.5);
    }

    .genre-card-icon {
        font-size: 28px;
        opacity: 0.95;
    }

    .genre-card-name {
        font-family: var(--heading-family);
        font-size: 18px;
        font-weight: 800;
        text-shadow: 0 2px 6px rgba(0,0,0,0.5);
    }

    /* Horizontal Pills Section */
    .genre-pills-container {
        padding: 0 6%;
        margin-bottom: 30px;
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 10px;
    }

    .genre-pills-container::-webkit-scrollbar {
        height: 4px;
    }

    .genre-pill-btn {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        color: var(--text-color);
        padding: 10px 22px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .genre-pill-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 42, 84, 0.5);
        transform: translateY(-2px);
    }

    .genre-pill-btn.active {
        background: var(--primary-gradient);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
    }

    .active-genre-title {
        padding: 0 6%;
        margin-bottom: 25px;
        font-family: var(--heading-family);
        font-size: 24px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
@endsection

@section('content')

    <!-- Hero Header Banner -->
    <div class="genres-hero">
        <div class="genres-hero-text">
            <h1><i class="fa-solid fa-layer-group" style="color: var(--primary);"></i> Jelajahi Kategori Genre</h1>
            <p>Pilih genre favorit Anda di bawah ini untuk menemukan koleksi Drama & Film Korea terbaik sesuai keinginan Anda.</p>
        </div>
    </div>

    <!-- API Warning Banner -->
    @if($isMocked)
        <div class="api-warning">
            <div class="warning-content">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div class="warning-text">
                    <h4>Koneksi TMDB API Belum Dikonfigurasi</h4>
                    <p>Menampilkan data demonstrasi (mock) untuk kategori genre.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Visual Genre Category Cards Grid -->
    <div class="genre-cards-grid">
        @foreach($genres as $g)
            <div class="genre-card-box {{ $activeGenre == $g['id'] ? 'active-genre' : '' }}" 
                 style="background: {{ $g['gradient'] }};" 
                 onclick="selectGenreFilter('{{ $g['id'] }}', this)">
                <i class="fa-solid {{ $g['icon'] }} genre-card-icon"></i>
                <div class="genre-card-name">{{ $g['name'] }}</div>
            </div>
        @endforeach
    </div>

    <!-- Active Genre Subtitle Header -->
    <div class="active-genre-title">
        <span id="currentGenreLabel">
            <i class="fa-solid fa-film" style="color: var(--primary); margin-right: 8px;"></i> 
            <span id="labelText">Semua Genre</span>
        </span>
        <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;" id="contentCounter">
            Menampilkan {{ count($uniqueContent) }} Judul
        </span>
    </div>

    <!-- Content Shows Grid -->
    <div class="shows-grid" id="genreShowsGrid">
        @foreach($uniqueContent as $item)
            @php
                $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                    ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                    : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400');
                $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                $isAdded = in_array($item['id'], $myListIds);
                $genreStr = implode(',', $item['genre_ids'] ?? []);
                $displayTitle = $item['title'] ?? $item['name'] ?? 'K-Content';
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

                <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                    <img src="{{ $poster }}" class="card-img" alt="Poster">
                </div>
                <div class="card-details">
                    <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">{{ $displayTitle }}</div>
                    <div class="card-meta">
                        <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                        <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 4000) }}k</span>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="btn-my-list-toggle" 
                                data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, '{{ $mType }}', '{{ addslashes($displayTitle) }}', '{{ $item['poster_path'] ?? null }}', {{ $item['vote_average'] ?? 0 }})">
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

@endsection

@section('scripts')
    <script>
        function selectGenreFilter(genreId, cardEl) {
            // Update active state on visual cards
            document.querySelectorAll('.genre-card-box').forEach(box => box.classList.remove('active-genre'));
            if (cardEl) {
                cardEl.classList.add('active-genre');
            }

            // Filter show cards
            const cards = document.querySelectorAll('#genreShowsGrid .show-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardGenres = (card.getAttribute('data-genres') || '').split(',');
                if (genreId === 'all' || cardGenres.includes(genreId.toString())) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.3s ease-out';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update label and counter
            const genreNames = {
                'all': 'Semua Genre',
                '10749': 'Romantis',
                '18': 'Drama',
                '28': 'Aksi',
                '35': 'Komedi',
                '9648': 'Misteri & Thriller',
                '10765': 'Fantasi',
                '27': 'Horor'
            };

            const labelText = genreNames[genreId] || 'Genre Pilihan';
            document.getElementById('labelText').innerText = labelText;
            document.getElementById('contentCounter').innerText = `Menampilkan ${visibleCount} Judul`;
        }

        // Initialize if activeGenre is provided in query string
        document.addEventListener('DOMContentLoaded', function() {
            const activeG = "{{ $activeGenre }}";
            if (activeG && activeG !== 'all') {
                const targetCard = Array.from(document.querySelectorAll('.genre-card-box')).find(el => el.getAttribute('onclick').includes(`'${activeG}'`));
                selectGenreFilter(activeG, targetCard);
            }
        });
    </script>
@endsection
