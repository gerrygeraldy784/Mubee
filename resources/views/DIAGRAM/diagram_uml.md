# 🏛️ Class Diagram (UML) - Platform Streaming Mubee (Tugas 1)

Dokumen ini berisi **Class Diagram (UML)** yang menggambarkan struktur kelas data (*Eloquent Models*) dan pengontrol sistem (*Controllers*) yang ada pada aplikasi **Mubee (K-Drama & K-Movie Streaming Platform)** beserta hubungan antar kelasnya.

---

## 📐 Diagram Kelas (Class Diagram)

```mermaid
classDiagram
    direction TB

    class User {
        +BigInt id
        +String name
        +String email
        +String password
        +Timestamp email_verified_at
        +setting() UserSetting
        +myList() MyList[]
        +bookmarks() Bookmark[]
        +watchHistory() WatchHistory[]
        +videoProgress() VideoProgress[]
    }

    class UserSetting {
        +BigInt id
        +BigInt user_id
        +String theme_accent
        +Boolean autoplay
        +String playback_quality
        +String language
        +user() User
    }

    class MyList {
        +BigInt id
        +BigInt user_id
        +BigInt tmdb_id
        +String media_type
        +String title
        +String poster_path
        +Float vote_average
        +user() User
    }

    class Bookmark {
        +BigInt id
        +BigInt user_id
        +BigInt tmdb_id
        +String type
        +String title
        +String poster_path
        +user() User
    }

    class WatchHistory {
        +BigInt id
        +BigInt user_id
        +BigInt tmdb_id
        +String media_type
        +Int season_number
        +Int episode_number
        +Timestamp watched_at
        +user() User
    }

    class VideoProgress {
        +BigInt id
        +BigInt user_id
        +BigInt tmdb_id
        +String episode_id
        +Int last_position_seconds
        +Boolean is_finished
        +user() User
    }

    class ViewCount {
        +BigInt id
        +BigInt tmdb_id
        +String episode_id
        +Int views_count
    }

    class DashboardController {
        +index()
        +search()
        +show()
        +dramas()
        +movies()
        +genres()
        +actors()
        +myList()
        +toggleMyList()
        +settings()
    }

    class MovieController {
        +index()
        +show()
        +toggleBookmark()
    }

    class TvController {
        +show()
        +watchEpisode()
    }

    class VideoProgressController {
        +storeOrUpdate()
        +getProgress()
    }

    User "1" <|-- "1" UserSetting : Memiliki 1 Pengaturan
    User "1" <|-- "*" MyList : Menyimpan Koleksi
    User "1" <|-- "*" Bookmark : Menandai Favorit
    User "1" <|-- "*" WatchHistory : Mencatat Riwayat
    User "1" <|-- "*" VideoProgress : Menyimpan Durasi Terakhir
    DashboardController ..> User : Mengelola Data Penonton
    DashboardController ..> MyList : Olah Daftar Saya
    MovieController ..> Bookmark : Olah Penanda Film
    TvController ..> WatchHistory : Olah Riwayat Menonton
    VideoProgressController ..> VideoProgress : Olah Progress Durasi
```

---

## 💡 Penjelasan Kelas Data & Pengontrol (Bahasa Awam)

### 👤 1. Kelas Data Utama (*Models*)
* **User (Akun Penonton)**: Menyimpan data dasar akun pengguna seperti ID, nama, alamat email, dan kata sandi yang telah dienkripsi.
* **UserSetting (Pengaturan Pengguna)**: Menyimpan pilihan warna layar (tema), setelan kualitas gambar video, dan opsi putar otomatis. Setiap 1 penonton memiliki 1 pengaturan.
* **MyList (Daftar Favorit Saya)**: Menyimpan judul-judul film atau drama Korea yang dimasukkan oleh penonton ke dalam daftar simpanan.
* **Bookmark (Penanda Cepat)**: Menyimpan penanda film yang disukai penonton untuk dibuka kembali dengan cepat.
* **WatchHistory (Riwayat Menonton)**: Catatan histori judul film, episode, dan jam kapan penonton menyaksikan tayangan tersebut.
* **VideoProgress (Durasi Terakhir)**: Menyimpan titik detik terakhir saat penonton berhenti/pause menonton agar tayangan bisa dilanjutkan (*Continue Watching*).
* **ViewCount (Total Penonton)**: Menghitung total berapa kali suatu film atau episode drama telah ditonton oleh seluruh pengguna.

---

### ⚙️ 2. Kelas Pengontrol Logika (*Controllers*)
* **DashboardController**: Pengatur utama layar aplikasi (menampilkan film yang sedang tren, filter genre, pencarian judul, dan daftar aktor oppa/eonni).
* **MovieController**: Pengatur khusus pemutaran dan informasi detail film layar lebar (K-Movie).
* **TvController**: Pengatur pemutaran episode dan musim serial drama Korea (K-Drama).
* **VideoProgressController**: Pengatur simpan-otomatis titik durasi tontonan secara *real-time* lewat belakang layar (*AJAX*).
