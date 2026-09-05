-- =================================================================
-- MIGRATION: Drop health_facilities & facility_recommendations
-- Alasan: Data fasilitas kesehatan kini didapatkan secara dinamis & real-time
-- melalui OpenStreetMap (Overpass API) di backend, sehingga tabel database lokal
-- tidak lagi dibutuhkan dan telah dihapus dari arsitektur aplikasi.
-- =================================================================

-- 1. Hapus tabel rekomendasi faskes per sesi skrining (jika ada)
drop table if exists public.facility_recommendations cascade;

-- 2. Hapus tabel fasilitas kesehatan lokal (jika ada)
drop table if exists public.health_facilities cascade;
