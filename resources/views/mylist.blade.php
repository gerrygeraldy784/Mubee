@extends('layouts.app')

@section('title', 'Daftar Saya (My List) - Mubee')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Daftar Saya (My List)</h1>
    </div>

    @if($myList->isEmpty())
        <div style="padding: 100px 0; text-align: center;">
            <i class="fa-solid fa-folder-open" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;"></i>
            <h3 style="font-family: var(--heading-family); font-size: 20px; font-weight: 600; margin-bottom: 8px;">Daftar Kamu Kosong</h3>
            <p style="color: var(--text-muted); font-size: 14px; max-width: 400px; margin: 0 auto 30px auto;">Mulai tambahkan K-Drama atau K-Movie favoritmu agar dapat menemukannya dengan cepat di sini.</p>
            <a href="/" class="header-btn" style="padding: 10px 24px; border-radius: 30px;">Cari Konten</a>
        </div>
    @else
        <div class="shows-grid">
            @foreach($myList as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
                @endphp
                <div class="show-card">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>

                    <div class="card-img-container" onclick="location.href='/shows/{{ $item['media_type'] }}/{{ $item['tmdb_id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $item['media_type'] }}/{{ $item['tmdb_id'] }}'">{{ $item['title'] ?? 'K-Content' }}</div>
                        <div class="card-meta">
                            <span style="text-transform: uppercase; font-weight: 700; color: var(--primary); font-size: 11px;">
                                {{ $item['media_type'] === 'tv' ? 'Drama' : 'Movie' }}
                            </span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" 
                                    data-added="true"
                                    style="border-color: var(--primary); color: var(--primary);"
                                    onclick="toggleMyListAjax(this, {{ $item['tmdb_id'] }}, '{{ $item['media_type'] }}', '{{ addslashes($item['title']) }}', '{{ $item['poster_path'] }}', {{ $item['vote_average'] ?? 0 }})">
                                <i class="fa-solid fa-check"></i> Di Daftar Tontonan
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
