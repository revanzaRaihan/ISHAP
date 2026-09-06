# 🫁 ISHAP — Intelligent Screening for Health Awareness & Prevention

**ISHAP (Intelligent Screening for Health Awareness & Prevention)** adalah platform web berbasis medis dan kecerdasan buatan (AI) yang dirancang untuk membantu masyarakat melakukan skrining awal risiko Infeksi Saluran Pernapasan Akut (ISPA), memahami mekanisme penyakit (*patogenesis*), serta menemukan rujukan fasilitas kesehatan dan konsultasi telemedika secara cepat dan akurat.

---

## 🚀 Fitur Utama

1. **Skrining Mandiri Gejala Berbasis AI & Deterministik**:
   - Ekstraksi keluhan subjektif bahasa sehari-hari dengan **Google Gemini 2.5 Flash**.
   - Peta interaktif anatomi saluran pernapasan atas (*Upper*) dan bawah (*Lower*).
   - Penilaian risiko klinis dengan sistem pembobotan deterministik murni (*zero hallucination*).
2. **Health Report Card Minimalis**:
   - Ringkasan kondisi medis utama, skor kecocokan (*confidence rate*), dan kategori keparahan.
   - Penjelasan etiologi penyakit, faktor kerentanan tubuh, dan tanda bahaya (*red flags*).
3. **Pencarian Faskes Real-time (OpenStreetMap)**:
   - Menampilkan Rumah Sakit, Puskesmas, dan Klinik terdekat menggunakan OpenStreetMap Overpass API dan formula Haversine tanpa API key berbayar.
4. **Rujukan Telemedika Terfokus (Halodoc Deep Linking)**:
   - Mengarahkan rujukan online langsung ke kategori dokter spesialis yang sesuai (Spesialis Paru, THT, dan Anak).
5. **Widget Kualitas Udara Real-time (AQI)**:
   - Pemantauan indeks kualitas udara aktual dan partikel PM2.5 via Open-Meteo Air Quality API.

---

## 🛠️ Dokumentasi Tech Stack Lengkap

Daftar lengkap teknologi, pustaka, API, dan arsitektur sistem dapat dibaca di:
👉 **[Dokumentasi Tech Stack (docs/TECH_STACK.md)](docs/TECH_STACK.md)**  
👉 **[Bedah Direktori Kode & Alur Hubungan File (docs/ARCHITECTURE_BREAKDOWN.md)](docs/ARCHITECTURE_BREAKDOWN.md)**

Ringkasan cepat:
- **Backend**: Laravel 11.x (PHP 8.3 / 8.2+)
- **Database**: Eloquent ORM, SQLite (Local Dev & Testing), Supabase (PostgreSQL Cloud)
- **AI & NLP**: Google Gemini 2.5 Flash API (Structured JSON)
- **Clinical Engine**: Pure Deterministic Rule-based Scoring Engine
- **Frontend**: Laravel Blade, Tailwind CSS, Plus Jakarta Sans, Interactive SVG Anatomy
- **Geospasial & Peta**: Leaflet.js (v1.9.4) & OpenStreetMap Overpass API
- **Testing**: PHPUnit (Unit & Feature Tests) dengan Mocking `Http::fake`

---

## 💻 Panduan Instalasi Lokal

### 1. Prasyarat
- PHP `>= 8.2`
- Composer
- Node.js & NPM

### 2. Kloning dan Setup Dependensi
```bash
git clone https://github.com/revanzaRaihan/ISHAP.git
cd ISHAP

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install
```

### 3. Konfigurasi Lingkungan (.env)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Isi variabel penting di `.env`:
```env
# Google Gemini API untuk Ekstraksi Gejala AI
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-2.5-flash

# Supabase (Opsional jika menggunakan sync cloud)
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your_supabase_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_supabase_service_key
```

### 4. Migrasi & Seeder Database
```bash
php artisan migrate:fresh --seed
```

### 5. Menjalankan Aplikasi
```bash
# Jalankan server Laravel
php artisan serve

# (Opsional) Jalankan Vite jika mengembangkan asset frontend
npm run dev
```

Akses aplikasi di browser: `http://localhost:8000`.

---

## 🧪 Menjalankan Automated Tests

Seluruh skenario pengujian unit dan fitur telah dilengkapi faking API eksternal sehingga dapat dijalankan kapan saja:
```bash
php artisan test
```

---

## 📄 Lisensi
Platform ISHAP dikembangkan untuk kepentingan edukasi, penelitian, dan inisiatif kesehatan masyarakat.
