# ISHAP — Progress Tracking & Project Logbook

Dokumen ini mencatat seluruh status perkembangan fitur, pemenuhan arsitektur, catatan keamanan, dan langkah kerja berikutnya.

---

## 📌 Status Fitur & Komponen

| Fitur / Komponen | Status | Lokasi Berkas Utama | Keterangan |
| :--- | :---: | :--- | :--- |
| **Arsitektur & Aturan Coding** | ✅ Selesai | `AGENTS.md` | Memuat 8 batasan mutlak & aturan penamaan |
| **Environment Configuration** | ✅ Selesai | `.env.local`, `.env.example` | URL Supabase & API keys terkonfigurasi |
| **Skema Database & RLS** | ✅ Aktif | `supabase/schema.sql` | 11 tabel aktif di Supabase dengan RLS |
| **Data Awal (Seed Data)** | ✅ Terisi | `supabase/seed.sql` | Master gejala, penyakit, bobot, faskes terisi |
| **Pure Scoring Engine** | ✅ Selesai | `src/lib/scoring/screeningEngine.ts` | Algoritma pencocokan gejala tanpa dependensi UI/DB |
| **API Route Handlers (Server Layer)** | ✅ Selesai | `src/app/api/...` | Dilengkapi validasi Zod untuk mencegah injeksi |
| **Database TypeScript Types** | ✅ Selesai | `src/lib/supabase/database.types.ts` | Type safety lengkap untuk Supabase client |
| **Supabase Client & Server** | ✅ Selesai | `src/lib/supabase/{client,server}.ts` | Terintegrasi SSR cookies dan admin client |
| **Komponen Screening Mandiri** | ✅ Selesai | `src/features/screening/...` | Form interaktif gejala & tampilan hasil perkiraan risiko |
| **Komponen Fasilitas Kesehatan** | 🔄 Siap Integrasi | `src/features/facilities/...` | API nearby & service sudah siap |
| **Komponen Konsultasi Dokter** | 🔄 Siap Integrasi | `src/features/consultation/...` | Model domain & service sudah siap |
| **UI Reusable & Layout** | ✅ Selesai | `src/components/{ui,layout}/...` | Button, Card, Badge, Navbar, Footer, CSS tokens |
| **Kompilasi & Build System** | ✅ Lulus 100% | Next.js 15.5 App Router | `npm run build` berhasil tanpa error |

---

## 🛡️ Catatan Keamanan (Security Mitigation)
Untuk mencegah eksploitasi, pembobolan skor risiko, dan injeksi payload:
1. **Server-Side Firewall**: Seluruh mutasi sesi dan pengiriman gejala diproses melalui Next.js API Route Handlers di server.
2. **Sanitasi Skema (Zod)**: Semua ID divalidasi sebagai UUID valid. Array gejala divalidasi tidak boleh kosong. Koordinat geolokasi dibatasi pada rentang koordinat global yang valid.
3. **Scoring Terisolasi**: Kalkulasi persentase kecocokan risiko dijalankan di server, mencegah manipulasi skor dari sisi browser.
4. **Isolasi Service Role**: Kunci `SUPABASE_SERVICE_ROLE_KEY` hanya dapat diakses oleh server (`createAdminClient`) dan tidak pernah dikirim ke browser.

---

## 📋 Langkah Kerja Selanjutnya
1. [x] **Input API Key**: Memasukkan `NEXT_PUBLIC_SUPABASE_ANON_KEY` dan `SUPABASE_SERVICE_ROLE_KEY` ke `.env.local`.
2. [x] **Inisialisasi Database**: Menjalankan skrip skema dan seed di Supabase SQL Editor.
3. [x] **Verifikasi Koneksi Langsung**: Menguji alur skrining mandiri secara end-to-end (berhasil).
4. [ ] **Penyempurnaan Halaman Fasilitas Kesehatan Terdekat**: Menampilkan daftar Puskesmas/RS terdekat dengan estimasi jarak (km) dan link petunjuk arah.
5. [ ] **Penyempurnaan Halaman Konsultasi Dokter**: Menampilkan kartu dokter online mitra dengan tombol langsung ke platform.
