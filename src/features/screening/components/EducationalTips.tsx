import React from "react";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";

interface EducationalTipsProps {
  severityLevel?: string | null;
  diseaseName?: string;
  diseaseDescription?: string | null;
}

interface DiseaseCauseInfo {
  overview: string;
  causes: string[];
  riskFactors: string[];
}

/**
 * Pemetaan edukasi penyebab & faktor penularan berdasarkan kondisi ISPA yang teridentifikasi.
 */
function getDiseaseCauseDetails(diseaseName: string = ""): DiseaseCauseInfo {
  const nameLower = diseaseName.toLowerCase();

  if (nameLower.includes("common cold") || nameLower.includes("ispa ringan")) {
    return {
      overview:
        "ISPA Ringan (Common Cold) umumnya disebabkan oleh infeksi virus saluran napas atas (terbanyak adalah Rhinovirus, Coronavirus musiman, atau Adenovirus) yang menginfeksi lapisan mukosa hidung dan tenggorokan.",
      causes: [
        "Inhalasi Droplet: Menghirup percikan udara saat orang di sekitar Anda yang sedang flu batuk, bersin, atau berbicara.",
        "Kontak Permukaan Tangan: Menyentuh gagang pintu, layar ponsel, atau meja publik yang terkontaminasi virus, lalu tanpa sadar menyentuh area mata, hidung, atau mulut.",
      ],
      riskFactors: [
        "Penurunan Daya Tahan Tubuh: Kurang tidur, kelelahan fisik berat, atau stres berkepanjangan yang menurunkan respon imun sekresi mukosa.",
        "Perubahan Cuaca Ekstrem / Pancaroba: Udara dingin dan kering dapat mengeringkan lapisan lendir alami saluran napas, membuat virus lebih mudah menempel.",
      ],
    };
  }

  if (nameLower.includes("faringitis") || nameLower.includes("radang tenggorokan")) {
    return {
      overview:
        "Faringitis Akut adalah peradangan pada jaringan faring (dinding belakang tenggorokan). Sebagian besar dipicu oleh virus flu, namun sekitar 15–30% dapat disebabkan oleh infeksi bakteri (seperti Streptococcus).",
      causes: [
        "Paparan Patogen Saluran Napas: Penularan langsung dari air liur atau droplet pernapasan orang yang terinfeksi.",
        "Iritasi Kimiawi & Lingkungan: Menghirup udara kering, polusi jalanan, atau asap rokok yang mengikis lapisan pelindung dinding tenggorokan.",
      ],
      riskFactors: [
        "Bernapas Melalui Mulut: Terjadi saat hidung tersumbat atau saat tidur, menyebabkan tenggorokan kering dan meradang.",
        "Makanan & Minuman Iritatif: Mengonsumsi makanan yang terlalu panas, pedas menyengat, atau berminyak berlebihan saat kondisi tubuh sedang rentan.",
      ],
    };
  }

  if (nameLower.includes("bronkitis")) {
    return {
      overview:
        "Bronkitis Akut adalah peradangan pada pipa bronkus (saluran udara utama yang mengalirkan oksigen ke paru-paru). Kondisi ini kerap terjadi sebagai perkembangan lanjutan dari flu atau batuk pilek biasa.",
      causes: [
        "Perluasan Infeksi Virus: Virus dari saluran napas atas (seperti Influenza atau RSV) menyebar ke bawah menuju cabang-cabang bronkus.",
        "Reaksi Inflamasi Mukosa Bronkus: Dinding bronkus membengkak dan memproduksi dahak berlebih sebagai mekanisme pertahanan tubuh terhadap infeksi.",
      ],
      riskFactors: [
        "Membiarkan Batuk Pilek Tanpa Istirahat: Melanjutkan aktivitas berat saat terserang flu membuat infeksi turun ke saluran napas bawah.",
        "Paparan Iritan Paru: Merokok aktif, sering terpapar asap rokok orang lain (perokok pasif), debu proyek, atau polusi knalpot pekat.",
      ],
    };
  }

  if (nameLower.includes("pneumonia") || nameLower.includes("radang paru")) {
    return {
      overview:
        "Pneumonia adalah infeksi akut pada kantung udara paru-paru (alveoli) yang menyebabkan alveoli terisi cairan atau nanah, sehingga penyerapan oksigen ke darah terganggu.",
      causes: [
        "Infeksi Mikroorganisme Patogen: Disebabkan oleh bakteri agresif (seperti Streptococcus pneumoniae), virus pernapasan berat, atau jamur.",
        "Infeksi Sekunder (Secondary Infection): Berawal dari flu atau infeksi paru ringan yang tidak tertangani, lalu ditunggangi oleh bakteri yang berkembang biak dengan cepat.",
      ],
      riskFactors: [
        "Sistem Imun Sangat Lemah: Lansia, balita, atau individu dengan penyakit bawaan memiliki perlindungan paru yang lebih rentan.",
        "Kerusakan Silia Paru: Kebiasaan merokok merusak bulu-bulu halus (silia) pembersih saluran napas, memudahkan bakteri mengendap di kantung paru.",
      ],
    };
  }

  if (nameLower.includes("asma")) {
    return {
      overview:
        "Eksaserbasi Asma Akut terjadi ketika saluran pernapasan mengalami penyempitan mendadak (bronkospasme), pembengkakan dinding napas, dan penumpukan lendir kental akibat reaksi hipersensitivitas.",
      causes: [
        "Reaksi Hipersensitivitas Bronkus: Otot polos saluran napas berkontraksi kuat saat terpapar zat pemicu (alergen atau iritan).",
        "Infeksi Saluran Napas Atas: Infeksi virus flu ringan sering kali menjadi pemicu utama timbulnya serangan napas berbunyi (mengi) dan sesak.",
      ],
      riskFactors: [
        "Paparan Alergen & Suhu Dingin: Debu tungau, bulu hewan, serbuk bunga, atau udara malam yang dingin dan kering.",
        "Stres Fisik atau Polusi Udara: Menghirup asap kendaraan pekat atau berolahraga berat di lingkungan berpolusi tanpa pemanasan cukup.",
      ],
    };
  }

  // Fallback umum jika kondisi umum ISPA
  return {
    overview:
      "Infeksi Saluran Pernapasan Akut (ISPA) terjadi akibat invasi mikroorganisme patogen (virus atau bakteri) pada membran mukosa saluran pernapasan manusia.",
    causes: [
      "Penularan Droplet Udara: Terpapar droplet pernapasan dari penderita yang batuk, bersin, atau berbicara.",
      "Kontak Tangan & Wajah: Memindahkan kuman dari permukaan benda ke hidung, mulut, atau mata.",
    ],
    riskFactors: [
      "Kelelahan & Daya Tahan Tubuh Menurun: Kurang istirahat dan nutrisi tidak seimbang.",
      "Faktor Lingkungan: Ruangan tertutup tanpa sirkulasi udara baik serta polusi udara luar ruangan.",
    ],
  };
}

export const EducationalTips: React.FC<EducationalTipsProps> = ({
  severityLevel,
  diseaseName = "Kondisi ISPA",
  diseaseDescription,
}) => {
  const causeInfo = getDiseaseCauseDetails(diseaseName);
  const isSevere = severityLevel === "berat";
  const isModerate = severityLevel === "sedang";

  return (
    <div style={{ display: "grid", gap: "20px", marginTop: "24px" }}>
      {/* 1. PENJELASAN MENGAPA KONDISI INI BISA TERJADI (ETIOLOGI & FAKTOR PENULARAN) */}
      <Card
        style={{
          padding: "26px",
          borderLeft: isSevere
            ? "4px solid var(--accent-red)"
            : isModerate
            ? "4px solid var(--accent-amber)"
            : "4px solid var(--primary)",
        }}
      >
        <div style={{ display: "flex", alignItems: "center", gap: "12px", marginBottom: "16px" }}>
          <span style={{ fontSize: "1.75rem" }}>🔍</span>
          <div>
            <h3 style={{ fontSize: "1.2rem", fontWeight: 700 }}>
              Mengapa Anda Mengalami Gejala Ini?
            </h3>
            <p style={{ fontSize: "0.85rem", color: "var(--text-secondary)" }}>
              Penjelasan klinis dan faktor penyebab yang melatarbelakangi {diseaseName}
            </p>
          </div>
        </div>

        {/* Ringkasan Medis */}
        <p
          style={{
            fontSize: "0.95rem",
            color: "var(--text-primary)",
            lineHeight: 1.6,
            marginBottom: "18px",
            backgroundColor: "var(--background-secondary)",
            padding: "14px 16px",
            borderRadius: "var(--radius-sm)",
            border: "1px solid var(--border-color)",
          }}
        >
          {diseaseDescription || causeInfo.overview}
        </p>

        {/* 2 Kolom: Cara Masuknya Kuman & Faktor yang Memicunya */}
        <div
          style={{
            display: "grid",
            gridTemplateColumns: "repeat(auto-fit, minmax(280px, 1fr))",
            gap: "16px",
          }}
        >
          {/* Jalur Penularan / Penyebab */}
          <div
            style={{
              padding: "16px",
              backgroundColor: "var(--background-secondary)",
              borderRadius: "var(--radius-sm)",
            }}
          >
            <strong style={{ display: "flex", alignItems: "center", gap: "6px", marginBottom: "10px", fontSize: "0.95rem" }}>
              <span>🦠</span> Bagaimana Kuman / Iritan Masuk?
            </strong>
            <ul style={{ margin: 0, paddingLeft: "18px", fontSize: "0.875rem", color: "var(--text-secondary)", lineHeight: 1.5 }}>
              {causeInfo.causes.map((c, idx) => (
                <li key={idx} style={{ marginBottom: "6px" }}>
                  {c}
                </li>
              ))}
            </ul>
          </div>

          {/* Mengapa Tubuh Rentan / Faktor Risiko */}
          <div
            style={{
              padding: "16px",
              backgroundColor: "var(--background-secondary)",
              borderRadius: "var(--radius-sm)",
            }}
          >
            <strong style={{ display: "flex", alignItems: "center", gap: "6px", marginBottom: "10px", fontSize: "0.95rem" }}>
              <span>📉</span> Mengapa Tubuh Anda Rentan?
            </strong>
            <ul style={{ margin: 0, paddingLeft: "18px", fontSize: "0.875rem", color: "var(--text-secondary)", lineHeight: 1.5 }}>
              {causeInfo.riskFactors.map((r, idx) => (
                <li key={idx} style={{ marginBottom: "6px" }}>
                  {r}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </Card>

      {/* 2. PANDUAN PENCEGAHAN & PERAWATAN MANDIRI DI RUMAH */}
      <Card style={{ padding: "26px" }}>
        <div style={{ display: "flex", alignItems: "center", gap: "12px", marginBottom: "16px" }}>
          <span style={{ fontSize: "1.75rem" }}>🛡️</span>
          <div>
            <h3 style={{ fontSize: "1.2rem", fontWeight: 700 }}>
              Panduan Pencegahan & Perawatan Pemulihan
            </h3>
            <p style={{ fontSize: "0.85rem", color: "var(--text-secondary)" }}>
              Langkah mandiri untuk mempercepat kesembuhan dan mencegah penularan ke keluarga
            </p>
          </div>
        </div>

        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(230px, 1fr))", gap: "14px" }}>
          <div
            style={{
              padding: "14px",
              backgroundColor: "var(--background-secondary)",
              borderRadius: "var(--radius-sm)",
              border: "1px solid var(--border-color)",
            }}
          >
            <strong style={{ display: "block", marginBottom: "4px", fontSize: "0.95rem" }}>
              💧 Hidrasi & Cairan Hangat
            </strong>
            <p style={{ fontSize: "0.85rem", color: "var(--text-secondary)", margin: 0, lineHeight: 1.4 }}>
              Minum air putih hangat minimal 2–2.5 liter per hari untuk mengencerkan lendir dan menjaga kelembapan tenggorokan.
            </p>
          </div>

          <div
            style={{
              padding: "14px",
              backgroundColor: "var(--background-secondary)",
              borderRadius: "var(--radius-sm)",
              border: "1px solid var(--border-color)",
            }}
          >
            <strong style={{ display: "block", marginBottom: "4px", fontSize: "0.95rem" }}>
              😷 Pakai Masker & Etika Batuk
            </strong>
            <p style={{ fontSize: "0.85rem", color: "var(--text-secondary)", margin: 0, lineHeight: 1.4 }}>
              Kenakan masker medis di rumah untuk memutus droplet penularan ke orang lain, serta tutup mulut dengan tisu saat bersin.
            </p>
          </div>

          <div
            style={{
              padding: "14px",
              backgroundColor: "var(--background-secondary)",
              borderRadius: "var(--radius-sm)",
              border: "1px solid var(--border-color)",
            }}
          >
            <strong style={{ display: "block", marginBottom: "4px", fontSize: "0.95rem" }}>
              🧼 Cuci Tangan Berkala
            </strong>
            <p style={{ fontSize: "0.85rem", color: "var(--text-secondary)", margin: 0, lineHeight: 1.4 }}>
              Cuci tangan menggunakan sabun setidaknya 20 detik setelah memegang hidung atau sebelum mengolah makanan.
            </p>
          </div>

          <div
            style={{
              padding: "14px",
              backgroundColor: "var(--background-secondary)",
              borderRadius: "var(--radius-sm)",
              border: "1px solid var(--border-color)",
            }}
          >
            <strong style={{ display: "block", marginBottom: "4px", fontSize: "0.95rem" }}>
              🌬️ Jaga Sirkulasi & Ventilasi
            </strong>
            <p style={{ fontSize: "0.85rem", color: "var(--text-secondary)", margin: 0, lineHeight: 1.4 }}>
              Buka jendela kamar agar udara segar bersirkulasi dan hindari paparan asap rokok atau debu pekat.
            </p>
          </div>
        </div>

        {/* Tanda Bahaya Singkat */}
        <div
          style={{
            marginTop: "18px",
            padding: "12px 16px",
            backgroundColor: isSevere ? "rgba(239, 68, 68, 0.08)" : "var(--background-secondary)",
            borderRadius: "var(--radius-sm)",
            fontSize: "0.85rem",
            color: isSevere ? "var(--accent-red)" : "var(--text-secondary)",
            display: "flex",
            alignItems: "flex-start",
            gap: "8px",
          }}
        >
          <span>⚠️</span>
          <span>
            <strong>Kapan Harus Segera ke Faskes/IGD?</strong> Jika timbul sesak napas berat, bibir membiru, demam &gt;38.5°C lebih dari 3 hari, atau batuk berdarah, segera kunjungi fasilitas kesehatan terdekat.
          </span>
        </div>
      </Card>
    </div>
  );
};
