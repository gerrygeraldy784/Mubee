@extends('layouts.app')

@section('title', 'Hasil Pencarian - Mubee')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Hasil Pencarian</h1>
    </div>

    <div style="padding: 0 6% 20px 6%; margin-top: -10px;">
        <p style="color: var(--text-muted); font-size: 15px;">
            Menampilkan hasil pencarian untuk kata kunci: <strong style="color: var(--primary);">"{{ $query }}"</strong>
        </p>
    </div>

    @if(empty($results))
        <div style="padding: 100px 0; text-align: center;">
            <i class="fa-solid fa-magnifying-glass-minus" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;"></i>
            <h3 style="font-family: var(--heading-family); font-size: 20px; font-weight: 600; margin-bottom: 8px;">Tidak Ada Hasil Ditemukan</h3>
            <p style="color: var(--text-muted); font-size: 14px; max-width: 400px; margin: 0 auto 30px auto;">Kami tidak dapat menemukan film atau drama Korea yang cocok dengan "{{ $query }}". Coba cari dengan kata kunci lain.</p>
            <a href="/" class="header-btn" style="padding: 10px 24px; border-radius: 30px;">Kembali ke Beranda</a>
        </div>
    @else
        <div class="shows-grid">
            @foreach($results as $item)
                @php
                    $poster = isset($item['poster_path']) && !str_starts_with($item['poster_path'], 'http')
                        ? "https://image.tmdb.org/t/p/w500" . $item['poster_path']
                        : ($item['poster_path'] ?? 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=400');
                    $mType = $item['media_type'] ?? (isset($item['name']) ? 'tv' : 'movie');
                    $isAdded = in_array($item['id'], $myListIds);
                @endphp
                <div class="show-card">
                    <div class="badge-rating">
                        <i class="fa-solid fa-star"></i> {{ number_format($item['vote_average'] ?? 8.0, 1) }}
                    </div>

                    <div class="card-img-container" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                        <img src="{{ $poster }}" class="card-img" alt="Poster">
                    </div>
                    <div class="card-details">
                        <div class="card-title" onclick="location.href='/shows/{{ $mType }}/{{ $item['id'] }}'">
                            {{ $item['title'] ?? $item['name'] ?? 'K-Content' }}
                        </div>
                        <div class="card-meta">
                            <span>{{ substr($item['release_date'] ?? $item['first_air_date'] ?? '2024', 0, 4) }}</span>
                            <span style="text-transform: uppercase; font-weight: 700; color: var(--primary); font-size: 11px;">
                                {{ $mType === 'tv' ? 'Drama' : 'Movie' }}
                            </span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-my-list-toggle" 
                                    data-added="{{ $isAdded ? 'true' : 'false' }}"
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
    @endif
@endsection
