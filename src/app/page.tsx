import Link from "next/link";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Badge } from "@/components/ui/Badge";

export default function HomePage() {
  return (
    <div className="container">
      {/* Hero Section */}
      <section style={{ textAlign: "center", padding: "40px 0 60px" }}>
        <Badge variant="info" style={{ marginBottom: "16px", padding: "6px 16px" }}>
          Inisiatif Kesehatan Digital & Deteksi Dini
        </Badge>
        <h1 className="title-hero" style={{ marginBottom: "20px" }}>
          Deteksi Dini Risiko ISPA dengan{" "}
          <span className="gradient-text">Skrining Mandiri Cerdas</span>
        </h1>
        <p className="subtitle-hero" style={{ margin: "0 auto 32px" }}>
          Evaluasi pola gejala pernapasan Anda secara cepat dan terstruktur, pantau indeks kualitas udara (AQI) setempat, serta dapatkan rujukan fasilitas kesehatan atau konsultasi daring terpercaya.
        </p>
        <div style={{ display: "flex", gap: "16px", justifyContent: "center", flexWrap: "wrap" }}>
          <Link href="/screening">
            <Button size="lg">Mulai Skrining Mandiri Sekarang</Button>
          </Link>
          <a href="#edukasi">
            <Button variant="secondary" size="lg">
              Pelajari Gejala ISPA
            </Button>
          </a>
        </div>
      </section>

      {/* AQI Monitoring Widget */}
      <section style={{ marginBottom: "60px" }}>
        <Card
          style={{
            background: "linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%)",
            border: "1px solid #bae6fd",
            padding: "28px",
          }}
        >
          <div
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "center",
              flexWrap: "wrap",
              gap: "20px",
            }}
          >
            <div>
              <div style={{ display: "flex", alignItems: "center", gap: "10px", marginBottom: "8px" }}>
                <span style={{ fontSize: "1.2rem" }}>🍃</span>
                <h3 style={{ fontSize: "1.25rem", fontWeight: 700 }}>
                  Pantauan Kualitas Udara (AQI) Terkini
                </h3>
                <Badge variant="success">Sedang (Moderate)</Badge>
              </div>
              <p style={{ color: "var(--text-secondary)", fontSize: "0.95rem", maxWidth: "680px" }}>
                Polusi udara dan partikel PM2.5 merupakan pemicu utama iritasi saluran pernapasan dan peningkatan risiko infeksi ISPA. Selalu gunakan masker standar saat beraktivitas di luar ruangan jika indeks menurun.
              </p>
            </div>
            <div
              style={{
                backgroundColor: "#ffffff",
                padding: "16px 24px",
                borderRadius: "var(--radius-md)",
                textAlign: "center",
                boxShadow: "var(--shadow-sm)",
                border: "1px solid #e2e8f0",
              }}
            >
              <div style={{ fontSize: "2rem", fontWeight: 800, color: "var(--accent-emerald)" }}>
                68
              </div>
              <div style={{ fontSize: "0.75rem", color: "var(--text-muted)", textTransform: "uppercase" }}>
                AQI US (PM2.5)
              </div>
            </div>
          </div>
        </Card>
      </section>

      {/* Edukasi ISPA Section */}
      <section id="edukasi" style={{ marginBottom: "60px" }}>
        <div style={{ textAlign: "center", marginBottom: "36px" }}>
          <h2 style={{ fontSize: "1.875rem", fontWeight: 800 }}>
            Kenali Gejala & Risiko ISPA
          </h2>
          <p style={{ color: "var(--text-secondary)", marginTop: "8px" }}>
            Infeksi Saluran Pernapasan Akut (ISPA) dapat menyerang saluran pernapasan atas maupun bawah.
          </p>
        </div>

        <div
          style={{
            display: "grid",
            gridTemplateColumns: "repeat(auto-fit, minmax(300px, 1fr))",
            gap: "24px",
          }}
        >
          <Card>
            <div style={{ fontSize: "2rem", marginBottom: "12px" }}>🌡️</div>
            <h3 style={{ fontSize: "1.2rem", fontWeight: 700, marginBottom: "8px" }}>
              Gejala Umum ISPA Ringan
            </h3>
            <p style={{ color: "var(--text-secondary)", fontSize: "0.9rem", lineHeight: 1.6 }}>
              Batuk ringan, pilek, bersin, hidung tersumbat, dan nyeri tenggorokan tanpa disertai sesak napas. Biasanya dapat membaik dengan istirahat cukup dan hidrasi.
            </p>
          </Card>

          <Card>
            <div style={{ fontSize: "2rem", marginBottom: "12px" }}>⚠️</div>
            <h3 style={{ fontSize: "1.2rem", fontWeight: 700, marginBottom: "8px" }}>
              Tanda Bahaya (Red Flags)
            </h3>
            <p style={{ color: "var(--text-secondary)", fontSize: "0.9rem", lineHeight: 1.6 }}>
              Napas cepat atau tersengal-sengal, tarikan dinding dada bagian bawah ke dalam, bibir membiru, demam tinggi persisten, atau penurunan kesadaran membutuhkan penanganan medis segera!
            </p>
          </Card>

          <Card>
            <div style={{ fontSize: "2rem", marginBottom: "12px" }}>🛡️</div>
            <h3 style={{ fontSize: "1.2rem", fontWeight: 700, marginBottom: "8px" }}>
              Pencegahan & Proteksi Diri
            </h3>
            <p style={{ color: "var(--text-secondary)", fontSize: "0.9rem", lineHeight: 1.6 }}>
              Menggunakan masker saat polusi udara tinggi, mencuci tangan dengan sabun, menjaga ventilasi ruangan, dan mendapatkan imunisasi influenza/pneumokokus.
            </p>
          </Card>
        </div>
      </section>

      {/* CTA Box */}
      <section
        style={{
          background: "linear-gradient(135deg, #0284c7 0%, #0d9488 100%)",
          color: "#ffffff",
          borderRadius: "var(--radius-lg)",
          padding: "48px 32px",
          textAlign: "center",
          boxShadow: "var(--shadow-lg)",
        }}
      >
        <h2 style={{ fontSize: "2rem", fontWeight: 800, marginBottom: "12px" }}>
          Mulai Skrining Mandiri Anda Sekarang
        </h2>
        <p style={{ maxWidth: "600px", margin: "0 auto 28px", opacity: 0.9 }}>
          Cukup 2 menit untuk mengevaluasi keluhan dan mengetahui tingkat perkiraan risiko Anda secara gratis dan privat.
        </p>
        <Link href="/screening">
          <Button
            style={{
              backgroundColor: "#ffffff",
              color: "var(--primary-dark)",
              fontWeight: 700,
            }}
            size="lg"
          >
            Lakukan Skrining Gejala
          </Button>
        </Link>
      </section>
    </div>
  );
}
