<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mubee - Premium K-Drama & K-Movie Streaming')</title>
    
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
            --primary: #ff2a54; /* Mubee Hot Pink */
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
            padding-top: 100px; /* Offset for fixed header */
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0d0d11;
        }
        ::-webkit-scrollbar-thumb {
            background: #2a2a35;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Header Navigation */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(8, 8, 10, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            transition: all 0.3s ease;
        }

        .logo {
            font-family: var(--heading-family);
            font-weight: 800;
            font-size: 28px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s ease;
            opacity: 0.8;
        }

        .nav-links a:hover, .nav-links a.active {
            opacity: 1;
            color: var(--primary);
        }

        .nav-genre-item {
            display: flex;
            align-items: center;
        }

        .nav-genre-select {
            background: transparent;
            border: none;
            color: var(--text-color);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            opacity: 0.8;
            transition: all 0.2s ease;
            font-family: var(--font-family);
        }

        .nav-genre-select:hover, .nav-genre-select:focus {
            opacity: 1;
            color: var(--primary);
        }

        .nav-genre-select option {
            background: #141419;
            color: #f3f4f6;
            padding: 10px;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-bar {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--glass-border);
            padding: 8px 16px;
            border-radius: 30px;
            color: white;
            outline: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-bar input {
            background: none;
            border: none;
            color: white;
            outline: none;
        }

        .header-btn {
            color: var(--text-color);
            font-weight: 600;
            font-size: 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--glass-border);
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .header-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* Movie/Drama Cards */
        .show-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .show-card:hover {
            transform: translateY(-8px) scale(1.03);
            border-color: rgba(255, 42, 84, 0.5);
            box-shadow: 0 8px 25px rgba(0,0,0,0.6), 0 0 15px rgba(255, 42, 84, 0.15);
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

        .card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            z-index: 5;
        }

        .badge-ongoing {
            background-color: var(--ongoing-color);
            color: #033012;
        }

        .badge-completed {
            background-color: var(--completed-color);
            color: #06193b;
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

        .card-views {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .card-actions {
            margin-top: 10px;
            display: flex;
            gap: 8px;
        }

        .btn-my-list-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-my-list-toggle:hover {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* General layout grid for show rows */
        .shows-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 24px;
            padding: 0 6%;
            margin-bottom: 50px;
        }

        .page-header {
            padding: 40px 6% 20px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-family: var(--heading-family);
            font-size: 32px;
            font-weight: 800;
            position: relative;
            padding-left: 16px;
        }

        .page-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 15%;
            height: 70%;
            width: 5px;
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        /* Footer styling */
        footer {
            margin-top: 80px;
            padding: 40px 0;
            border-top: 1px solid rgba(255,255,255,0.05);
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        footer .logo {
            justify-content: center;
            margin-bottom: 15px;
            font-size: 22px;
        }

        /* Warning Banner */
        .api-warning {
            margin: 20px 6% 30px 6%;
            padding: 16px 24px;
            background: rgba(255, 42, 84, 0.12);
            border: 1px solid rgba(255, 42, 84, 0.3);
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .warning-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .warning-content i {
            font-size: 24px;
            color: var(--primary);
        }

        .warning-text h4 {
            font-family: var(--heading-family);
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .warning-text p {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Responsive & Zoom Interactive Enhancements */
        .mobile-menu-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            background: var(--primary);
            border-color: var(--primary);
        }

        .logout-btn {
            background: rgba(255, 42, 84, 0.1) !important;
            border-color: rgba(255, 42, 84, 0.2) !important;
            color: #ff2a54 !important;
        }

        .logout-btn:hover {
            background: var(--primary) !important;
            color: white !important;
        }

        /* Dynamic Breakpoints for Zoom In / Zoom Out Resilience */
        @media(max-width: 1100px) {
            header {
                padding: 16px 4%;
            }
            .nav-links {
                gap: 15px;
            }
            .search-bar input {
                width: 110px;
            }
        }

        @media(max-width: 950px) {
            .mobile-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 10, 14, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 20px 6%;
                gap: 12px;
                border-bottom: 1px solid var(--glass-border);
                box-shadow: 0 15px 35px rgba(0,0,0,0.8);
                z-index: 1100;
            }

            .nav-links.active {
                display: flex;
                animation: fadeInDown 0.3s ease-out;
            }

            .nav-links a {
                font-size: 16px;
                padding: 8px 0;
                display: block;
            }
        }

        @media(max-width: 640px) {
            body {
                padding-top: 130px;
            }

            header {
                flex-wrap: wrap;
                gap: 10px;
                padding: 12px 4%;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
                order: 3;
            }

            .search-bar {
                flex: 1;
            }

            .search-bar input {
                width: 100%;
            }

            .btn-text {
                display: none;
            }

            .shows-grid {
                grid-template-columns: repeat(auto-fill, minmax(min(100%, 140px), 1fr));
                gap: 12px;
                padding: 0 4%;
            }

            .page-header {
                padding: 20px 4% 15px 4%;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .page-title {
                font-size: clamp(22px, 5vw, 32px);
            }
        }

        @media(max-width: 400px) {
            .shows-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .card-details {
                padding: 10px;
            }

            .card-title {
                font-size: 13px;
            }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <a href="/" class="logo">
            <i class="fa-solid fa-play"></i> Mubee
        </a>
        <ul class="nav-links" id="navLinks">
            <li><a href="/" class="{{ Request::is('/') ? 'active' : '' }}">Home</a></li>
            <li><a href="/dramas" class="{{ Request::is('dramas') ? 'active' : '' }}">Dramas</a></li>
            <li><a href="/movies" class="{{ Request::is('movies') ? 'active' : '' }}">Movies</a></li>
            <li><a href="/genres" class="{{ Request::is('genres*') ? 'active' : '' }}">Genres</a></li>
            <li><a href="/actors" class="{{ Request::is('actors') ? 'active' : '' }}">Popular Actors</a></li>
            <li><a href="/my-list" class="{{ Request::is('my-list') ? 'active' : '' }}">My List</a></li>
        </ul>
        <div class="header-actions">
            <form action="{{ route('search') }}" method="GET" class="search-bar" style="margin-right: 5px;">
                <i class="fa-solid fa-magnifying-glass" style="opacity: 0.6;"></i>
                <input type="text" name="q" placeholder="Cari film atau drama..." value="{{ request('q') }}">
            </form>
            <a href="/settings" class="header-btn {{ Request::is('settings') ? 'active' : '' }}" title="Settings">
                <i class="fa-solid fa-gear"></i> <span class="btn-text">Settings</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="header-btn logout-btn" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i> <span class="btn-text">Keluar</span>
                </button>
            </form>
            <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileNav()" aria-label="Menu Toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Main Content Area -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="logo">
            <i class="fa-solid fa-play"></i> Mubee
        </div>
        <p>© 2026 Mubee. Dibuat dengan cinta untuk seluruh pencinta K-Drama di Indonesia.</p>
        <p style="font-size: 11px; margin-top: 5px; opacity: 0.6;">Didukung oleh TMDB API Integration.</p>
    </footer>

    <!-- Global Flash Notification Toast -->
    @if(session('status'))
        <div id="statusToast" style="position: fixed; bottom: 30px; right: 30px; background: rgba(8, 8, 10, 0.95); border: 1px solid var(--primary); padding: 16px 24px; border-radius: 12px; z-index: 9999; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 12px; animation: fadeInUp 0.4s ease-out; backdrop-filter: blur(10px);">
            <i class="fa-solid fa-circle-check" style="color: var(--primary); font-size: 20px;"></i>
            <span style="font-weight: 500;">{{ session('status') }}</span>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('statusToast');
                if (toast) {
                    toast.style.transition = 'opacity 0.5s ease';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

    <!-- Ajax My List Script & Mobile Nav -->
    <script>
        function toggleMobileNav() {
            const nav = document.getElementById('navLinks');
            const icon = document.querySelector('#mobileMenuToggle i');
            if (nav) {
                nav.classList.toggle('active');
                if (icon) {
                    if (nav.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                    } else {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        }

        function toggleMyListAjax(element, tmdbId, mediaType, title, posterPath, voteAverage) {
            const isAdded = element.getAttribute('data-added') === 'true';
            
            fetch("{{ route('mylist.toggle') }}", {
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
                        element.innerHTML = '<i class="fa-solid fa-plus"></i> Daftar Tontonan';
                        element.style.borderColor = '';
                        element.style.color = '';
                        
                        // If we are currently on the my-list page, remove the card from view
                        if (window.location.pathname === '/my-list') {
                            const card = element.closest('.show-card');
                            if (card) {
                                card.style.transition = 'all 0.4s ease';
                                card.style.opacity = '0';
                                card.style.transform = 'scale(0.8)';
                                setTimeout(() => {
                                    card.remove();
                                    // If no cards left, reload to show empty state
                                    if (document.querySelectorAll('.shows-grid .show-card').length === 0) {
                                        window.location.reload();
                                    }
                                }, 400);
                            }
                        }
                    }
                }
            })
            .catch(err => console.error("Error toggling watchlist: ", err));
        }
    </script>

    @yield('scripts')
</body>
</html>
