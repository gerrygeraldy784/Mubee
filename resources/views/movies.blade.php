@extends('layouts.app')

@section('title', 'Korean Movies - Mubee')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Film Korea Pilihan</h1>
    </div>

    <!-- API Connection Warning Banner -->
    @if($isMocked)
        <div class="api-warning">
            <div class="warning-content">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div class="warning-text">
                    <h4>Koneksi TMDB API Belum Dikonfigurasi</h4>
                    <p>Menampilkan data demonstrasi (mock) untuk K-Movies.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="shows-grid">
        @foreach($moviesList as $item)
            @php
                $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                    ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                    : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
                $isAdded = in_array($item['id'], $myListIds);
            @endphp
            <div class="show-card">
                <div class="badge-rating">
                    <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                </div>

                <div class="card-img-container" onclick="location.href='/shows/movie/{{ $item['id'] }}'">
                    <img src="{{ $poster }}" class="card-img" alt="Poster">
                </div>
                <div class="card-details">
                    <div class="card-title" onclick="location.href='/shows/movie/{{ $item['id'] }}'">{{ $item['title'] ?? 'K-Movie' }}</div>
                    <div class="card-meta">
                        <span>{{ substr($item['release_date'] ?? '2024', 0, 4) }}</span>
                        <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 2500) }}k</span>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="btn-my-list-toggle" 
                                data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, 'movie', '{{ addslashes($item['title'] ?? 'K-Movie') }}', '{{ $item['poster_path'] }}', {{ $item['vote_average'] ?? 0 }})">
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
