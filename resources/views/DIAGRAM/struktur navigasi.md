# 🧭 Pemodelan Struktur Navigasi Aplikasi Mubee (Tugas 1)

Dokumen ini berisi perancangan **Struktur Navigasi (Navigation Structure)** untuk aplikasi **Mubee (Platform Streaming Film & Drama Korea Berbasis Website)**. Sistem ini menerapkan **Struktur Navigasi Campuran (Composite Navigation)**, yang mengombinasikan alur linier/hirarki (*Authentication ke Dashboard ke Player*) dan alur non-linier (*Bebas navigasi via Navbar*).

---

## 📐 1. Diagram Pohon Struktur Navigasi Utama

```mermaid
flowchart TD
    %% 1. TINGKAT GERBANG UTAMA
    subgraph Header_Area ["🌐 TINGKAT UTAMA (LANDING PAGE APLIKASI MUBEE)"]
        LandingPage([Halaman Utama / Landing Page Mubee])
    end

    %% 2. TINGKAT AUTENTIKASI PENGUNJUNG
    subgraph Area_Guest ["🔐 AREA PENGUNJUNG (GUEST / SEBELUM LOGIN)"]
        G1[Form Login]
        G2[Form Register]
        G3[Form Lupa Password]
    end

    %% 3. TINGKAT PENGGUNA TERAUTENTIKASI
    subgraph Area_User ["👤 AREA PENGGUNA TERAUTENTIKASI (DASHBOARD & NAVBAR)"]
        Dashboard[Dashboard Utama]
        M1[Serial Drama Korea]
        M2[Film Bioskop K-Movie]
        M3[Kategori Genre]
        M4[Aktor Oppa & Eonni]
        M5[Pencarian Realtime]
        M6[Koleksi Daftar Saya]
        M7[Pengaturan Akun & Layar]
    end

    %% 4. TINGKAT DETAIL KONTEN & STREAMING
    subgraph Area_Content ["🎬 AREA DETAIL KONTEN & STREAMING VIDEO"]
        DetailPage[Halaman Detail Film / Drama]
        PlayerPage[Pemutar Video Stream & Auto Progress]
    end

    %% Hubungan Alur Navigasi
    LandingPage --> G1
    LandingPage --> G2
    G1 --> G3
    G2 --> G1
    G1 -- Login Berhasil --> Dashboard

    Dashboard --> M1
    Dashboard --> M2
    Dashboard --> M3
    Dashboard --> M4
    Dashboard --> M5
    Dashboard --> M6
    Dashboard --> M7

    M1 --> DetailPage
    M2 --> DetailPage
    M3 --> DetailPage
    M4 --> DetailPage
    M5 --> DetailPage
    M6 --> DetailPage

    Dashboard --> DetailPage
    Dashboard -- Resume Play --> PlayerPage
    DetailPage --> PlayerPage
```

---

## 📋 2. Tabel Matriks Struktur Navigasi Halaman

Tabel di bawah ini menjelaskan fungsionalitas, alur perpindahan, dan tujuan dari setiap kode halaman pada aplikasi Mubee:

### 🔐 A. Tingkatan Autentikasi Pengunjung (Guest Level)
| Kode Halaman | Nama Halaman / Menu | Fungsi Utilitas Halaman | Halaman Tujuan Selanjutnya |
| :---: | :--- | :--- | :--- |
| **H-01** | Halaman Login | Memasukkan email & password untuk masuk ke aplikasi | Dashboard Utama (`H-04`) |
| **H-02** | Halaman Registrasi | Mendaftarkan akun penonton baru | Halaman Login (`H-01`) |
| **H-03** | Halaman Lupa Password | Mengajukan pemulihan kata sandi akun | Halaman Login (`H-01`) |

---

### 👤 B. Tingkatan Beranda & Kategori Navigasi (Dashboard Level)
| Kode Halaman | Nama Halaman / Menu | Fungsi Utilitas Halaman | Halaman Tujuan Selanjutnya |
| :---: | :--- | :--- | :--- |
| **H-04** | Dashboard Utama | Pusat rekomendasi film tren, banner hot, & continue watching | Seluruh Menu Navbar & Detail Film |
| **H-05** | Serial Drama Korea | Katalog serial K-Drama terpopuler rilis terbaru | Halaman Detail Film (`H-12`) |
| **H-06** | Film Bioskop (K-Movie) | Katalog film layar lebar Korea Selatan | Halaman Detail Film (`H-12`) |
| **H-07** | Kategori Genre | Filter tayangan berdasarkan genre (Romance, Action, dsb) | Halaman Detail Film (`H-12`) |
| **H-08** | Aktor & Aktris Favorit | Katalog profil & karya pemain drama Oppa & Eonni | Halaman Detail Film (`H-12`) |
| **H-09** | Pencarian Real-time | Mencari judul film/drama atau nama pemain secara langsung | Halaman Detail Film (`H-12`) |
| **H-10** | Koleksi Daftar Saya | Menampilkan daftar tayangan simpanan favorit pengguna | Halaman Detail Film (`H-12`) |
| **H-11** | Pengaturan Akun | Menyesuaikan tema warna layar, kualitas video, & bahasa | Dashboard Utama (`H-04`) |

---

### 🎬 C. Tingkatan Detail Konten & Pemutar Video (Streaming Level)
| Kode Halaman | Nama Halaman / Menu | Fungsi Utilitas Halaman | Halaman Tujuan Selanjutnya |
| :---: | :--- | :--- | :--- |
| **H-12** | Halaman Detail Film | Menampilkan sinopsis lengkap, skor IMDB, cast, & trailer | Player Stream Video (`H-13`) |
| **H-13** | Player Stream Video | Memutar video stream & otomatis menyimpan detik menit tontonan | Dashboard / Halaman Detail |
