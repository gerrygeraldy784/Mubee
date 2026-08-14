# 🎨 Perancangan Wireframe Block Layout Halaman - Platform Streaming Mubee

Dokumen ini berisi **Perancangan Gambar Tata Letak Halaman (Visual Wireframe Block Layout Diagram)** untuk setiap halaman pada aplikasi **Mubee (K-Drama & K-Movie Streaming Platform)** yang digambar dalam format **Mermaid.js** sesuai dengan diagram struktur blok UI/UX.

---

## 🗺️ 1. Pemetaan Struktur Router & Controller

| URL Route | Controller & Action | Nama Halaman | Komponen UI Utama |
| :--- | :--- | :--- | :--- |
| `/login` | `LoginController@showLoginForm` | Login Page | Logo, Input Email/Password, Button Submit, Register Link |
| `/register` | `RegisterController@showRegistrationForm` | Register Page | Logo, Input Name/Email/Pass, Button Submit, Login Link |
| `/` | `DashboardController@index` | Beranda (Home) | Logo + Search, Menu Navigasi, Big Feature Hero, Riwayat Tontonan, Small Features Grid, Sidebar, Footer |
| `/dramas` | `DashboardController@dramas` | Serial Drama | Header Nav, Page Title, Drama Grid Cards (Ongoing/Completed), Footer |
| `/movies` | `DashboardController@movies` | Film Korea | Header Nav, Page Title, Movie Grid Cards, Sidebar Highlight, Footer |
| `/genres` | `DashboardController@genres` | Kategori Genre | Header Nav, Genre Hero Select, Category Cards, Filtered Grid, Footer |
| `/actors` | `DashboardController@actors` | Aktor & Aktris | Header Nav, Hero Banner, Search Actor, Actor Cards Grid, Works Modal, Footer |
| `/my-list` | `DashboardController@myList` | Daftar Tontonan | Header Nav, Page Title, My List Grid Cards, Remove Button, Empty Notice, Footer |
| `/search` | `DashboardController@search` | Hasil Pencarian | Header Nav + Search Active, Results Grid, Query Status, Footer |
| `/settings` | `DashboardController@settings` | Pengaturan | Header Nav, User Profile Card, Accent Theme Picker, Player Quality, Footer |
| `/shows/{type}/{id}` | `DashboardController@show` | Detail Konten | Header + Back Btn, Hero Backdrop, Poster Card, Metadata, Seasons/Episodes, Recs, Footer |
| `/movies/{id}` | `MovieController@show` | Pemutar Film | Header Nav, VidSrc Embed Player Frame, Server Select, Synopsis, Recs, Footer |
| `/tv/{id}/watch/{s}/{e}`| `TvController@watchEpisode` | Pemutar Drama TV | Header Nav, VidSrc Embed Player Frame, Prev/Next Episode Nav, Episode Selector Grid, Sync Script |

---

## 📐 2. Perancangan Wireframe Block Layout Setiap Halaman (Mermaid.js)

---

### 2.1. Halaman Beranda Utama (`/`) - Home Layout

Halaman utama memiliki tata letak blok yang terdiri dari **Header (Logo & Search)**, **Menu Navigasi**, **Big Feature Hero Banner**, **Riwayat Tontonan (Recent Post)**, **Small Features Grid (Katalog Tren & Drama)**, **Sidebar Kanan**, dan **Footer**.

```mermaid
graph TD
    subgraph HOME_PAGE [" 🏠 WIREFRAME LAYOUT: HALAMAN BERANDA UTAMA (HOME) "]
        direction TB

        subgraph H_TOP [" 1. HEADER ROW "]
            direction LR
            H_LOGO["LOGO MUBEE\n[ Brand Icon & Slogan ]"] --- H_SEARCH["SEARCH BAR & USER PROFILE\n[ Input Cari Film | Settings | Keluar ]"]
        end

        subgraph H_NAV [" 2. MENU NAVIGASI ROW "]
            direction LR
            N1["Home"] --- N2["Dramas"] --- N3["Movies"] --- N4["Genres"] --- N5["Actors"] --- N6["My List"]
        end

        subgraph H_BODY [" 3. MAIN CONTENT BODY AREA "]
            direction LR

            subgraph H_LEFT [" AREA KONTEN UTAMA (LEFT MAIN SECTION) "]
                direction TB

                subgraph H_HERO [" BIG FEATURE (HERO BANNER) "]
                    HERO_ITEM["BIG FEATURE: Trending #1 Banner\n- Backdrop Poster HD & Rating IMDB\n- Judul Film/Drama & Sinopsis Ringkas\n- Tombol: [ Putar Sekarang ] [ Info Lengkap ] [ + Daftar Tontonan ]"]
                end

                subgraph H_HISTORY [" RECENT WATCH (RIWAYAT TONTONAN TERAKHIR) "]
                    HIST_HEAD["Featured: RIWAYAT TONTONAN (TERAKHIR DITONTON)"]
                    direction LR
                    HW1["Card 1\nS1 E3 (2 jam lalu)"] --- HW2["Card 2\nMovie (Baru saja)"] --- HW3["Card 3\nS1 E5 (1 hari lalu)"]
                end

                subgraph H_SMALL_GRID [" SMALL FEATURES GRID (KATALOG TREN & DRAMA) "]
                    direction LR
                    SF1["Small Feature 1\nTop 10 Tren #1"] --- SF2["Small Feature 2\nTop 10 Tren #2"] --- SF3["Small Feature 3\nTop 10 Tren #3"]
                end

                subgraph H_SMALL_GRID2 [" SMALL FEATURES GRID (SERIAL DRAMA & FILM) "]
                    direction LR
                    SF4["Small Feature 4\nSerial Drama"] --- SF5["Small Feature 5\nDrama Populer"] --- SF6["Small Feature 6\nFilm Korea"]
                end
            end

            subgraph H_RIGHT [" SIDEBAR KANAN (RECENT POST & HIGHLIGHTS) "]
                direction TB
                SIDE_RECENT["RECENT POST / HIGHLIGHTS\n- Drama Ongoing Terbaru\n- Populer Sepanjang Masa\n- Top Rating IMDB"]
                SIDE_NEWS["NEWS & ANNOUNCEMENT\n- TMDB API Status\n- Genre Quick Select\n- Aktor Korean Highlight"]
            end
        end

        subgraph H_FOOTER [" 4. FOOTER ROW "]
            FOOT_TXT["Copyright @ 2026 Mubee K-Drama & K-Movie Streaming Platform"]
        end

        H_TOP --> H_NAV
        H_NAV --> H_BODY
        H_BODY --> H_FOOTER
    end
```

---

### 2.2. Halaman Serial Drama (`/dramas`)

```mermaid
graph TD
    subgraph DRAMAS_PAGE [" 📺 WIREFRAME LAYOUT: HALAMAN SERIAL DRAMA (/dramas) "]
        direction TB

        subgraph D_TOP [" HEADER ROW "]
            direction LR
            D_LOGO["LOGO MUBEE"] --- D_SEARCH["SEARCH BAR & USER CONTROLS"]
        end

        subgraph D_NAV [" MENU NAVIGASI ROW "]
            direction LR
            DN1["Home"] --- DN2["[Dramas (Active)]"] --- DN3["Movies"] --- DN4["Genres"] --- DN5["Actors"] --- DN6["My List"]
        end

        subgraph D_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph D_LEFT [" AREA KATALOG DRAMA "]
                direction TB
                D_TITLE["BIG FEATURE TITLE: Serial Drama Korea (Rilis 2024-2025)"]

                subgraph D_GRID1 [" SMALL FEATURES GRID (ROW 1) "]
                    direction LR
                    D1["Small Feature 1\nDrama Ongoing"] --- D2["Small Feature 2\nDrama Completed"] --- D3["Small Feature 3\nDrama Ongoing"]
                end

                subgraph D_GRID2 [" SMALL FEATURES GRID (ROW 2) "]
                    direction LR
                    D4["Small Feature 4\nDrama Completed"] --- D5["Small Feature 5\nDrama Ongoing"] --- D6["Small Feature 6\nDrama Completed"]
                end
            end

            subgraph D_RIGHT [" SIDEBAR KANAN (RECENT POST DRAMA) "]
                direction TB
                D_SIDE1["RECENT POST\n- Top Rating Drama\n- Episode Terbaru Minggu Ini"]
                D_SIDE2["FILTER DRAMA\n- Status: Ongoing / Tamat\n- Tahun: 2024 / 2025"]
            end
        end

        subgraph D_FOOTER [" FOOTER ROW "]
            D_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        D_TOP --> D_NAV
        D_NAV --> D_BODY
        D_BODY --> D_FOOTER
    end
```

---

### 2.3. Halaman Film Korea (`/movies`)

```mermaid
graph TD
    subgraph MOVIES_PAGE [" 🎬 WIREFRAME LAYOUT: HALAMAN FILM KOREA (/movies) "]
        direction TB

        subgraph M_TOP [" HEADER ROW "]
            direction LR
            M_LOGO["LOGO MUBEE"] --- M_SEARCH["SEARCH BAR & USER CONTROLS"]
        end

        subgraph M_NAV [" MENU NAVIGASI ROW "]
            direction LR
            MN1["Home"] --- MN2["Dramas"] --- MN3["[Movies (Active)]"] --- MN4["Genres"] --- MN5["Actors"] --- MN6["My List"]
        end

        subgraph M_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph M_LEFT [" AREA KATALOG FILM "]
                direction TB
                M_TITLE["BIG FEATURE TITLE: Film Korea Pilihan (K-Movies)"]

                subgraph M_GRID1 [" SMALL FEATURES GRID (ROW 1) "]
                    direction LR
                    M1["Small Feature 1\nFilm Aksi"] --- M2["Small Feature 2\nFilm Thriller"] --- M3["Small Feature 3\nFilm Romantis"]
                end

                subgraph M_GRID2 [" SMALL FEATURES GRID (ROW 2) "]
                    direction LR
                    M4["Small Feature 4\nFilm Komedi"] --- M5["Small Feature 5\nFilm Horor"] --- M6["Small Feature 6\nFilm Drama"]
                end
            end

            subgraph M_RIGHT [" SIDEBAR KANAN (RECENT POST MOVIES) "]
                direction TB
                M_SIDE1["RECENT POST MOVIES\n- Box Office Korea\n- Rating IMDB > 8.0"]
                M_SIDE2["NEWS & UPDATES\n- Rilis Film Bioskop Terbaru"]
            end
        end

        subgraph M_FOOTER [" FOOTER ROW "]
            M_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        M_TOP --> M_NAV
        M_NAV --> M_BODY
        M_BODY --> M_FOOTER
    end
```

---

### 2.4. Halaman Kategori Genre (`/genres`)

```mermaid
graph TD
    subgraph GENRES_PAGE [" 🎭 WIREFRAME LAYOUT: HALAMAN KATEGORI GENRE (/genres) "]
        direction TB

        subgraph G_TOP [" HEADER ROW "]
            direction LR
            G_LOGO["LOGO MUBEE"] --- G_SEARCH["SEARCH BAR"]
        end

        subgraph G_NAV [" MENU NAVIGASI ROW "]
            direction LR
            GN1["Home"] --- GN2["Dramas"] --- GN3["Movies"] --- GN4["[Genres (Active)]"] --- GN5["Actors"] --- GN6["My List"]
        end

        subgraph G_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph G_LEFT [" AREA EXPLORE GENRE "]
                direction TB
                G_HERO["BIG FEATURE: Genre Selection Header & Dropdown Select"]

                subgraph G_CARDS [" SMALL FEATURES GRID (GENRE CARDS) "]
                    direction LR
                    GC1["Small Feature\nDrama Icon"] --- GC2["Small Feature\nRomantis Icon"] --- GC3["Small Feature\nAksi Icon"]
                end

                subgraph G_FILTERED [" SMALL FEATURES GRID (HASIL FILTER KONTEN) "]
                    direction LR
                    GF1["Konten Genre 1"] --- GF2["Konten Genre 2"] --- GF3["Konten Genre 3"]
                end
            end

            subgraph G_RIGHT [" SIDEBAR KANAN (RECENT GENRE POST) "]
                direction TB
                G_SIDE1["RECENT POST GENRE\n- Genre Terpopuler Minggu Ini"]
                G_SIDE2["NEWS\n- Rekomendasi Genre Berdasarkan Mood"]
            end
        end

        subgraph G_FOOTER [" FOOTER ROW "]
            G_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        G_TOP --> G_NAV
        G_NAV --> G_BODY
        G_BODY --> G_FOOTER
    end
```

---

### 2.5. Halaman Aktor & Aktris Populer (`/actors`)

```mermaid
graph TD
    subgraph ACTORS_PAGE [" 🌟 WIREFRAME LAYOUT: HALAMAN AKTOR & AKTRIS (/actors) "]
        direction TB

        subgraph A_TOP [" HEADER ROW "]
            direction LR
            A_LOGO["LOGO MUBEE"] --- A_SEARCH["SEARCH BAR"]
        end

        subgraph A_NAV [" MENU NAVIGASI ROW "]
            direction LR
            AN1["Home"] --- AN2["Dramas"] --- AN3["Movies"] --- AN4["Genres"] --- AN5["[Actors (Active)]"] --- AN6["My List"]
        end

        subgraph A_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph A_LEFT [" AREA PROFILE AKTOR "]
                direction TB
                A_HERO["BIG FEATURE: Profil Aktor Korea & Search Actor Input"]

                subgraph A_GRID1 [" SMALL FEATURES GRID (AKTOR PROFILES) "]
                    direction LR
                    A1["Small Feature 1\nRound Avatar Aktor A"] --- A2["Small Feature 2\nRound Avatar Aktris B"] --- A3["Small Feature 3\nRound Avatar Aktor C"]
                end

                subgraph A_MODAL [" MODAL OVERLAY (KARYA FILM AKTOR) "]
                    A_MOD["Featured: Modal Overlay Popup Works List (Daftar Drama/Film Aktor)"]
                end
            end

            subgraph A_RIGHT [" SIDEBAR KANAN (RECENT POST AKTOR) "]
                direction TB
                A_SIDE1["RECENT POST AKTOR\n- Aktor Trending Top 1"]
                A_SIDE2["NEWS\n- Awards & Penghargaan"]
            end
        end

        subgraph A_FOOTER [" FOOTER ROW "]
            A_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        A_TOP --> A_NAV
        A_NAV --> A_BODY
        A_BODY --> A_FOOTER
    end
```

---

### 2.6. Halaman Daftar Tontonan Saya (`/my-list`)

```mermaid
graph TD
    subgraph MYLIST_PAGE [" 📌 WIREFRAME LAYOUT: HALAMAN DAFTAR TONTONAN (/my-list) "]
        direction TB

        subgraph ML_TOP [" HEADER ROW "]
            direction LR
            ML_LOGO["LOGO MUBEE"] --- ML_SEARCH["SEARCH BAR"]
        end

        subgraph ML_NAV [" MENU NAVIGASI ROW "]
            direction LR
            MLN1["Home"] --- MLN2["Dramas"] --- MLN3["Movies"] --- MLN4["Genres"] --- MLN5["Actors"] --- MLN6["[My List (Active)]"]
        end

        subgraph ML_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph ML_LEFT [" AREA DAFTAR FAVORIT PRIBADI "]
                direction TB
                ML_HERO["BIG FEATURE: Header Daftar Tontonan Saya (My List)"]

                subgraph ML_GRID [" SMALL FEATURES GRID (CARD FAVORIT) "]
                    direction LR
                    ML1["Small Feature 1\nFavorit A [Hapus]"] --- ML2["Small Feature 2\nFavorit B [Hapus]"] --- ML3["Small Feature 3\nFavorit C [Hapus]"]
                end
            end

            subgraph ML_RIGHT [" SIDEBAR KANAN (RECENT POST REKOMENDASI) "]
                direction TB
                ML_SIDE1["RECENT POST\n- Rekomendasi Berdasarkan List"]
                ML_SIDE2["NEWS\n- Total Ditambahkan: N Konten"]
            end
        end

        subgraph ML_FOOTER [" FOOTER ROW "]
            ML_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        ML_TOP --> ML_NAV
        ML_NAV --> ML_BODY
        ML_BODY --> ML_FOOTER
    end
```

---

### 2.7. Halaman Hasil Pencarian (`/search`)

```mermaid
graph TD
    subgraph SEARCH_PAGE [" 🔍 WIREFRAME LAYOUT: HALAMAN HASIL PENCARIAN (/search) "]
        direction TB

        subgraph S_TOP [" HEADER ROW "]
            direction LR
            S_LOGO["LOGO MUBEE"] --- S_SEARCH["SEARCH INPUT ACTIVE: 'Queen of Tears'"]
        end

        subgraph S_NAV [" MENU NAVIGASI ROW "]
            direction LR
            SN1["Home"] --- SN2["Dramas"] --- SN3["Movies"] --- SN4["Genres"] --- SN5["Actors"] --- SN6["My List"]
        end

        subgraph S_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph S_LEFT [" AREA HASIL PENCARIAN "]
                direction TB
                S_HERO["BIG FEATURE: Hasil Pencarian Kata Kunci: 'Query' (N Hasil Found)"]

                subgraph S_GRID [" SMALL FEATURES GRID (CARD HASIL CARI) "]
                    direction LR
                    S1["Small Feature 1\nHasil Match 1"] --- S2["Small Feature 2\nHasil Match 2"] --- S3["Small Feature 3\nHasil Match 3"]
                end
            end

            subgraph S_RIGHT [" SIDEBAR KANAN (RECENT POST CARI) "]
                direction TB
                S_SIDE1["RECENT POST\n- Kata Kunci Populer"]
                S_SIDE2["NEWS\n- Film Mirip"]
            end
        end

        subgraph S_FOOTER [" FOOTER ROW "]
            S_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        S_TOP --> S_NAV
        S_NAV --> S_BODY
        S_BODY --> S_FOOTER
    end
```

---

### 2.8. Halaman Pengaturan / Settings (`/settings`)

```mermaid
graph TD
    subgraph SETTINGS_PAGE [" ⚙️ WIREFRAME LAYOUT: HALAMAN PENGATURAN (/settings) "]
        direction TB

        subgraph ST_TOP [" HEADER ROW "]
            direction LR
            ST_LOGO["LOGO MUBEE"] --- ST_SEARCH["SEARCH BAR"]
        end

        subgraph ST_NAV [" MENU NAVIGASI ROW "]
            direction LR
            STN1["Home"] --- STN2["Dramas"] --- STN3["Movies"] --- STN4["Genres"] --- STN5["Actors"] --- STN6["My List"]
        end

        subgraph ST_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph ST_LEFT [" AREA PENGATURAN AKUN & TEMA "]
                direction TB
                ST_HERO["BIG FEATURE: Card Form Informasi Akun & Profil Pengguna"]

                subgraph ST_SWATCHES [" SMALL FEATURES GRID (PREFERENSI SISTEM) "]
                    direction LR
                    ST1["Small Feature 1\nTheme Accent Picker\n(Pink/Blue/Purple/Green)"] --- ST2["Small Feature 2\nKualitas Video\n(Auto 1080p/720p)"] --- ST3["Small Feature 3\nAutoplay Toggle\n(ON / OFF)"]
                end
            end

            subgraph ST_RIGHT [" SIDEBAR KANAN (RECENT POST USER STATUS) "]
                direction TB
                ST_SIDE1["RECENT POST / STATUS AKUN\n- Status Berlangganan VIP"]
                ST_SIDE2["NEWS & SECURITY\n- Terakhir Login: Today"]
            end
        end

        subgraph ST_FOOTER [" FOOTER ROW "]
            ST_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        ST_TOP --> ST_NAV
        ST_NAV --> ST_BODY
        ST_BODY --> ST_FOOTER
    end
```

---

### 2.9. Halaman Detail Konten (`/shows/{type}/{id}`)

```mermaid
graph TD
    subgraph DETAIL_PAGE [" ℹ️ WIREFRAME LAYOUT: HALAMAN DETAIL KONTEN (/shows/{type}/{id}) "]
        direction TB

        subgraph DET_TOP [" HEADER ROW "]
            direction LR
            DET_LOGO["LOGO MUBEE"] --- DET_BACK["TOMBOL KEMBALI (< Kembali)"]
        end

        subgraph DET_NAV [" MENU NAVIGASI ROW "]
            direction LR
            DETN1["Home"] --- DETN2["Dramas"] --- DETN3["Movies"] --- DETN4["Genres"] --- DETN5["Actors"] --- DETN6["My List"]
        end

        subgraph DET_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph DET_LEFT [" AREA HERO DETAIL & SINOPSIS "]
                direction TB
                DET_HERO["BIG FEATURE: Backdrop Hero Container & Poster Card\n- Judul Film/Drama KR & EN\n- IMDB Rating, Year, Status Badge\n- Tombol: [ Putar Sekarang ] [ + Daftar Tontonan ]"]

                subgraph DET_GRID [" SMALL FEATURES GRID (DAFTAR EPISODE / CAST) "]
                    direction LR
                    DET1["Small Feature 1\nEpisode 1 Card"] --- DET2["Small Feature 2\nEpisode 2 Card"] --- DET3["Small Feature 3\nCast Avatar"]
                end
            end

            subgraph DET_RIGHT [" SIDEBAR KANAN (RECENT POST RECOMMENDATIONS) "]
                direction TB
                DET_SIDE1["RECENT POST / REKOMENDASI\n- Film/Drama Serupa"]
                DET_SIDE2["NEWS & METADATA\n- Sutradara & Studio Rilis"]
            end
        end

        subgraph DET_FOOTER [" FOOTER ROW "]
            DET_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        DET_TOP --> DET_NAV
        DET_NAV --> DET_BODY
        DET_BODY --> DET_FOOTER
    end
```

---

### 2.10. Halaman Pemutar Film (`/movies/{id}`) & Episode TV (`/tv/{id}/watch/{s}/{e}`)

```mermaid
graph TD
    subgraph WATCH_PAGE [" 🎬 WIREFRAME LAYOUT: HALAMAN PEMUTAR VIDEO (WATCH PLAYER) "]
        direction TB

        subgraph W_TOP [" HEADER ROW "]
            direction LR
            W_LOGO["LOGO MUBEE"] --- W_SEARCH["SEARCH BAR & KELUAR"]
        end

        subgraph W_NAV [" MENU NAVIGASI ROW "]
            direction LR
            WN1["Home"] --- WN2["Dramas"] --- WN3["Movies"] --- WN4["Genres"] --- WN5["Actors"] --- WN6["My List"]
        end

        subgraph W_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph W_LEFT [" AREA PEMUTAR VIDEO PLAYER "]
                direction TB
                W_HERO["BIG FEATURE: VidSrc Embed Video Player Frame\n- Multi-Server Selector (Server 1 / Server 2 / Server 3)\n- Auto Progress Tracker & Auto-Save Sync"]

                subgraph W_GRID [" SMALL FEATURES GRID (EPISODE NAV & ACTIONS) "]
                    direction LR
                    W1["Small Feature 1\n[<< Episode Prev]"] --- W2["Small Feature 2\n[Episode Next >>]"] --- W3["Small Feature 3\n[Bookmark / Share]"]
                end
            end

            subgraph W_RIGHT [" SIDEBAR KANAN (RECENT POST & REKOMENDASI) "]
                direction TB
                W_SIDE1["RECENT POST\n- Rekomendasi Selanjutnya"]
                W_SIDE2["NEWS & COMMENTS\n- Sinopsis Episode Active"]
            end
        end

        subgraph W_FOOTER [" FOOTER ROW "]
            W_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        W_TOP --> W_NAV
        W_NAV --> W_BODY
        W_BODY --> W_FOOTER
    end
```

---

### 2.11. Halaman Login / Masuk Akun (`/login`)

```mermaid
graph TD
    subgraph LOGIN_PAGE [" 🔐 WIREFRAME LAYOUT: HALAMAN LOGIN (/login) "]
        direction TB

        subgraph L_TOP [" HEADER ROW "]
            direction LR
            L_LOGO["LOGO MUBEE BRANDING"] --- L_INFO["PORTAL STREAMING K-DRAMA & K-MOVIE"]
        end

        subgraph L_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph L_LEFT [" FORM LOGIN UTAMA "]
                direction TB
                L_HERO["BIG FEATURE: Card Glassmorphism Form Login\n- Input Email & Password\n- Checkbox Remember Me & Link Lupa Password\n- Tombol: [ MASUK SEKARANG ]\n- Link: Belum punya akun? Daftar Akun Baru"]
            end

            subgraph L_RIGHT [" SIDEBAR KANAN (RECENT POST BANNER PROMO) "]
                direction TB
                L_SIDE1["RECENT POST / HIGHLIGHTS\n- Nonton K-Drama Terbaru Gratis"]
                L_SIDE2["NEWS & SECURITY\n- Keamanan & Akses VIP"]
            end
        end

        subgraph L_FOOTER [" FOOTER ROW "]
            L_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        L_TOP --> L_BODY
        L_BODY --> L_FOOTER
    end
```

---

### 2.12. Halaman Register / Buat Akun Baru (`/register`)

```mermaid
graph TD
    subgraph REGISTER_PAGE [" 📝 WIREFRAME LAYOUT: HALAMAN REGISTER (/register) "]
        direction TB

        subgraph R_TOP [" HEADER ROW "]
            direction LR
            R_LOGO["LOGO MUBEE BRANDING"] --- R_INFO["PENDAFTARAN AKUN BARU PENGGUNA"]
        end

        subgraph R_BODY [" MAIN CONTENT AREA "]
            direction LR

            subgraph R_LEFT [" FORM REGISTRASI UTAMA "]
                direction TB
                R_HERO["BIG FEATURE: Card Form Registrasi Akun Baru\n- Input Nama Lengkap\n- Input Alamat Email\n- Input Password & Konfirmasi Password\n- Checkbox Persetujuan Syarat & Ketentuan\n- Tombol: [ DAFTAR AKUN SEKARANG ]\n- Link: Sudah memiliki akun? Masuk di sini"]
            end

            subgraph R_RIGHT [" SIDEBAR KANAN (BENEFIT AKUN) "]
                direction TB
                R_SIDE1["BENEFIT ANGGOTA\n- Simpan Watchlist & Riwayat Tontonan Sync\n- Akses Streaming Server Kualitas HD"]
                R_SIDE2["PROMO\n- Gratis Uji Coba VIP 30 Hari"]
            end
        end

        subgraph R_FOOTER [" FOOTER ROW "]
            R_FOOT["Copyright @ 2026 Mubee Platform"]
        end

        R_TOP --> R_BODY
        R_BODY --> R_FOOTER
    end
```

---

## 📌 Kesimpulan Pemetaan Layout

Dengan gambar perancangan **Wireframe Block Layout** berbasis **Mermaid.js** di atas:
1. Setiap halaman pada aplikasi **Mubee** dipetakan sesuai dengan spesifikasi gambar yang diberikan:
   - **Header Row** (Logo & Search Bar)
   - **Menu Navigasi Row** (Baris Menu Utama)
   - **Big Feature Box** (Area Utama Banner / Player / Form)
   - **Recent Post / Watch History Box** (Area Riwayat Tontonan & Highlight Sidebar)
   - **Small Features Grid Boxes** (Grid Baris Kartu Konten / Kategori / Profil Aktor)
   - **Sidebar Kanan (Recent Post & News)**
   - **Footer Row** (Copyright)
2. Dokumentasi ini secara visual menggambarkan posisi tata letak UI/UX yang konsisten untuk seluruh rute dan pengontrol aplikasi.
