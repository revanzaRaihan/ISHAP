# 🏛️ Bedah Arsitektur & Direktori Kode — ISHAP

Dokumen ini membedah struktur direktori kode **ISHAP (Intelligent Screening for Health Awareness & Prevention)**, menjelaskan kegunaan tiap berkas, fungsi inti (*core functions*), serta hubungan keterkaitan antar lapisan (**Model ➔ Controller ➔ Service ➔ View**).

---

## 🔄 Diagram Alur Hubungan Antar Lapisan

```
[ Pengguna / Browser ]
         │
         ▼
[ routes/web.php ] ── Menentukan rute URL & mengarahkan ke Controller
         │
         ▼
[ Controllers ] ── Menerima request, validasi input, memanggil Model & Service
   │         │
   │         ├──► [ Services ] (ScreeningEngine, Gemini AI, Overpass OSM, Supabase)
   │         │
   │         └──► [ Models & DB ] (Symptom, Disease, ScreeningSession, ScreeningResult)
   │
   ▼
[ Views (Blade) ] ── Menampilkan data ke pengguna (HTML + Tailwind CSS + SVG + Leaflet)
```

---

## 1. 🗄️ Lapisan Model (`app/Models/`)

Model bertindak sebagai representasi tabel database dan pendefinisi relasi data menggunakan Laravel Eloquent ORM.

### 1. `Disease.php` (Model Penyakit)
* **Tabel Terkait**: `diseases`
* **Kegunaan**: Menyimpan data master penyakit pernapasan (Faringitis, Bronkitis, Pneumonia, Common Cold, Asma, Influenza).
* **Atribut Utama**: `name`, `severity_level`, `description`, `pathogenesis_overview`, `pathogenesis_causes`, `recovery_tips`, `red_flags`.
* **Core Relations**:
  * `symptoms()`: Relasi `BelongsToMany` ke `Symptom` melalui tabel pivot `symptom_disease_map` lengkap dengan bobot (`weight`).
* **Hubungan Antar Berkas**:
  * Digunakan oleh `ScreeningEngine` untuk membaca nama dan tingkat keparahan saat penilaian risiko.
  * Ditampilkan di View `screening/result.blade.php` (sebagai Primary Assessment & kartu patogenesis).

### 2. `Symptom.php` (Model Gejala)
* **Tabel Terkait**: `symptoms`
* **Kegunaan**: Menyimpan daftar gejala medis standar ISPA (demam, batuk kering, sesak, hidung tersumbat, nyeri telan, dll).
* **Atribut Utama**: `id`, `name`, `category` (Upper, Lower, General, Systemic).
* **Core Relations**:
  * `diseases()`: Relasi `BelongsToMany` ke `Disease` melalui `symptom_disease_map`.
* **Hubungan Antar Berkas**:
  * Ditampilkan di View `screening/index.blade.php` sebagai daftar checklist per kategori anatomi.
  * Digunakan `GeminiSymptomMapperService` sebagai kamus pencocokan gejala dari teks bebas pasien.

### 3. `SymptomDiseaseMap.php` (Model Bobot Relasi Gejala-Penyakit)
* **Tabel Terkait**: `symptom_disease_map`
* **Kegunaan**: Menyimpan bobot signifikansi klinis (`weight`: 0.5 – 3.0) antara suatu gejala terhadap penyakit tertentu.
* **Core Relations**:
  * `belongsTo(Symptom::class)` dan `belongsTo(Disease::class)`.
* **Hubungan Antar Berkas**:
  * Menjadi masukan utama bagi `ScreeningEngine` untuk menghitung skor persentase kecocokan.

### 4. `ScreeningSession.php` (Model Sesi Skrining)
* **Tabel Terkait**: `screening_sessions`
* **Kegunaan**: Mencatat setiap kali pengguna memulai atau menyelesaikan sesi skrining mandiri (menggunakan UUID).
* **Core Relations**:
  * `symptoms()`: Relasi `BelongsToMany` ke `Symptom` melalui `session_symptoms`.
  * `results()`: Relasi `HasMany` ke `ScreeningResult` (diurutkan dari skor kecocokan tertinggi).
  * `topResult()`: Relasi `HasOne` mengambil hasil dengan `confidence_score` tertinggi.
* **Hubungan Antar Berkas**:
  * Dibuat saat pengguna menekan tombol "Analisis Gejala" di `ScreeningController@submit`.
  * Dibaca di `ScreeningController@result` untuk menampilkan Health Report Card.

### 5. `ScreeningResult.php` (Model Hasil Penilaian Skrining)
* **Tabel Terkait**: `screening_results`
* **Kegunaan**: Menyimpan kalkulasi skor kecocokan hasil pemrosesan engine untuk setiap penyakit dalam suatu sesi.
* **Atribut Utama**: `confidence_score`, `matched_symptoms_count`, `total_symptoms_for_disease`, `reasoning`.
* **Core Relations**:
  * `belongsTo(ScreeningSession::class)` & `belongsTo(Disease::class)`.

### 6. `SessionSymptom.php` (Model Pivot Gejala Terpilih)
* **Tabel Terkait**: `session_symptoms`
* **Kegunaan**: Menyimpan relasi ID gejala yang dipilih pengguna pada sesi tertentu.

---

## 2. ⚙️ Lapisan Service (`app/Services/`)

Service Layer memisahkan logika bisnis yang kompleks, algoritma klinis, koneksi AI, dan API eksternal agar Controller tetap bersih (*lean controller*).

### 1. `ScreeningEngine.php` (Mesin Penilaian Klinis)
* **Kegunaan**: Algoritma deterministik murni (*pure function*) tanpa efek samping untuk menghitung perkiraan risiko ISPA.
* **Core Function**:
  * `calculateScreeningRisk(array $selectedSymptomIds, array $weights, array $diseases): array`
    * Menghitung total bobot gejala yang cocok dibagi total kemungkinan bobot penyakit.
    * Menghasilkan persentase `confidence_score` (0–100%) dan teks alasan klinis (*reasoning*).
    * Mengurutkan hasil dari probabilitas risiko tertinggi ke terendah.
* **Karakteristik**: Bebas dari halusinasi AI (100% konsisten secara matematis).

### 2. `GeminiSymptomMapperService.php` (Integrasi Google AI Studio)
* **Kegunaan**: Menggunakan model Google **Gemini 2.5 Flash** untuk Natural Language Processing (NLP) keluhan pasien.
* **Core Function**:
  * `mapComplaintToSymptoms(string $complaint): array`
    * Mengirim teks cerita bebas pasien ke endpoint Gemini via HTTP POST.
    * Memaksa model menghasilkan output JSON terstruktur (`responseMimeType: application/json`).
    * Mengembalikan array ID gejala yang otomatis mencentang kotak checklist di frontend.

### 3. `OverpassOsmService.php` (Pencarian Faskes Geospasial)
* **Kegunaan**: Menghubungi OpenStreetMap Overpass API untuk mencari faskes secara langsung.
* **Core Functions**:
  * `findNearbyFacilities(float $lat, float $lon, float $radiusKm, int $limit): array`
    * Melakukan query Overpass QL untuk tag `amenity=hospital`, `amenity=clinic`, dan `healthcare=centre/clinic`.
  * `calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float`
    * Menghitung jarak garis lurus akurat (dalam kilometer) antara koordinat pengguna dan faskes.

### 4. `SupabaseService.php` (Klien Database Cloud Supabase)
* **Kegunaan**: Integrasi dengan database Supabase PostgreSQL via PostgREST API.
* **Core Functions**:
  * `saveScreeningSession(...)`: Melakukan sinkronisasi riwayat sesi dan hasil skrining ke tabel Supabase.
  * `syncRemoteToLocal()`: Sinkronisasi data master (gejala dan penyakit) dari Supabase ke database lokal jika tabel lokal kosong (*self-healing mechanism*).

---

## 3. 🎮 Lapisan Controller (`app/Http/Controllers/`)

Controller bertindak sebagai pengatur lalu lintas request HTTP, melakukan validasi data, memanggil Model/Service, lalu mengembalikan Response (View HTML atau JSON).

### 1. `ScreeningController.php` (Alur Skrining & Hasil)
* **Core Functions**:
  * `index()`: Mengambil daftar semua gejala yang dikelompokkan berdasarkan kategori anatomi dan merender view `screening.index`. Memiliki mekanisme *self-healing* data master jika tabel lokal kosong.
  * `extractSymptoms(Request $request, GeminiSymptomMapperService $gemini)`: Endpoint AJAX ber-rate-limit (`throttle:15,1`) untuk mengekstraksi keluhan teks pasien via AI.
  * `submit(Request $request, ScreeningEngine $engine)`: Memvalidasi minimal 1 gejala terpilih, membuat `ScreeningSession`, menghitung skor via `ScreeningEngine`, menyimpan ke `ScreeningResult`, lalu me-redirect ke halaman hasil.
  * `result(string $sessionId, OverpassOsmService $osmService)`: Mengambil hasil skrining, menentukan kategori dokter spesialis Halodoc secara dinamis (THT untuk saluran atas vs Pulmonologi untuk saluran bawah), mencari 1 faskes terdekat via OSM, dan merender `screening.result`.
  * `detectUserLocation(Request $request)`: Helper deteksi lokasi pengguna berdasarkan IP client via `ip-api.com`.

### 2. `HomeController.php` (Halaman Beranda & Edukasi)
* **Core Functions**:
  * `index()`: Menampilkan landing page, menghitung jumlah penyakit/gejala, serta mengambil status indeks kualitas udara aktual.
  * `getAirQualityData()`: Mengontak Open-Meteo Air Quality API secara gratis untuk mendapatkan data US AQI, PM2.5, dan PM10 terkini.

### 3. `FacilityController.php` (Halaman Direktori Faskes)
* **Core Functions**:
  * `index(Request $request, OverpassOsmService $osmService)`: Menerima koordinat GPS atau preset kota, memanggil `OverpassOsmService`, lalu mengembalikan data ke tampilan peta interaktif `facilities.index` atau JSON API.

### 4. `DoctorController.php` (Halaman Direktori Dokter Mitra)
* **Core Functions**:
  * `index()`: Menyajikan katalog kategori konsultasi telemedika (Spesialis Paru, Spesialis THT, dan Spesialis Anak) yang langsung mengarah ke tautan Halodoc tanpa menyimpan data dokter individual di database.

### 5. `AqiController.php` (API Kualitas Udara Berdasarkan Koordinat)
* **Core Functions**:
  * `getAqiByCoords(Request $request)`: Endpoint API pendukung untuk mengambil data feed kualitas udara berdasarkan koordinat lintang dan bujur.

---

## 4. 🖥️ Lapisan View (`resources/views/`)

View bertanggung jawab menampilkan antarmuka visual (UI/UX) kepada pengguna menggunakan Blade Templating, Tailwind CSS, SVG, dan Leaflet.js.

### 1. `layouts/app.blade.php` (Template Induk)
* **Kegunaan**: Kerangka dasar seluruh halaman web.
* **Fitur Utama**:
  * Konfigurasi Tailwind CSS kustom dan font *Plus Jakarta Sans*.
  * Navbar responsif dengan efek kaca (*glassmorphism*).
  * Kontainer pesan flash (*success/error alert*).
  * Footer aplikasi yang memuat kepanjangan resmi ISHAP, navigasi cepat, dan *medical disclaimer* (penafian medis).

### 2. `home.blade.php` (Beranda & Edukasi ISPA)
* **Kegunaan**: Landing page edukatif untuk meningkatkan kesadaran masyarakat tentang ISPA.
* **Komponen**:
  * **Hero Section**: Pengenalan platform dan tombol CTA menuju skrining.
  * **Live AQI Widget**: Indikator kualitas udara real-time (Aman / Sedang / Tidak Sehat) beserta konsentrasi debu halus PM2.5.
  * **Pilar Edukasi ISPA**: Perbedaan Infeksi Saluran Pernapasan Atas (ISPA Atas) dan Bawah (ISPA Bawah).
  * **Panduan 3 Langkah Cepat**: Skrining Mandiri ➔ Evaluasi Patogenesis ➔ Rujukan Faskes/Dokter.

### 3. `screening/index.blade.php` (Formulir Skrining Interaktif)
* **Kegunaan**: Halaman input keluhan dan pemilihan gejala.
* **Komponen**:
  * **AI Keluhan Box**: Textarea input bahasa sehari-hari dengan tombol "Deteksi Otomatis via AI" (memanggil AJAX ke `ScreeningController@extractSymptoms`).
  * **Interactive Body Anatomy Map (SVG)**: Ilustrasi visual organ saluran napas atas (hidung, faring, laring) dan bawah (trakea, bronkus, paru-paru) dengan radio selector interaktif.
  * **Checklist Gejala Terkategori**: Daftar gejala per anatomi yang otomatis tercentang jika dideteksi oleh AI atau dipilih secara manual.

### 4. `screening/result.blade.php` (Health Report Card & Rujukan)
* **Kegunaan**: Halaman ringkasan hasil penilaian skrining klinis.
* **Komponen**:
  * **Health Report Card**: Kartu ringkasan terstruktur dengan persentase kecocokan, status keparahan (Ringan/Sedang/Berat), daftar gejala yang cocok vs belum muncul.
  * **Kartu Edukasi Patogenesis ("Mengapa Anda Mengalami Gejala Ini?")**: Menjelaskan penyebab mikrobiologi, pintu masuk kuman (droplet), dan faktor pemicu imunitas turun.
  * **Tanda Bahaya (*Red Flags*) & Tips Pemulihan**: Edukasi mandiri kapan harus segera ke IGD.
  * **Compact Referral Cards**:
    * **1 Faskes Terdekat**: Nama RS/Puskesmas, estimasi jarak km, dan tombol rute navigasi Google Maps.
    * **1 Dokter Spesialis Halodoc**: Deep link otomatis ke kategori dokter spesialis yang sesuai (THT untuk radang tenggorokan/pilek; Pulmonologi untuk batuk persisten/paru).

### 5. `facilities/index.blade.php` (Peta Faskes Terdekat)
* **Kegunaan**: Eksplorasi faskes di sekitar pengguna.
* **Komponen**:
  * **Peta Leaflet.js**: Peta interaktif dengan marker warna faskes dan popup detail.
  * **Tombol "Gunakan Lokasi Saya (GPS)"**: Membaca GPS browser untuk mencari faskes terdekat.
  * **Filter Preset Kota**: Pilihan cepat untuk kota-kota besar (Jakarta, Surabaya, Bandung, Medan, Makassar).
  * **Grid Kartu Faskes**: Daftar RS, Puskesmas, dan Klinik dengan tombol telepon dan Google Maps.

### 6. `doctors/index.blade.php` (Direktori Dokter Mitra)
* **Kegunaan**: Halaman katalog rujukan konsultasi telemedika online.
* **Komponen**:
  * 3 kartu kategori dokter spesialis (Spesialis Paru, Spesialis THT, Spesialis Anak) yang bersih dan langsung mengarahkan pengguna ke platform Halodoc.

---

## 5. 🛣️ Lapisan Routing (`routes/web.php`)

Menghubungkan URL browser ke Controller terkait:

| Rute URL | Method HTTP | Controller & Action | Deskripsi |
| :--- | :---: | :--- | :--- |
| `/` | `GET` | `HomeController@index` | Beranda, edukasi ISPA, dan widget AQI |
| `/api/aqi` | `GET` | `AqiController@getAqiByCoords` | API kualitas udara koordinat |
| `/screening` | `GET` | `ScreeningController@index` | Formulir skrining gejala mandiri |
| `/screening/extract-symptoms` | `POST` | `ScreeningController@extractSymptoms` | Ekstraksi gejala via Google Gemini AI (AJAX) |
| `/screening` | `POST` | `ScreeningController@submit` | Proses kalkulasi skor & simpan sesi |
| `/screening/{sessionId}/result` | `GET` | `ScreeningController@result` | Tampilan Health Report Card & Rujukan |
| `/facilities` | `GET` | `FacilityController@index` | Direktori & peta faskes terdekat (OSM) |
| `/doctors` | `GET` | `DoctorController@index` | Direktori telemedika Halodoc |

---

## 6. 📊 Matriks Keterkaitan Antar File

| Dari File (Source) | Memanggil / Bergantung Pada (Target) | Alasan Keterkaitan |
| :--- | :--- | :--- |
| `routes/web.php` | Semua Controller | Mendaftarkan URL endpoint ke fungsi controller |
| `ScreeningController.php` | `Symptom`, `Disease`, `ScreeningSession` | Mengambil dan menyimpan data skrining |
| `ScreeningController.php` | `ScreeningEngine.php` | Menghitung persentase kecocokan klinis |
| `ScreeningController.php` | `GeminiSymptomMapperService.php` | Mengekstrak teks keluhan pasien |
| `ScreeningController.php` | `OverpassOsmService.php` | Mengambil 1 faskes terdekat untuk kartu hasil |
| `ScreeningController.php` | `SupabaseService.php` | Sinkronisasi riwayat sesi ke database cloud |
| `ScreeningController.php` | `screening/index.blade.php` & `result.blade.php` | Mengirim data gejala dan hasil evaluasi ke view |
| `FacilityController.php` | `OverpassOsmService.php` | Mengambil data RS/Puskesmas terdekat |
| `FacilityController.php` | `facilities/index.blade.php` | Menampilkan koordinat faskes ke Leaflet map |
| `HomeController.php` | Open-Meteo Air Quality API | Menampilkan data polusi udara real-time di beranda |
| `home.blade.php`, dsb. | `layouts/app.blade.php` | Mewarisi layout induk, navbar, CSS, dan footer |
