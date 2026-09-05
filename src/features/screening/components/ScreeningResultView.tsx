"use client";

import React, { useEffect, useState } from "react";
import Link from "next/link";
import { screeningService } from "../services/screeningService";
import type { ScreeningResultResponse } from "../types/screening.types";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";

import { EducationalTips } from "./EducationalTips";

interface Props {
  sessionId: string;
}

export const ScreeningResultView: React.FC<Props> = ({ sessionId }) => {
  const [data, setData] = useState<ScreeningResultResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let mounted = true;
    async function fetchResult() {
      try {
        const res = await screeningService.getScreeningResult(sessionId);
        if (mounted) setData(res);
      } catch (err: any) {
        if (mounted) setError(err.message || "Gagal mengambil hasil skrining");
      } finally {
        if (mounted) setLoading(false);
      }
    }
    fetchResult();
    return () => {
      mounted = false;
    };
  }, [sessionId]);

  if (loading) {
    return (
      <div style={{ textAlign: "center", padding: "80px 0" }}>
        <p style={{ color: "var(--text-secondary)", fontSize: "1.1rem" }}>
          Menganalisis hasil perkiraan risiko skrining...
        </p>
      </div>
    );
  }

  if (error || !data) {
    return (
      <div style={{ maxWidth: "600px", margin: "40px auto", textAlign: "center" }}>
        <Card style={{ padding: "32px" }}>
          <h2 style={{ color: "var(--accent-red)", marginBottom: "12px" }}>Terjadi Kendala</h2>
          <p style={{ color: "var(--text-secondary)", marginBottom: "24px" }}>
            {error || "Hasil skrining tidak ditemukan."}
          </p>
          <Link href="/screening">
            <Button>Ulangi Skrining Mandiri</Button>
          </Link>
        </Card>
      </div>
    );
  }

  // Ambil hanya 1 hasil yang paling mendekati dengan skor kecocokan tertinggi
  const topResult = data.results && data.results.length > 0 ? data.results[0] : null;
  const isHighRisk = topResult ? topResult.confidence_score >= 60 : false;
  const badgeVariant = isHighRisk ? "warning" : "info";

  return (
    <div style={{ maxWidth: "840px", margin: "0 auto" }}>
      <div style={{ marginBottom: "28px", textAlign: "center" }}>
        <Badge variant="success" style={{ marginBottom: "12px" }}>
          Skrining Selesai Diproses
        </Badge>
        <h1 style={{ fontSize: "2.25rem", fontWeight: 800 }}>
          Perkiraan Risiko Gejala ISPA
        </h1>
        <p style={{ color: "var(--text-secondary)", marginTop: "8px" }}>
          Berdasarkan seleksi gejala yang Anda masukkan, berikut adalah indikator kondisi yang paling mendekati.
        </p>
      </div>

      {/* Medical Disclaimer Banner */}
      <div
        style={{
          padding: "16px 20px",
          backgroundColor: "var(--accent-amber-light)",
          borderLeft: "4px solid var(--accent-amber)",
          borderRadius: "var(--radius-sm)",
          marginBottom: "24px",
          fontSize: "0.875rem",
          color: "#78350f",
          lineHeight: 1.5,
        }}
      >
        <strong>Pernyataan Medis Penting:</strong> {data.disclaimer}
      </div>

      {/* Kartu Utama: 1 Hasil Skrining Paling Mendekati */}
      {!topResult ? (
        <Card style={{ textAlign: "center", padding: "40px 24px" }}>
          <span style={{ fontSize: "2.5rem", display: "block", marginBottom: "12px" }}>🩺</span>
          <h3 style={{ fontSize: "1.25rem", fontWeight: 700, marginBottom: "8px" }}>
            Pola Gejala Tidak Mengarah ke Kondisi Spesifik
          </h3>
          <p style={{ color: "var(--text-secondary)", maxWidth: "500px", margin: "0 auto 20px auto" }}>
            Gejala yang Anda pilih belum menunjukkan pola spesifik ISPA pada sistem skrining mandiri kami. Tetap jaga kondisi tubuh dan periksakan ke dokter jika keluhan menetap.
          </p>
          <Link href="/screening">
            <Button variant="secondary">Coba Skrining Ulang</Button>
          </Link>
        </Card>
      ) : (
        <Card style={{ padding: "28px", border: "1.5px solid var(--border-color)" }}>
          <div
            style={{
              display: "flex",
              justifyContent: "space-between",
              alignItems: "flex-start",
              flexWrap: "wrap",
              gap: "12px",
              marginBottom: "16px",
            }}
          >
            <div>
              <span
                style={{
                  fontSize: "0.8rem",
                  color: "var(--text-muted)",
                  textTransform: "uppercase",
                  letterSpacing: "0.08em",
                  fontWeight: 600,
                  display: "block",
                  marginBottom: "4px",
                }}
              >
                Kondisi Paling Mendekati Gejala
              </span>
              <h2 style={{ fontSize: "1.6rem", fontWeight: 800 }}>
                {topResult.disease_name}
              </h2>
            </div>
            <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
              {topResult.severity_level && (
                <Badge variant={topResult.severity_level === "berat" ? "danger" : topResult.severity_level === "sedang" ? "warning" : "info"}>
                  Tingkat {topResult.severity_level}
                </Badge>
              )}
              <Badge variant={badgeVariant}>
                Kecocokan: {topResult.confidence_score}%
              </Badge>
            </div>
          </div>

          <p style={{ color: "var(--text-secondary)", fontSize: "1rem", lineHeight: 1.6, marginBottom: "20px" }}>
            {topResult.reasoning}
          </p>

          {/* Visual Progress Bar Kecocokan */}
          <div>
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.85rem", color: "var(--text-secondary)", marginBottom: "6px" }}>
              <span>Tingkat Estimasi Risiko</span>
              <span><strong>{topResult.confidence_score}%</strong> kecocokan pola gejala</span>
            </div>
            <div
              style={{
                width: "100%",
                height: "10px",
                backgroundColor: "var(--border-color)",
                borderRadius: "5px",
                overflow: "hidden",
              }}
            >
              <div
                style={{
                  width: `${Math.min(topResult.confidence_score, 100)}%`,
                  height: "100%",
                  backgroundColor:
                    topResult.severity_level === "berat"
                      ? "var(--accent-red)"
                      : isHighRisk
                      ? "var(--accent-amber)"
                      : "var(--primary)",
                  borderRadius: "5px",
                  transition: "width 0.5s ease-in-out",
                }}
              />
            </div>
          </div>
        </Card>
      )}

      {/* Komponen Penjelasan Penyebab Penyakit & Tips Pencegahan */}
      {topResult && (
        <EducationalTips
          severityLevel={topResult.severity_level}
          diseaseName={topResult.disease_name}
          diseaseDescription={topResult.disease_description}
        />
      )}

      {/* Tindakan Lanjutan & Rujukan Faskes / Dokter */}
      <div
        style={{
          display: "grid",
          gridTemplateColumns: "repeat(auto-fit, minmax(280px, 1fr))",
          gap: "16px",
          marginTop: "28px",
          marginBottom: "36px",
        }}
      >
        <Card style={{ padding: "24px" }}>
          <h4 style={{ fontSize: "1.1rem", fontWeight: 700, marginBottom: "8px" }}>
            🏥 Fasilitas Kesehatan Terdekat
          </h4>
          <p style={{ fontSize: "0.875rem", color: "var(--text-secondary)", marginBottom: "16px" }}>
            Cari Puskesmas, Klinik, atau Rumah Sakit terdekat di sekitar lokasi Anda untuk pemeriksaan fisik.
          </p>
          <Link href={`/facilities?session=${sessionId}`}>
            <Button variant="secondary" size="sm" style={{ width: "100%" }}>
              Cari Faskes Terdekat
            </Button>
          </Link>
        </Card>

        <Card style={{ padding: "24px" }}>
          <h4 style={{ fontSize: "1.1rem", fontWeight: 700, marginBottom: "8px" }}>
            👨‍⚕️ Konsultasi Dokter Online
          </h4>
          <p style={{ fontSize: "0.875rem", color: "var(--text-secondary)", marginBottom: "16px" }}>
            Hubungi dokter umum atau spesialis paru secara daring untuk konsultasi dan resep pengobatan.
          </p>
          <Link href={`/consultation?session=${sessionId}`}>
            <Button variant="primary" size="sm" style={{ width: "100%" }}>
              Lihat Profil Dokter Mitra
            </Button>
          </Link>
        </Card>
      </div>

      <div style={{ textAlign: "center", marginBottom: "40px" }}>
        <Link href="/screening">
          <Button variant="secondary">Mulai Skrining Baru</Button>
        </Link>
      </div>
    </div>
  );
};
