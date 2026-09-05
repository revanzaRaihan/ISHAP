<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\OnlineDoctorProfile;
use App\Models\Symptom;
use App\Models\SymptomDiseaseMap;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Master Gejala ISPA (Integer ID selaras dengan Supabase)
        $symptoms = [
            ['id' => 1,  'name' => 'Batuk Kering', 'category' => 'Saluran Napas Atas', 'description' => 'Batuk tanpa lendir/dahak, sering memicu rasa gatal di tenggorokan'],
            ['id' => 2,  'name' => 'Batuk Berdahak', 'category' => 'Saluran Napas Bawah', 'description' => 'Batuk disertai lendir atau dahak kental putih, kuning, atau kehijauan'],
            ['id' => 3,  'name' => 'Hidung Tersumbat / Pilek', 'category' => 'Saluran Napas Atas', 'description' => 'Keluarnya sekret hidung cair atau hidung tersumbat yang menyulitkan bernapas'],
            ['id' => 4,  'name' => 'Nyeri Tenggorokan', 'category' => 'Saluran Napas Atas', 'description' => 'Rasa perih, panas, atau sakit saat menelan makanan dan minuman'],
            ['id' => 5,  'name' => 'Demam Ringan (37.5°C - 38.4°C)', 'category' => 'Sistemik', 'description' => 'Peningkatan suhu tubuh ringan disertai rasa hangat atau kedinginan sesekali'],
            ['id' => 6,  'name' => 'Demam Tinggi (>= 38.5°C)', 'category' => 'Sistemik', 'description' => 'Suhu tubuh meningkat tinggi, tubuh menggigil, dan berkeringat dingin'],
            ['id' => 7,  'name' => 'Sesak Napas / Napas Cepat', 'category' => 'Saluran Napas Bawah', 'description' => 'Tanda bahaya: rasa berat saat menarik napas atau frekuensi napas meningkat'],
            ['id' => 8,  'name' => 'Nyeri Dada saat Bernapas', 'category' => 'Saluran Napas Bawah', 'description' => 'Rasa tertusuk atau nyeri pada dinding dada saat batuk atau bernapas dalam'],
            ['id' => 9,  'name' => 'Napas Berbunyi (Mengi / Wheezing)', 'category' => 'Saluran Napas Bawah', 'description' => 'Terdengar suara bernada tinggi seperti siulan saat mengembuskan napas'],
            ['id' => 10, 'name' => 'Sakit Kepala & Pegal Seluruh Tubuh', 'category' => 'Sistemik', 'description' => 'Nyeri otot (myalgia) menyeluruh dan rasa tegang di area kepala'],
            ['id' => 11, 'name' => 'Kelelahan Ekstrem / Badan Lemas', 'category' => 'Sistemik', 'description' => 'Kehilangan energi secara signifikan yang mengganggu aktivitas normal'],
        ];

        foreach ($symptoms as $symptom) {
            Symptom::updateOrCreate(['id' => $symptom['id']], $symptom);
        }

        // 2. Master Penyakit ISPA beserta Etiologi / Patogenesis & Edukasi
        $diseases = [
            [
                'id' => 1,
                'name' => 'ISPA Ringan (Common Cold)',
                'severity_level' => 'ringan',
                'description' => 'Infeksi virus saluran pernapasan bagian atas yang umumnya dapat pulih secara mandiri dengan istirahat dan hidrasi cukup.',
                'pathogenesis_overview' => 'ISPA Ringan (Common Cold) umumnya disebabkan oleh infeksi virus saluran napas atas (Rhinovirus, Coronavirus musiman, atau Adenovirus) yang menginfeksi lapisan mukosa hidung dan tenggorokan.',
                'pathogenesis_causes' => [
                    'Inhalasi Droplet: Menghirup percikan udara mikro saat orang di sekitar yang sedang flu batuk, bersin, atau berbicara.',
                    'Kontak Permukaan Tangan: Menyentuh gagang pintu, layar ponsel, atau meja publik terkontaminasi, lalu menyentuh hidung/mata.',
                ],
                'pathogenesis_risk_factors' => [
                    'Penurunan Daya Tahan Tubuh: Kurang tidur, kelelahan fisik berat, atau stres berkepanjangan.',
                    'Perubahan Cuaca Ekstrem / Pancaroba: Udara dingin dan kering mengeringkan lapisan mukosa pelindung alami hidung.',
                ],
                'recovery_tips' => [
                    'Istirahat total 7-8 jam per hari untuk memulihkan sistem pertahanan sel darah putih.',
                    'Konsumsi air hangat minimal 2.5 liter per hari untuk mengencerkan lendir hidung.',
                    'Kumur air garam hangat 2-3 kali sehari untuk meredakan iritasi saluran cerna atas.',
                    'Konsumsi vitamin C dan makanan bergizi kaya antioksidan.',
                ],
                'red_flags' => [
                    'Demam tinggi menetap di atas 38.5°C lebih dari 3 hari berturut-turut.',
                    'Mulai timbul sesak napas atau dada terasa terhimpit berat.',
                    'Tidak dapat menelan cairan sama sekali atau timbul dehidrasi.',
                ],
            ],
            [
                'id' => 2,
                'name' => 'Faringitis Akut (Radang Tenggorokan)',
                'severity_level' => 'ringan',
                'description' => 'Peradangan pada mukosa faring yang menimbulkan nyeri tajam saat menelan, sering disebabkan virus atau infeksi bakteri Streptokokus.',
                'pathogenesis_overview' => 'Faringitis Akut adalah inflamasi pada dinding belakang tenggorokan (faring) akibat kolonisasi mikroba atau iritasi lingkungan yang memicu vasodilatasi pembuluh darah lokal.',
                'pathogenesis_causes' => [
                    'Paparan Patogen Saluran Napas: Penularan langsung droplet pernapasan atau cairan liur dari orang yang sedang terinfeksi.',
                    'Iritasi Kimiawi & Lingkungan: Polusi asap knalpot, debu jalanan, atau asap rokok yang mengikis lapisan pelindung dinding faring.',
                ],
                'pathogenesis_risk_factors' => [
                    'Bernapas Melalui Mulut: Terjadi saat hidung tersumbat, membuat tenggorokan cepat kering dan rentan meradang.',
                    'Makanan & Minuman Iritatif: Gorengan berminyak berulang atau makanan pedas menyengat saat daya tahan tubuh menurun.',
                ],
                'recovery_tips' => [
                    'Konsumsi minuman hangat (seperti air madu lemon atau teh chamomile).',
                    'Hindari makanan bertekstur keras, berminyak, atau bersuhu terlalu dingin/panas.',
                    'Gunakan humidifier atau uap hangat jika udara kamar kering.',
                ],
                'red_flags' => [
                    'Nyeri menelan sangat hebat hingga tidak dapat menelan air liur sendiri (drooling).',
                    'Leher membengkak atau tampak pembesaran kelenjar getah bening yang sangat nyeri.',
                    'Suara serak hilang total disertai kesulitan menarik napas.',
                ],
            ],
            [
                'id' => 3,
                'name' => 'Bronkitis Akut',
                'severity_level' => 'sedang',
                'description' => 'Peradangan pada bronkus (saluran udara utama ke paru-paru) yang ditandai dengan batuk persisten berdahak dan rasa dada terbakar.',
                'pathogenesis_overview' => 'Bronkitis Akut terjadi saat respon inflamasi menjalar dari saluran napas atas menuju percabangan bronkus, memicu hipersekresi lendir dan batuk produktif.',
                'pathogenesis_causes' => [
                    'Perluasan Infeksi Saluran Napas: Virus influenza atau RSV turun menginfeksi epitel bersilia bronkus.',
                    'Reaksi Inflamasi Mukosa: Pembengkakan dinding saluran napas memicu refleks batuk terus menerus.',
                ],
                'pathogenesis_risk_factors' => [
                    'Membiarkan Flu Tanpa Istirahat: Melanjutkan aktivitas fisik berat saat terkena batuk pilek.',
                    'Paparan Asap Rokok & Debu: Perokok aktif atau pasif memiliki silia pembersih paru yang kurang optimal.',
                ],
                'recovery_tips' => [
                    'Hindari paparan asap rokok, vape, debu, atau aerosol kimia sama sekali.',
                    'Gunakan inhalasi uap air hangat untuk membantu melonggarkan dahak di saluran dada.',
                    'Perbanyak minum air hangat dan konsumsi sup kaldu bernutrisi.',
                ],
                'red_flags' => [
                    'Batuk berdahak disertai bercak darah segar.',
                    'Sesak napas bertambah berat saat berbaring telentang.',
                    'Demam tinggi menggigil yang tidak turun dengan pereda demam biasa.',
                ],
            ],
            [
                'id' => 4,
                'name' => 'Pneumonia (ISPA Berat / Radang Paru)',
                'severity_level' => 'berat',
                'description' => 'Kondisi kegawatdaruratan infeksi kantung udara paru-paru yang memerlukan evaluasi medis segera di fasilitas kesehatan.',
                'pathogenesis_overview' => 'Pneumonia adalah infeksi akut pada unit pertukaran gas paru-paru (alveoli) yang terisi cairan eksudat dan nanah, menghambat pasokan oksigen ke peredaran darah.',
                'pathogenesis_causes' => [
                    'Infeksi Mikroorganisme Patogen: Bakteri agresif (seperti Streptococcus pneumoniae) atau virus pernapasan yang mencapai parenkim paru.',
                    'Infeksi Sekunder: Komplikasi berat dari flu yang tidak teratasi pada individu rentan.',
                ],
                'pathogenesis_risk_factors' => [
                    'Sistem Imun Sangat Lemah: Usia lanjut (>65 tahun), balita, penderita diabetes, atau asma kronis.',
                    'Kerusakan Mekanisme Pembersih Paru: Kerusakan silia akibat polusi pekat berkepanjangan.',
                ],
                'recovery_tips' => [
                    'SEGERA kunjungi fasilitas kesehatan (Puskesmas / IGD RS) terdekat untuk pemeriksaan saturasi oksigen dan rontgen toraks.',
                    'Jangan menunda penanganan medis dengan hanya mengandalkan obat bebas di rumah.',
                    'Posisikan tubuh setengah duduk untuk memudahkan ventilasi udara paru.',
                ],
                'red_flags' => [
                    'Napas sangat cepat (>24 kali per menit pada dewasa) atau tarikan dinding dada ke dalam.',
                    'Bibir, ujung kuku, atau lidah tampak kebiruan (sianosis).',
                    'Penurunan kesadaran, linglung, atau rasa kantuk yang tidak wajar.',
                ],
            ],
            [
                'id' => 5,
                'name' => 'Eksaserbasi Asma Akut',
                'severity_level' => 'sedang',
                'description' => 'Penyempitan dan hiperaktivitas saluran napas akibat alergen atau infeksi virus yang memicu mengi dan napas sesak.',
                'pathogenesis_overview' => 'Kondisi bronkospasme akut di mana otot polos bronkus berkontraksi kuat, disertai edema mukosa dan sumbatan lendir kental yang menyempitkan jalan napas.',
                'pathogenesis_causes' => [
                    'Reaksi Hipersensitivitas Bronkus: Pemicu lingkungan (debu, serbuk bunga, bulu hewan peliharaan, udara dingin).',
                    'Pemicu Infeksi Virus: Infeksi flu ringan memicu lonjakan reaktivitas saluran napas pada penderita bakat asma.',
                ],
                'pathogenesis_risk_factors' => [
                    'Riwayat Alergi (Atopi): Riwayat keluarga dengan asma, dermatitis atopik, atau rinitis alergi.',
                    'Stres Fisik & Polusi Udara: Berada di dekat jalan raya berpolusi atau aktivitas fisik berlebih tanpa obat pengendali.',
                ],
                'recovery_tips' => [
                    'Gunakan inhaler pereda (reliever) sesuai instruksi dokter spesialis paru Anda.',
                    'Duduk tegak dengan tenang, jangan panik, dan longgarkan pakaian yang ketat di leher dan dada.',
                    'Jauhi ruangan berdebu atau ber-AC dingin ekstrem secara langsung.',
                ],
                'red_flags' => [
                    'Inhaler pereda tidak memberikan perbaikan setelah 2 kali penggunaan.',
                    'Kesulitan berbicara dalam satu kalimat utuh akibat sesak napas hebat.',
                    'Cuping hidung kembang kempis dan tampak kelelahan bernapas.',
                ],
            ],
        ];

        foreach ($diseases as $disease) {
            Disease::updateOrCreate(['id' => $disease['id']], $disease);
        }

        // 3. Pemetaan Gejala ke Penyakit & Bobot
        $weights = [
            // Common Cold (disease 1)
            ['symptom_id' => 1, 'disease_id' => 1, 'weight' => 2.0],
            ['symptom_id' => 3, 'disease_id' => 1, 'weight' => 3.0],
            ['symptom_id' => 4, 'disease_id' => 1, 'weight' => 2.0],
            ['symptom_id' => 5, 'disease_id' => 1, 'weight' => 1.0],

            // Faringitis Akut (disease 2)
            ['symptom_id' => 4, 'disease_id' => 2, 'weight' => 4.0],
            ['symptom_id' => 5, 'disease_id' => 2, 'weight' => 2.0],
            ['symptom_id' => 1, 'disease_id' => 2, 'weight' => 1.5],
            ['symptom_id' => 10, 'disease_id' => 2, 'weight' => 1.0],

            // Bronkitis Akut (disease 3)
            ['symptom_id' => 2, 'disease_id' => 3, 'weight' => 4.0],
            ['symptom_id' => 5, 'disease_id' => 3, 'weight' => 2.0],
            ['symptom_id' => 8, 'disease_id' => 3, 'weight' => 2.0],
            ['symptom_id' => 11, 'disease_id' => 3, 'weight' => 1.5],

            // Pneumonia (disease 4)
            ['symptom_id' => 6, 'disease_id' => 4, 'weight' => 3.5],
            ['symptom_id' => 7, 'disease_id' => 4, 'weight' => 4.0],
            ['symptom_id' => 8, 'disease_id' => 4, 'weight' => 3.0],
            ['symptom_id' => 2, 'disease_id' => 4, 'weight' => 2.5],
            ['symptom_id' => 11, 'disease_id' => 4, 'weight' => 2.0],

            // Eksaserbasi Asma (disease 5)
            ['symptom_id' => 9, 'disease_id' => 5, 'weight' => 4.5],
            ['symptom_id' => 7, 'disease_id' => 5, 'weight' => 3.5],
            ['symptom_id' => 1, 'disease_id' => 5, 'weight' => 1.5],
        ];

        foreach ($weights as $map) {
            SymptomDiseaseMap::updateOrCreate(
                ['symptom_id' => $map['symptom_id'], 'disease_id' => $map['disease_id']],
                ['weight' => $map['weight']]
            );
        }

        // 4. Dokter Mitra Konsultasi Online
        $doctors = [
            [
                'id' => 1,
                'name' => 'Dokter Spesialis Paru & Pernapasan',
                'platform' => 'Halodoc',
                'profile_url' => 'https://www.halodoc.com/tanya-dokter',
                'specialty' => 'Pulmonologi',
                'hospital' => 'Mitra Klinis ISHAP',
            ],
            [
                'id' => 2,
                'name' => 'Dokter Umum - Skrining ISPA',
                'platform' => 'Alodokter',
                'profile_url' => 'https://www.alodokter.com/tanya-dokter',
                'specialty' => 'Dokter Umum',
                'hospital' => 'Klinik Telemedika Pratama',
            ],
            [
                'id' => 3,
                'name' => 'Dokter Spesialis Anak (ISPA pada Anak)',
                'platform' => 'Halodoc',
                'profile_url' => 'https://www.halodoc.com/tanya-dokter',
                'specialty' => 'Pediatri',
                'hospital' => 'RSIA Tumbuh Sehat',
            ],
        ];

        foreach ($doctors as $doctor) {
            OnlineDoctorProfile::updateOrCreate(['id' => $doctor['id']], $doctor);
        }
    }
}
