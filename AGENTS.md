# ISHAP — Panduan Arsitektur & Aturan Coding

Aplikasi skrining mandiri ISPA berbasis Next.js dan Supabase.

## Peran AI Agent
Mengimplementasikan fitur sesuai arsitektur dan skema yang telah ditentukan. Tidak mengambil keputusan arsitektur sepihak tanpa konfirmasi.

---

## Struktur Direktori Wajib
```
src/
├── app/                                  # MURNI ROUTING — tidak boleh ada logika di sini
│   ├── page.tsx                          # landing page (edukasi + AQI)
│   ├── screening/
│   │   ├── page.tsx                      # panggil <ScreeningChat />
│   │   └── [sessionId]/
│   │       └── result/
│   │           └── page.tsx              # panggil <ScreeningResult />
│   └── api/                              # Route handlers — tipis, cuma panggil services
│       ├── screening-sessions/
│       │   ├── route.ts                  # POST: buat sesi baru
│       │   └── [sessionId]/
│       │       ├── symptoms/route.ts     # POST: submit gejala terpilih
│       │       └── result/route.ts       # GET: ambil hasil skrining
│       └── facilities/
│           └── nearby/route.ts           # GET: faskes terdekat by lat/long
│
├── components/                           # UI reusable lintas fitur
│   ├── ui/                               # Button, Card, Badge, Checkbox
│   └── layout/                           # Navbar, Footer
│
├── features/                             # Logika bisnis per domain
│   ├── screening/
│   │   ├── components/                   # SymptomChecklist, ChatBubble, ResultCard, RiskBadge
│   │   ├── hooks/                        # useScreeningSession.ts, useSymptomChecklist.ts
│   │   ├── services/                     # screeningService.ts — satu-satunya pintu ke Supabase
│   │   └── types/                        # screening.types.ts
│   │
│   ├── facilities/
│   │   ├── components/                   # FacilityCard, FacilityMap
│   │   ├── hooks/                        # useNearbyFacilities.ts
│   │   ├── services/                     # facilityService.ts
│   │   └── types/
│   │
│   └── consultation/
│       ├── components/                   # DoctorProfileCard
│       ├── services/                     # consultationService.ts
│       └── types/
│
├── lib/
│   ├── supabase/
│   │   ├── client.ts                     # browser client (anon key)
│   │   ├── server.ts                     # server client (dipakai di route handler/server component)
│   │   └── database.types.ts             # hasil supabase gen types typescript
│   └── scoring/
│       └── screeningEngine.ts            # pure function: gejala[] -> hasil skrining[]
│
└── middleware.ts                         # opsional, untuk auth/session
```

---

## 8 Batasan yang Tidak Boleh Dilanggar
1. **Akses Database Terpusat**: Jangan panggil `supabase.from(...)` langsung dari komponen React atau dari dalam `src/app/**`. Semua akses database wajib melalui `features/*/services/*.ts`.
2. **Integritas Skema**: Jangan menambah, mengubah, atau menghapus kolom/tabel di skema database tanpa menyebutkan secara eksplisit perubahan apa dan alasannya — tunggu konfirmasi sebelum menjalankan migrasi.
3. **Keamanan Kunci API**: Jangan menaruh `service_role` key Supabase di kode yang berjalan di client/browser. Service role hanya boleh dipakai di server (route handler atau server component).
4. **Konsistensi Struktur**: Jangan membuat folder atau pola struktur baru di luar pola `components/hooks/services/types` tanpa konfirmasi terlebih dahulu.
5. **Manajemen Dependensi**: Jangan menambahkan dependency/library baru tanpa menyebutkan alasan singkat kenapa dibutuhkan.
6. **Pure Function Skoring**: Algoritma skrining (pencocokan gejala-penyakit) wajib berupa pure function di `src/lib/scoring/` — tidak boleh bercampur dengan kode Supabase ataupun kode UI.
7. **Konvensi Penamaan**:
   - Tabel & kolom database: `snake_case`
   - Komponen React: `PascalCase`
   - Hooks: camelCase diawali `use` (e.g. `useScreeningSession`)
   - Services: camelCase diakhiri `Service` (e.g. `screeningService`)
8. **Terminologi Medis**: Ini aplikasi **SKRINING MANDIRI**, bukan alat diagnosis resmi. Semua teks UI, komentar kode, dan nama variabel/fungsi yang related ke output sistem wajib memakai istilah **"skrining"** / **"perkiraan risiko"**, BUKAN "diagnosis" — termasuk di commit message dan dokumentasi.

---

## Cara Kerja
- Sebelum menulis kode, sebutkan dulu file mana saja yang akan dibuat atau diubah.
- Kerjakan satu fitur per permintaan. Jangan mengerjakan lintas fitur sekaligus kecuali diminta secara eksplisit.
- Kalau instruksi yang diberikan ambigu terhadap struktur ini, tanyakan terlebih dahulu.
