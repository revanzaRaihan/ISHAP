-- ==========================================================
-- ISHAP — Reset & Seed Data untuk Supabase SQL Editor
-- ==========================================================
-- Script ini siap di-copy & paste langsung ke Supabase SQL Editor.
-- Menggunakan 'overriding system value' & 'setval' agar aman dari
-- constraint identity PostgreSQL.

-- 1. Kosongkan tabel & reset sequence
truncate table
  session_symptoms,
  screening_results,
  symptom_disease_map,
  online_doctor_profiles,
  symptoms,
  diseases
restart identity cascade;

-- 2. Seed: Symptoms (Master Gejala ISPA)
insert into symptoms (id, name, category, description) overriding system value values
(1,  'Batuk Kering', 'Saluran Napas Atas', 'Batuk tanpa lendir/dahak, sering memicu rasa gatal di tenggorokan'),
(2,  'Batuk Berdahak', 'Saluran Napas Bawah', 'Batuk disertai lendir atau dahak kental putih, kuning, atau kehijauan'),
(3,  'Hidung Tersumbat / Pilek', 'Saluran Napas Atas', 'Keluarnya sekret hidung cair atau hidung tersumbat yang menyulitkan bernapas'),
(4,  'Nyeri Tenggorokan', 'Saluran Napas Atas', 'Rasa perih, panas, atau sakit saat menelan makanan dan minuman'),
(5,  'Demam Ringan (37.5°C - 38.4°C)', 'Sistemik', 'Peningkatan suhu tubuh ringan disertai rasa hangat atau kedinginan sesekali'),
(6,  'Demam Tinggi (>= 38.5°C)', 'Sistemik', 'Suhu tubuh meningkat tinggi, tubuh menggigil, dan berkeringat dingin'),
(7,  'Sesak Napas / Napas Cepat', 'Saluran Napas Bawah', 'Tanda bahaya: rasa berat saat menarik napas atau frekuensi napas meningkat'),
(8,  'Nyeri Dada saat Bernapas', 'Saluran Napas Bawah', 'Rasa tertusuk atau nyeri pada dinding dada saat batuk atau bernapas dalam'),
(9,  'Napas Berbunyi (Mengi / Wheezing)', 'Saluran Napas Bawah', 'Terdengar suara bernada tinggi seperti siulan saat mengembuskan napas'),
(10, 'Sakit Kepala & Pegal Seluruh Tubuh', 'Sistemik', 'Nyeri otot (myalgia) menyeluruh dan rasa tegang di area kepala'),
(11, 'Kelelahan Ekstrem / Badan Lemas', 'Sistemik', 'Kehilangan energi secara signifikan yang mengganggu aktivitas normal');

-- 3. Seed: Diseases (Master Penyakit ISPA, Etiologi & Edukasi)
insert into diseases (
  id, name, severity_level, description,
  pathogenesis_overview, pathogenesis_causes, pathogenesis_risk_factors,
  recovery_tips, red_flags
) overriding system value values
(
  1, 'ISPA Ringan (Common Cold)', 'ringan',
  'Infeksi virus saluran pernapasan bagian atas yang umumnya dapat pulih secara mandiri dengan istirahat dan hidrasi cukup.',
  'ISPA Ringan (Common Cold) umumnya disebabkan oleh infeksi virus saluran napas atas (Rhinovirus, Coronavirus musiman, atau Adenovirus) yang menginfeksi lapisan mukosa hidung dan tenggorokan.',
  '["Inhalasi Droplet: Menghirup percikan udara mikro saat orang di sekitar yang sedang flu batuk, bersin, atau berbicara.", "Kontak Permukaan Tangan: Menyentuh gagang pintu, layar ponsel, atau meja publik terkontaminasi, lalu menyentuh hidung/mata."]'::jsonb,
  '["Penurunan Daya Tahan Tubuh: Kurang tidur, kelelahan fisik berat, atau stres berkepanjangan.", "Perubahan Cuaca Ekstrem / Pancaroba: Udara dingin dan kering mengeringkan lapisan mukosa pelindung alami hidung."]'::jsonb,
  '["Istirahat total 7-8 jam per hari untuk memulihkan sistem pertahanan sel darah putih.", "Konsumsi air hangat minimal 2.5 liter per hari untuk mengencerkan lendir hidung.", "Kumur air garam hangat 2-3 kali sehari untuk meredakan iritasi saluran cerna atas.", "Konsumsi vitamin C dan makanan bergizi kaya antioksidan."]'::jsonb,
  '["Demam tinggi menetap di atas 38.5°C lebih dari 3 hari berturut-turut.", "Mulai timbul sesak napas atau dada terasa terhimpit berat.", "Tidak dapat menelan cairan sama sekali atau timbul dehidrasi."]'::jsonb
),
(
  2, 'Faringitis Akut (Radang Tenggorokan)', 'ringan',
  'Peradangan pada mukosa faring yang menimbulkan nyeri tajam saat menelan, sering disebabkan virus atau infeksi bakteri Streptokokus.',
  'Faringitis Akut adalah inflamasi pada dinding belakang tenggorokan (faring) akibat kolonisasi mikroba atau iritasi lingkungan yang memicu vasodilatasi pembuluh darah lokal.',
  '["Paparan Patogen Saluran Napas: Penularan langsung droplet pernapasan atau cairan liur dari orang yang sedang terinfeksi.", "Iritasi Kimiawi & Lingkungan: Polusi asap knalpot, debu jalanan, atau asap rokok yang mengikis lapisan pelindung dinding faring."]'::jsonb,
  '["Bernapas Melalui Mulut: Terjadi saat hidung tersumbat, membuat tenggorokan cepat kering dan rentan meradang.", "Makanan & Minuman Iritatif: Gorengan berminyak berulang atau makanan pedas menyengat saat daya tahan tubuh menurun."]'::jsonb,
  '["Konsumsi minuman hangat (seperti air madu lemon atau teh chamomile).", "Hindari makanan bertekstur keras, berminyak, atau bersuhu terlalu dingin/panas.", "Gunakan humidifier atau uap hangat jika udara kamar kering."]'::jsonb,
  '["Nyeri menelan sangat hebat hingga tidak dapat menelan air liur sendiri (drooling).", "Leher membengkak atau tampak pembesaran kelenjar getah bening yang sangat nyeri.", "Suara serak hilang total disertai kesulitan menarik napas."]'::jsonb
),
(
  3, 'Bronkitis Akut', 'sedang',
  'Peradangan pada bronkus (saluran udara utama ke paru-paru) yang ditandai dengan batuk persisten berdahak dan rasa dada terbakar.',
  'Bronkitis Akut terjadi saat respon inflamasi menjalar dari saluran napas atas menuju percabangan bronkus, memicu hipersekresi lendir dan batuk produktif.',
  '["Perluasan Infeksi Saluran Napas: Virus influenza atau RSV turun menginfeksi epitel bersilia bronkus.", "Reaksi Inflamasi Mukosa: Pembengkakan dinding saluran napas memicu refleks batuk terus menerus."]'::jsonb,
  '["Membiarkan Flu Tanpa Istirahat: Melanjutkan aktivitas fisik berat saat terkena batuk pilek.", "Paparan Asap Rokok & Debu: Perokok aktif atau pasif memiliki silia pembersih paru yang kurang optimal."]'::jsonb,
  '["Hindari paparan asap rokok, vape, debu, atau aerosol kimia sama sekali.", "Gunakan inhalasi uap air hangat untuk membantu melonggarkan dahak di saluran dada.", "Perbanyak minum air hangat dan konsumsi sup kaldu bernutrisi."]'::jsonb,
  '["Batuk berdahak disertai bercak darah segar.", "Sesak napas bertambah berat saat berbaring telentang.", "Demam tinggi menggigil yang tidak turun dengan pereda demam biasa."]'::jsonb
),
(
  4, 'Pneumonia (ISPA Berat / Radang Paru)', 'berat',
  'Kondisi kegawatdaruratan infeksi kantung udara paru-paru yang memerlukan evaluasi medis segera di fasilitas kesehatan.',
  'Pneumonia adalah infeksi akut pada unit pertukaran gas paru-paru (alveoli) yang terisi cairan eksudat dan nanah, menghambat pasokan oksigen ke peredaran darah.',
  '["Infeksi Mikroorganisme Patogen: Bakteri agresif (seperti Streptococcus pneumoniae) atau virus pernapasan yang mencapai parenkim paru.", "Infeksi Sekunder: Komplikasi berat dari flu yang tidak teratasi pada individu rentan."]'::jsonb,
  '["Sistem Imun Sangat Lemah: Usia lanjut (>65 tahun), balita, penderita diabetes, atau asma kronis.", "Kerusakan Mekanisme Pembersih Paru: Kerusakan silia akibat polusi pekat berkepanjangan."]'::jsonb,
  '["SEGERA kunjungi fasilitas kesehatan (Puskesmas / IGD RS) terdekat untuk pemeriksaan saturasi oksigen dan rontgen toraks.", "Jangan menunda penanganan medis dengan hanya mengandalkan obat bebas di rumah.", "Posisikan tubuh setengah duduk untuk memudahkan ventilasi udara paru."]'::jsonb,
  '["Napas sangat cepat (>24 kali per menit pada dewasa) atau tarikan dinding dada ke dalam.", "Bibir, ujung kuku, atau lidah tampak kebiruan (sianosis).", "Penurunan kesadaran, linglung, atau rasa kantuk yang tidak wajar."]'::jsonb
),
(
  5, 'Eksaserbasi Asma Akut', 'sedang',
  'Penyempitan dan hiperaktivitas saluran napas akibat alergen atau infeksi virus yang memicu mengi dan napas sesak.',
  'Kondisi bronkospasme akut di mana otot polos bronkus berkontraksi kuat, disertai edema mukosa dan sumbatan lendir kental yang menyempitkan jalan napas.',
  '["Reaksi Hipersensitivitas Bronkus: Pemicu lingkungan (debu, serbuk bunga, bulu hewan peliharaan, udara dingin).", "Pemicu Infeksi Virus: Infeksi flu ringan memicu lonjakan reaktivitas saluran napas pada penderita bakat asma."]'::jsonb,
  '["Riwayat Alergi (Atopi): Riwayat keluarga dengan asma, dermatitis atopik, atau rinitis alergi.", "Stres Fisik & Polusi Udara: Berada di dekat jalan raya berpolusi atau aktivitas fisik berlebih tanpa obat pengendali."]'::jsonb,
  '["Gunakan inhaler pereda (reliever) sesuai instruksi dokter spesialis paru Anda.", "Duduk tegak dengan tenang, jangan panik, dan longgarkan pakaian yang ketat di leher dan dada.", "Jauhi ruangan berdebu atau ber-AC dingin ekstrem secara langsung."]'::jsonb,
  '["Inhaler pereda tidak memberikan perbaikan setelah 2 kali penggunaan.", "Kesulitan berbicara dalam satu kalimat utuh akibat sesak napas hebat.", "Cuping hidung kembang kempis dan tampak kelelahan bernapas."]'::jsonb
);

-- 4. Seed: Symptom-Disease Map (Bobot Relasi Klinis)
insert into symptom_disease_map (symptom_id, disease_id, weight) values
-- Common Cold (disease 1)
(1, 1, 2.0), (3, 1, 3.0), (4, 1, 2.0), (5, 1, 1.0),
-- Faringitis Akut (disease 2)
(4, 2, 4.0), (5, 2, 2.0), (1, 2, 1.5), (10, 2, 1.0),
-- Bronkitis Akut (disease 3)
(2, 3, 4.0), (5, 3, 2.0), (8, 3, 2.0), (11, 3, 1.5),
-- Pneumonia (disease 4)
(6, 4, 3.5), (7, 4, 4.0), (8, 4, 3.0), (2, 4, 2.5), (11, 4, 2.0),
-- Eksaserbasi Asma (disease 5)
(9, 5, 4.5), (7, 5, 3.5), (1, 5, 1.5);

-- 5. Seed: Mitra Konsultasi Online
insert into online_doctor_profiles (id, name, platform, profile_url, specialty) overriding system value values
(1, 'Dokter Spesialis Paru & Pernapasan', 'Halodoc', 'https://www.halodoc.com/tanya-dokter', 'Pulmonologi'),
(2, 'Dokter Umum - Skrining ISPA', 'Alodokter', 'https://www.alodokter.com/tanya-dokter', 'Dokter Umum'),
(3, 'Dokter Spesialis Anak (ISPA pada Anak)', 'Halodoc', 'https://www.halodoc.com/tanya-dokter', 'Pediatri');

-- 6. Selaraskan Auto-Increment Sequence ke ID Terakhir
select setval(pg_get_serial_sequence('symptoms', 'id'), coalesce(max(id), 1)) from symptoms;
select setval(pg_get_serial_sequence('diseases', 'id'), coalesce(max(id), 1)) from diseases;
select setval(pg_get_serial_sequence('online_doctor_profiles', 'id'), coalesce(max(id), 1)) from online_doctor_profiles;
