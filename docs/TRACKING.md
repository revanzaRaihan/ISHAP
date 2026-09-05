# ISHAP — Progress Tracking & Project Logbook

Dokumen ini mencatat seluruh status perkembangan fitur, pemenuhan arsitektur, catatan keamanan, dan langkah kerja berikutnya.

---

## 📌 Status Fitur & Komponen

| Fitur / Komponen | Status | Lokasi Berkas Utama | Keterangan |
| :--- | :---: | :--- | :--- |
| **Arsitektur & Aturan Coding** | ✅ Selesai | `AGENTS.md` | Memuat 8 batasan mutlak & aturan penamaan |
| **Environment Configuration** | ✅ Selesai | `.env.local`, `.env.example` | URL Supabase & API keys terkonfigurasi |
| **Skema Database & RLS** | ✅ Aktif | `supabase/schema.sql` | 9 tabel inti skrining ISPA aktif di Supabase dengan RLS |
| **Data Awal (Seed Data)** | ✅ Terisi | `supabase/seed.sql` | Master gejala, penyakit, bobot, dan dokter mitra terisi |
| **Pure Scoring Engine** | ✅ Selesai | `src/lib/scoring/screeningEngine.ts` | Algoritma pencocokan gejala tanpa dependensi UI/DB |
| **API Route Handlers (Server Layer)** | ✅ Selesai | `src/app/api/...` | Validasi Zod & Overpass API untuk faskes dinamis |
| **Database TypeScript Types** | ✅ Selesai | `src/lib/supabase/database.types.ts` | Type safety Supabase diselaraskan tanpa tabel faskes |
| **Supabase Client & Server** | ✅ Selesai | `src/lib/supabase/{client,server}.ts` | Terintegrasi SSR cookies dan admin client |
| **Komponen Screening Mandiri** | ✅ Selesai | `src/features/screening/...` | Form interaktif gejala & tampilan hasil perkiraan risiko |
| **Komponen Fasilitas Kesehatan** | ✅ Selesai (OSM Live) | `src/features/facilities/...` | Real-time OpenStreetMap Overpass API, radius kota, peta & rute Google Maps |
| **Komponen Konsultasi Dokter** | 🔄 Siap Integrasi | `src/features/consultation/...` | Model domain & service sudah siap |
| **UI Reusable & Layout** | ✅ Selesai | `src/components/{ui,layout}/...` | Button, Card, Badge, Navbar, Footer, CSS tokens |
| **Kompilasi & Build System** | ✅ Lulus 100% | Next.js 15.5 App Router | `npm run build` & `tsc` berhasil tanpa error |

---

## 🛡️ Catatan Keamanan & Desain Arsitektur
1. **Penyelarasan Desain Tanpa Tabel Faskes Lokal**:
   - Data faskes diambil secara dinamis & real-time via backend route handler (`/api/facilities/nearby`) yang mengontak OpenStreetMap (Overpass API).
   - Tabel `health_facilities` dan `facility_recommendations` dihapus karena tidak skalabel dan menimbulkan redundansi data.
   - Script SQL drop table telah disediakan di `supabase/migrations/20260905000001_drop_health_facilities.sql`.
2. **Server-Side Firewall & Caching**:
   - Route handler memvalidasi koordinat latitude, longitude, dan membatasi radius pencarian faskes agar tetap berada dalam cakupan kota pengguna (default radius 25 km).
3. **Scoring Terisolasi**:
   - Kalkulasi persentase kecocokan risiko dijalankan secara murni (pure function) di server.
4. **Isolasi Service Role**:
   - Kunci `SUPABASE_SERVICE_ROLE_KEY` hanya dapat diakses oleh server dan tidak pernah terekspos ke browser.

---

## 📋 Langkah Kerja Selanjutnya
1. [x] **Input API Key**: Memasukkan `NEXT_PUBLIC_SUPABASE_ANON_KEY` dan `SUPABASE_SERVICE_ROLE_KEY` ke `.env.local`.
2. [x] **Inisialisasi Database**: Menjalankan skrip skema dan seed di Supabase SQL Editor.
3. [x] **Verifikasi Koneksi Langsung**: Menguji alur skrining mandiri secara end-to-end (berhasil).
4. [x] **Penyempurnaan Halaman Fasilitas Kesehatan Terdekat (OSM Real-time)**: 
   - Menghapus tabel lokal `health_facilities` & `facility_recommendations`.
   - Mengintegrasikan OpenStreetMap Overpass API server-side secara gratis tanpa API key.
   - Menampilkan kartu faskes dengan estimasi jarak, penunjuk arah Google Maps, dan peta visual.
5. [ ] **Penyempurnaan Halaman Konsultasi Dokter**: Menampilkan kartu dokter online mitra dengan tombol langsung ke platform rujukan.
