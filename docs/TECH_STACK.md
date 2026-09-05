# Tech Stack — ISHAP (Intelligent Screening for Health Awareness & Prevention)

Daftar teknologi dan layanan yang digunakan di proyek **ISHAP (Intelligent Screening for Health Awareness & Prevention)**, dibuat dengan format list sederhana yang mudah dipahami.

---

### Tech Stack — Backend
1. **PHP (v8.3 / v8.2+)**
   Bahasa pemrograman utama di sisi server untuk memproses seluruh logika aplikasi.
2. **Laravel Framework (v11.x)**
   Framework PHP utama dengan pola MVC (Model-View-Controller) untuk menangani routing, controller, validasi form, dan middleware.
3. **Composer**
   Package manager untuk mengelola pustaka dan dependensi PHP.
4. **Artisan CLI**
   Tool bawaan Laravel untuk menjalankan migrasi database, seeder data master, dan command kustom.

---

### Tech Stack — Frontend
1. **Laravel Blade Templating**
   Engine template bawaan Laravel untuk menyusun tampilan halaman web, komponen modular, dan layout.
2. **Tailwind CSS**
   Framework CSS utility-first untuk styling tampilan yang modern, responsif di HP/laptop, dan pembuatan Health Report Card.
3. **Plus Jakarta Sans (Google Fonts)**
   Font modern dan bersih standar aplikasi medis dan dashboard.
4. **Interactive Vector SVG**
   Peta anatomi tubuh interaktif saluran pernapasan atas (*Upper*) dan bawah (*Lower*) yang bisa diklik langsung oleh pengguna.
5. **Leaflet.js (v1.9.4)**
   Library peta interaktif berbasis JavaScript untuk menampilkan sebaran lokasi fasilitas kesehatan terdekat.
6. **Vite (v8.x)**
   Build tool modern untuk mengompilasi dan mengoptimalkan aset frontend.

---

### Tech Stack — Database
1. **SQLite (Database Lokal)**
   Database lokal yang cepat dan ringan untuk kebutuhan pengembangan dan pengujian otomatis tanpa perlu setup server database terpisah.
2. **Supabase (PostgreSQL Cloud)**
   Database relasional PostgreSQL di cloud yang dilengkapi sistem keamanan Row Level Security (RLS).
3. **Laravel Eloquent ORM**
   Penghubung antara kode PHP dan tabel database (model penyakit, gejala, dan sesi skrining).

---

### Tech Stack — Feature & Integrasi Pihak Ketiga
1. **Google Gemini 2.5 Flash (AI & NLP)**
   Kecerdasan buatan dari Google AI Studio untuk mengekstrak cerita keluhan bebas pasien (misal: *"tenggorokan gatal dan batuk berdahak sudah 3 hari"*) secara otomatis menjadi pilihan gejala yang terstandarisasi.
2. **Pure Deterministic Screening Engine (Sistem Skrining Medis)**
   Algoritma penilaian klinis murni di backend yang menghitung persentase kecocokan (*confidence score*) dan tingkat risiko tanpa risiko halusinasi AI.
3. **OpenStreetMap Overpass API (Pencarian Faskes Terdekat)**
   Layanan pencarian data Rumah Sakit, Puskesmas, dan Klinik secara real-time berdasarkan koordinat GPS pengguna tanpa biaya API key.
4. **Haversine Distance Formula**
   Rumus matematika di backend untuk menghitung jarak akurat (dalam kilometer) antara lokasi pengguna dan faskes rujukan.
5. **Open-Meteo Air Quality API (Data Kualitas Udara)**
   API gratis untuk memantau data kualitas udara (US AQI dan partikel PM2.5) secara live di halaman beranda.
6. **Halodoc Deep Linking (Konsultasi Dokter Online)**
   Tombol rujukan langsung ke kategori spesialis Halodoc (Spesialis Paru, Spesialis THT, Spesialis Anak) sehingga platform tidak perlu menyimpan data dokter pribadi.
7. **IP-API (`ip-api.com`)**
   Layanan pendeteksi lokasi otomatis berdasarkan alamat IP jika pengguna tidak mengaktifkan izin GPS di browser.

---

### Tech Stack — Testing & Quality
1. **PHPUnit (v12.x)**
   Framework untuk automated testing (Unit Test untuk algoritma skrining dan Feature Test untuk alur skrining).
2. **Laravel HTTP Faking (`Http::fake`)**
   Sistem simulasi respons API eksternal (Gemini, OSM, Open-Meteo) agar pengujian aplikasi dapat berjalan cepat dan tanpa bergantung pada koneksi internet.
3. **Laravel Pint**
   Linter dan formatter kode otomatis agar susunan kode tetap rapi dan konsisten sesuai standar PSR-12.
