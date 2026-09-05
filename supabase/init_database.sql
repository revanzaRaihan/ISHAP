-- ====================================================================
-- ISHAP - SCRIPT LENGKAP INISIALISASI DATABASE & SEED DATA
-- Jalankan skrip ini langsung di Supabase SQL Editor
-- Dashboard URL: https://supabase.com/dashboard/project/jcdjtxcynbyksxnzglqt/sql
-- ====================================================================

-- 1. TABEL UTAMA & STRUKTUR DDL
create table if not exists public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  name text,
  phone text,
  created_at timestamptz default now()
);

create table if not exists public.symptoms (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  category text,
  description text,
  created_at timestamptz default now()
);

create table if not exists public.diseases (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  severity_level text,
  description text,
  created_at timestamptz default now()
);

create table if not exists public.symptom_disease_map (
  id uuid primary key default gen_random_uuid(),
  symptom_id uuid references public.symptoms(id) on delete cascade,
  disease_id uuid references public.diseases(id) on delete cascade,
  weight numeric not null,
  unique (symptom_id, disease_id)
);

create table if not exists public.screening_sessions (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references public.profiles(id) on delete set null,
  status text default 'in_progress',
  created_at timestamptz default now()
);

create table if not exists public.session_symptoms (
  id uuid primary key default gen_random_uuid(),
  session_id uuid references public.screening_sessions(id) on delete cascade,
  symptom_id uuid references public.symptoms(id) on delete restrict
);

create table if not exists public.screening_results (
  id uuid primary key default gen_random_uuid(),
  session_id uuid references public.screening_sessions(id) on delete cascade,
  disease_id uuid references public.diseases(id) on delete restrict,
  confidence_score numeric not null,
  reasoning text,
  created_at timestamptz default now()
);

create table if not exists public.online_doctor_profiles (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  platform text,
  profile_url text,
  specialty text
);

create table if not exists public.consultation_referrals (
  id uuid primary key default gen_random_uuid(),
  session_id uuid references public.screening_sessions(id) on delete cascade,
  doctor_profile_id uuid references public.online_doctor_profiles(id) on delete cascade
);

-- 2. ROW LEVEL SECURITY (RLS) POLICIES
alter table public.profiles enable row level security;
alter table public.screening_sessions enable row level security;
alter table public.session_symptoms enable row level security;
alter table public.screening_results enable row level security;
alter table public.consultation_referrals enable row level security;
alter table public.symptoms enable row level security;
alter table public.diseases enable row level security;
alter table public.symptom_disease_map enable row level security;
alter table public.online_doctor_profiles enable row level security;

-- Reference tables: Public Read
drop policy if exists "Public can read symptoms" on public.symptoms;
create policy "Public can read symptoms" on public.symptoms for select using (true);

drop policy if exists "Public can read diseases" on public.diseases;
create policy "Public can read diseases" on public.diseases for select using (true);

drop policy if exists "Public can read symptom_disease_map" on public.symptom_disease_map;
create policy "Public can read symptom_disease_map" on public.symptom_disease_map for select using (true);

drop policy if exists "Public can read online_doctor_profiles" on public.online_doctor_profiles;
create policy "Public can read online_doctor_profiles" on public.online_doctor_profiles for select using (true);

-- User Profiles
drop policy if exists "Users can view own profile" on public.profiles;
create policy "Users can view own profile" on public.profiles for select using (auth.uid() = id);

drop policy if exists "Users can update own profile" on public.profiles;
create policy "Users can update own profile" on public.profiles for update using (auth.uid() = id);

drop policy if exists "Users can insert own profile" on public.profiles;
create policy "Users can insert own profile" on public.profiles for insert with check (auth.uid() = id);

-- Screening Sessions & Results
drop policy if exists "Users can view own sessions" on public.screening_sessions;
create policy "Users can view own sessions" on public.screening_sessions for select
  using (auth.uid() = user_id or user_id is null);

drop policy if exists "Users can create sessions" on public.screening_sessions;
create policy "Users can create sessions" on public.screening_sessions for insert
  with check (auth.uid() = user_id or user_id is null);

drop policy if exists "Users can update own sessions" on public.screening_sessions;
create policy "Users can update own sessions" on public.screening_sessions for update
  using (auth.uid() = user_id or user_id is null);

drop policy if exists "Users can view session symptoms" on public.session_symptoms;
create policy "Users can view session symptoms" on public.session_symptoms for select
  using (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

drop policy if exists "Users can insert session symptoms" on public.session_symptoms;
create policy "Users can insert session symptoms" on public.session_symptoms for insert
  with check (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

drop policy if exists "Users can view screening results" on public.screening_results;
create policy "Users can view screening results" on public.screening_results for select
  using (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

drop policy if exists "Users can insert screening results" on public.screening_results;
create policy "Users can insert screening results" on public.screening_results for insert
  with check (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

drop policy if exists "Users can view consultation referrals" on public.consultation_referrals;
create policy "Users can view consultation referrals" on public.consultation_referrals for select
  using (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

drop policy if exists "Users can insert consultation referrals" on public.consultation_referrals;
create policy "Users can insert consultation referrals" on public.consultation_referrals for insert
  with check (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

-- 3. SEED DATA KLINIS ISPA
insert into public.symptoms (id, name, category, description) values
  ('a1000001-0000-0000-0000-000000000001', 'Batuk Kering', 'Saluran Napas Atas', 'Batuk tanpa lendir/dahak, sering memicu rasa gatal di tenggorokan'),
  ('a1000001-0000-0000-0000-000000000002', 'Batuk Berdahak', 'Saluran Napas Bawah', 'Batuk disertai lendir atau dahak kental putih, kuning, atau kehijauan'),
  ('a1000001-0000-0000-0000-000000000003', 'Hidung Tersumbat / Pilek', 'Saluran Napas Atas', 'Keluarnya sekret hidung cair atau hidung tersumbat yang menyulitkan bernapas'),
  ('a1000001-0000-0000-0000-000000000004', 'Nyeri Tenggorokan', 'Saluran Napas Atas', 'Rasa perih, panas, atau sakit saat menelan makanan dan minuman'),
  ('a1000001-0000-0000-0000-000000000005', 'Demam Ringan (37.5°C - 38.4°C)', 'Sistemik', 'Peningkatan suhu tubuh ringan disertai rasa hangat atau kedinginan sesekali'),
  ('a1000001-0000-0000-0000-000000000006', 'Demam Tinggi (>= 38.5°C)', 'Sistemik', 'Suhu tubuh meningkat tinggi, tubuh menggigil, dan berkeringat dingin'),
  ('a1000001-0000-0000-0000-000000000007', 'Sesak Napas / Napas Cepat', 'Saluran Napas Bawah', 'Tanda bahaya: rasa berat saat menarik napas atau frekuensi napas meningkat'),
  ('a1000001-0000-0000-0000-000000000008', 'Nyeri Dada saat Bernapas', 'Saluran Napas Bawah', 'Rasa tertusuk atau nyeri pada dinding dada saat batuk atau bernapas dalam'),
  ('a1000001-0000-0000-0000-000000000009', 'Napas Berbunyi (Mengi / Wheezing)', 'Saluran Napas Bawah', 'Terdengar suara bernada tinggi seperti siulan saat mengembuskan napas'),
  ('a1000001-0000-0000-0000-000000000010', 'Sakit Kepala & Pegal Seluruh Tubuh', 'Sistemik', 'Nyeri otot (myalgia) menyeluruh dan rasa tegang di area kepala'),
  ('a1000001-0000-0000-0000-000000000011', 'Kelelahan Ekstrem / Badan Lemas', 'Sistemik', 'Kehilangan energi secara signifikan yang mengganggu aktivitas normal')
on conflict (id) do nothing;

insert into public.diseases (id, name, severity_level, description) values
  ('b1000001-0000-0000-0000-000000000001', 'ISPA Ringan (Common Cold)', 'ringan', 'Infeksi virus saluran pernapasan bagian atas yang umumnya dapat pulih secara mandiri dengan istirahat dan hidrasi cukup.'),
  ('b1000001-0000-0000-0000-000000000002', 'Faringitis Akut (Radang Tenggorokan)', 'ringan', 'Peradangan pada mukosa faring yang menimbulkan nyeri tajam saat menelan, sering disebabkan virus atau infeksi bakteri Streptokokus.'),
  ('b1000001-0000-0000-0000-000000000003', 'Bronkitis Akut', 'sedang', 'Peradangan pada bronkus (saluran udara utama ke paru-paru) yang ditandai dengan batuk persisten berdahak dan rasa dada terbakar.'),
  ('b1000001-0000-0000-0000-000000000004', 'Pneumonia (ISPA Berat / Radang Paru)', 'berat', 'Kondisi kegawatdaruratan infeksi kantung udara paru-paru yang memerlukan evaluasi medis segera di fasilitas kesehatan.'),
  ('b1000001-0000-0000-0000-000000000005', 'Eksaserbasi Asma Akut', 'sedang', 'Penyempitan dan hiperaktivitas saluran napas akibat alergen atau infeksi virus yang memicu mengi dan napas sesak.')
on conflict (id) do nothing;

insert into public.symptom_disease_map (symptom_id, disease_id, weight) values
  ('a1000001-0000-0000-0000-000000000001', 'b1000001-0000-0000-0000-000000000001', 2.0),
  ('a1000001-0000-0000-0000-000000000003', 'b1000001-0000-0000-0000-000000000001', 3.0),
  ('a1000001-0000-0000-0000-000000000004', 'b1000001-0000-0000-0000-000000000001', 2.0),
  ('a1000001-0000-0000-0000-000000000005', 'b1000001-0000-0000-0000-000000000001', 1.0),
  ('a1000001-0000-0000-0000-000000000004', 'b1000001-0000-0000-0000-000000000002', 4.0),
  ('a1000001-0000-0000-0000-000000000005', 'b1000001-0000-0000-0000-000000000002', 2.0),
  ('a1000001-0000-0000-0000-000000000001', 'b1000001-0000-0000-0000-000000000002', 1.5),
  ('a1000001-0000-0000-0000-000000000010', 'b1000001-0000-0000-0000-000000000002', 1.0),
  ('a1000001-0000-0000-0000-000000000002', 'b1000001-0000-0000-0000-000000000003', 4.0),
  ('a1000001-0000-0000-0000-000000000005', 'b1000001-0000-0000-0000-000000000003', 2.0),
  ('a1000001-0000-0000-0000-000000000008', 'b1000001-0000-0000-0000-000000000003', 2.0),
  ('a1000001-0000-0000-0000-000000000011', 'b1000001-0000-0000-0000-000000000003', 1.5),
  ('a1000001-0000-0000-0000-000000000006', 'b1000001-0000-0000-0000-000000000004', 3.5),
  ('a1000001-0000-0000-0000-000000000007', 'b1000001-0000-0000-0000-000000000004', 4.0),
  ('a1000001-0000-0000-0000-000000000008', 'b1000001-0000-0000-0000-000000000004', 3.0),
  ('a1000001-0000-0000-0000-000000000002', 'b1000001-0000-0000-0000-000000000004', 2.5),
  ('a1000001-0000-0000-0000-000000000011', 'b1000001-0000-0000-0000-000000000004', 2.0),
  ('a1000001-0000-0000-0000-000000000009', 'b1000001-0000-0000-0000-000000000005', 4.5),
  ('a1000001-0000-0000-0000-000000000007', 'b1000001-0000-0000-0000-000000000005', 3.5),
  ('a1000001-0000-0000-0000-000000000001', 'b1000001-0000-0000-0000-000000000005', 1.5)
on conflict (symptom_id, disease_id) do nothing;

insert into public.online_doctor_profiles (id, name, platform, profile_url, specialty) values
  ('d1000001-0000-0000-0000-000000000001', 'dr. Sarah Nurbaiti, Sp.P', 'Halodoc', 'https://www.halodoc.com/tanya-dokter', 'Spesialis Paru & Pernapasan'),
  ('d1000001-0000-0000-0000-000000000002', 'dr. Budi Setiawan', 'Alodokter', 'https://www.alodokter.com/tanya-dokter', 'Dokter Umum - Skrining ISPA'),
  ('d1000001-0000-0000-0000-000000000003', 'dr. Amanda Putri, Sp.A', 'Halodoc', 'https://www.halodoc.com/tanya-dokter', 'Spesialis Anak (Pediatri ISPA)')
on conflict (id) do nothing;
