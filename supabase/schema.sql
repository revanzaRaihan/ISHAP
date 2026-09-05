-- ISHAP - Supabase Schema Definition
-- Aplikasi Skrining Mandiri ISPA

-- 1. Profil Pengguna (extend auth.users)
create table if not exists public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  name text,
  phone text,
  created_at timestamptz default now()
);

-- 2. Master Gejala ISPA
create table if not exists public.symptoms (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  category text,
  description text,
  created_at timestamptz default now()
);

-- 3. Master Penyakit ISPA
create table if not exists public.diseases (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  severity_level text,
  description text,
  created_at timestamptz default now()
);

-- 4. Pemetaan Gejala ke Penyakit & Bobot
create table if not exists public.symptom_disease_map (
  id uuid primary key default gen_random_uuid(),
  symptom_id uuid references public.symptoms(id) on delete cascade,
  disease_id uuid references public.diseases(id) on delete cascade,
  weight numeric not null,
  unique (symptom_id, disease_id)
);

-- 5. Sesi Skrining
create table if not exists public.screening_sessions (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references public.profiles(id) on delete set null,
  status text default 'in_progress',
  created_at timestamptz default now()
);

-- 6. Gejala Terpilih per Sesi
create table if not exists public.session_symptoms (
  id uuid primary key default gen_random_uuid(),
  session_id uuid references public.screening_sessions(id) on delete cascade,
  symptom_id uuid references public.symptoms(id) on delete restrict
);

-- 7. Hasil Skrining Mandiri (Perkiraan Risiko)
create table if not exists public.screening_results (
  id uuid primary key default gen_random_uuid(),
  session_id uuid references public.screening_sessions(id) on delete cascade,
  disease_id uuid references public.diseases(id) on delete restrict,
  confidence_score numeric not null,
  reasoning text,
  created_at timestamptz default now()
);

-- 8. Profil Dokter Konsultasi Online
create table if not exists public.online_doctor_profiles (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  platform text,                          -- 'halodoc', dsb
  profile_url text,
  specialty text
);

-- 9. Rujukan Konsultasi per Sesi Skrining
create table if not exists public.consultation_referrals (
  id uuid primary key default gen_random_uuid(),
  session_id uuid references public.screening_sessions(id) on delete cascade,
  doctor_profile_id uuid references public.online_doctor_profiles(id) on delete cascade
);

-- ==========================================
-- ROW LEVEL SECURITY (RLS) POLICIES
-- ==========================================

-- Enable RLS
alter table public.profiles enable row level security;
alter table public.screening_sessions enable row level security;
alter table public.session_symptoms enable row level security;
alter table public.screening_results enable row level security;
alter table public.consultation_referrals enable row level security;
alter table public.symptoms enable row level security;
alter table public.diseases enable row level security;
alter table public.symptom_disease_map enable row level security;
alter table public.online_doctor_profiles enable row level security;

-- 1. Reference tables: Public SELECT, modify restricted to service role
create policy "Public can read symptoms" on public.symptoms for select using (true);
create policy "Public can read diseases" on public.diseases for select using (true);
create policy "Public can read symptom_disease_map" on public.symptom_disease_map for select using (true);
create policy "Public can read online_doctor_profiles" on public.online_doctor_profiles for select using (true);

-- 2. Profiles: User can manage their own profile
create policy "Users can view own profile" on public.profiles for select using (auth.uid() = id);
create policy "Users can update own profile" on public.profiles for update using (auth.uid() = id);
create policy "Users can insert own profile" on public.profiles for insert with check (auth.uid() = id);

-- 3. Screening Sessions & Related:
-- Allows authenticated users to see their own sessions or anonymous sessions created in the current context
create policy "Users can view own sessions" on public.screening_sessions for select
  using (auth.uid() = user_id or user_id is null);

create policy "Users can create sessions" on public.screening_sessions for insert
  with check (auth.uid() = user_id or user_id is null);

create policy "Users can update own sessions" on public.screening_sessions for update
  using (auth.uid() = user_id or user_id is null);

-- Session Symptoms
create policy "Users can view session symptoms" on public.session_symptoms for select
  using (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

create policy "Users can insert session symptoms" on public.session_symptoms for insert
  with check (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

-- Screening Results
create policy "Users can view screening results" on public.screening_results for select
  using (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

create policy "Users can insert screening results" on public.screening_results for insert
  with check (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

-- Consultation Referrals
create policy "Users can view consultation referrals" on public.consultation_referrals for select
  using (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));

create policy "Users can insert consultation referrals" on public.consultation_referrals for insert
  with check (exists (select 1 from public.screening_sessions s where s.id = session_id and (s.user_id = auth.uid() or s.user_id is null)));
