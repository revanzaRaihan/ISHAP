-- ISHAP - Seed Data: Master Gejala, Penyakit, Bobot, Faskes, dan Dokter Mitra

-- 1. Insert Master Gejala ISPA
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

-- 2. Insert Master Penyakit ISPA
insert into public.diseases (id, name, severity_level, description) values
  ('b1000001-0000-0000-0000-000000000001', 'ISPA Ringan (Common Cold)', 'ringan', 'Infeksi virus saluran pernapasan bagian atas yang umumnya dapat pulih secara mandiri dengan istirahat dan hidrasi cukup.'),
  ('b1000001-0000-0000-0000-000000000002', 'Faringitis Akut (Radang Tenggorokan)', 'ringan', 'Peradangan pada mukosa faring yang menimbulkan nyeri tajam saat menelan, sering disebabkan virus atau infeksi bakteri Streptokokus.'),
  ('b1000001-0000-0000-0000-000000000003', 'Bronkitis Akut', 'sedang', 'Peradangan pada bronkus (saluran udara utama ke paru-paru) yang ditandai dengan batuk persisten berdahak dan rasa dada terbakar.'),
  ('b1000001-0000-0000-0000-000000000004', 'Pneumonia (ISPA Berat / Radang Paru)', 'berat', 'Kondisi kegawatdaruratan infeksi kantung udara paru-paru yang memerlukan evaluasi medis segera di fasilitas kesehatan.'),
  ('b1000001-0000-0000-0000-000000000005', 'Eksaserbasi Asma Akut', 'sedang', 'Penyempitan dan hiperaktivitas saluran napas akibat alergen atau infeksi virus yang memicu mengi dan napas sesak.')
on conflict (id) do nothing;

-- 3. Insert Pemetaan Gejala ke Penyakit & Bobot (Symptom-Disease Map)
-- Common Cold
insert into public.symptom_disease_map (symptom_id, disease_id, weight) values
  ('a1000001-0000-0000-0000-000000000001', 'b1000001-0000-0000-0000-000000000001', 2.0), -- batuk kering
  ('a1000001-0000-0000-0000-000000000003', 'b1000001-0000-0000-0000-000000000001', 3.0), -- pilek
  ('a1000001-0000-0000-0000-000000000004', 'b1000001-0000-0000-0000-000000000001', 2.0), -- nyeri tenggorokan
  ('a1000001-0000-0000-0000-000000000005', 'b1000001-0000-0000-0000-000000000001', 1.0)  -- demam ringan
on conflict (symptom_id, disease_id) do nothing;

-- Faringitis Akut
insert into public.symptom_disease_map (symptom_id, disease_id, weight) values
  ('a1000001-0000-0000-0000-000000000004', 'b1000001-0000-0000-0000-000000000002', 4.0), -- nyeri tenggorokan (utama)
  ('a1000001-0000-0000-0000-000000000005', 'b1000001-0000-0000-0000-000000000002', 2.0), -- demam ringan
  ('a1000001-0000-0000-0000-000000000001', 'b1000001-0000-0000-0000-000000000002', 1.5), -- batuk kering
  ('a1000001-0000-0000-0000-000000000010', 'b1000001-0000-0000-0000-000000000002', 1.0)  -- sakit kepala
on conflict (symptom_id, disease_id) do nothing;

-- Bronkitis Akut
insert into public.symptom_disease_map (symptom_id, disease_id, weight) values
  ('a1000001-0000-0000-0000-000000000002', 'b1000001-0000-0000-0000-000000000003', 4.0), -- batuk berdahak (utama)
  ('a1000001-0000-0000-0000-000000000005', 'b1000001-0000-0000-0000-000000000003', 2.0), -- demam ringan
  ('a1000001-0000-0000-0000-000000000008', 'b1000001-0000-0000-0000-000000000003', 2.0), -- dada perih/nyeri
  ('a1000001-0000-0000-0000-000000000011', 'b1000001-0000-0000-0000-000000000003', 1.5)  -- lemas
on conflict (symptom_id, disease_id) do nothing;

-- Pneumonia (ISPA Berat)
insert into public.symptom_disease_map (symptom_id, disease_id, weight) values
  ('a1000001-0000-0000-0000-000000000006', 'b1000001-0000-0000-0000-000000000004', 3.5), -- demam tinggi
  ('a1000001-0000-0000-0000-000000000007', 'b1000001-0000-0000-0000-000000000004', 4.0), -- sesak napas (kunci)
  ('a1000001-0000-0000-0000-000000000008', 'b1000001-0000-0000-0000-000000000004', 3.0), -- nyeri dada saat bernapas
  ('a1000001-0000-0000-0000-000000000002', 'b1000001-0000-0000-0000-000000000004', 2.5), -- batuk berdahak kental
  ('a1000001-0000-0000-0000-000000000011', 'b1000001-0000-0000-0000-000000000004', 2.0)  -- kelelahan berat
on conflict (symptom_id, disease_id) do nothing;

-- Eksaserbasi Asma Akut
insert into public.symptom_disease_map (symptom_id, disease_id, weight) values
  ('a1000001-0000-0000-0000-000000000009', 'b1000001-0000-0000-0000-000000000005', 4.5), -- mengi / wheezing (kunci)
  ('a1000001-0000-0000-0000-000000000007', 'b1000001-0000-0000-0000-000000000005', 3.5), -- sesak napas
  ('a1000001-0000-0000-0000-000000000001', 'b1000001-0000-0000-0000-000000000005', 1.5)  -- batuk kering
on conflict (symptom_id, disease_id) do nothing;

-- 4. Insert Profil Dokter Mitra Konsultasi Online
insert into public.online_doctor_profiles (id, name, platform, profile_url, specialty) values
  ('d1000001-0000-0000-0000-000000000001', 'dr. Sarah Nurbaiti, Sp.P', 'Halodoc', 'https://www.halodoc.com/tanya-dokter', 'Spesialis Paru & Pernapasan'),
  ('d1000001-0000-0000-0000-000000000002', 'dr. Budi Setiawan', 'Alodokter', 'https://www.alodokter.com/tanya-dokter', 'Dokter Umum - Skrining ISPA'),
  ('d1000001-0000-0000-0000-000000000003', 'dr. Amanda Putri, Sp.A', 'Halodoc', 'https://www.halodoc.com/tanya-dokter', 'Spesialis Anak (Pediatri ISPA)')
on conflict (id) do nothing;
