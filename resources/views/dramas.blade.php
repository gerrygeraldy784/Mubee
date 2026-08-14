@extends('layouts.app')

@section('title', 'Korean Dramas - Mubee')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Serial Drama Korea</h1>
    </div>

    <!-- API Connection Warning Banner -->
    @if($isMocked)
        <div class="api-warning">
            <div class="warning-content">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div class="warning-text">
                    <h4>Koneksi TMDB API Belum Dikonfigurasi</h4>
                    <p>Menampilkan data demonstrasi (mock) untuk K-Dramas.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="shows-grid">
        @foreach($tvList as $item)
            @php
                $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                    ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                    : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=400');
                $isAdded = in_array($item['id'], $myListIds);
            @endphp
            <div class="show-card">
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
                    <div class="card-title" onclick="location.href='/shows/tv/{{ $item['id'] }}'">{{ $item['name'] ?? 'K-Drama' }}</div>
                    <div class="card-meta">
                        <span>{{ substr($item['first_air_date'] ?? '2024', 0, 4) }}</span>
                        <span class="card-views"><i class="fa-regular fa-eye"></i> {{ rand(500, 3500) }}k</span>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="btn-my-list-toggle" 
                                data-added="{{ $isAdded ? 'true' : 'false' }}"
                                style="{{ $isAdded ? 'border-color: var(--primary); color: var(--primary);' : '' }}"
                                onclick="toggleMyListAjax(this, {{ $item['id'] }}, 'tv', '{{ addslashes($item['name'] ?? 'K-Drama') }}', '{{ $item['poster_path'] }}', {{ $item['vote_average'] ?? 0 }})">
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
