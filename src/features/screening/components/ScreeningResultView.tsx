"use client";

import React, { useEffect, useState } from "react";
import Link from "next/link";
import { screeningService } from "../services/screeningService";
import type { ScreeningResultResponse } from "../types/screening.types";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";

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

  return (
    <div style={{ maxWidth: "840px", margin: "0 auto" }}>
      <div style={{ marginBottom: "32px", textAlign: "center" }}>
        <Badge variant="success" style={{ marginBottom: "12px" }}>
          Skrining Selesai Diproses
        </Badge>
        <h1 style={{ fontSize: "2.25rem", fontWeight: 800 }}>
          Perkiraan Risiko Gejala ISPA
        </h1>
        <p style={{ color: "var(--text-secondary)", marginTop: "8px" }}>
          Berikut adalah hasil evaluasi awal berdasarkan indikator gejala yang Anda masukkan.
        </p>
      </div>

      {/* Medical Disclaimer Banner */}
      <div
        style={{
          padding: "16px 20px",
          backgroundColor: "var(--accent-amber-light)",
          borderLeft: "4px solid var(--accent-amber)",
          borderRadius: "var(--radius-sm)",
          marginBottom: "28px",
          fontSize: "0.875rem",
          color: "#78350f",
          lineHeight: 1.5,
        }}
      >
        <strong>Pernyataan Medis Penting:</strong> {data.disclaimer}
      </div>

      {/* Results List */}
      <div style={{ display: "grid", gap: "16px", marginBottom: "36px" }}>
        {data.results.length === 0 ? (
          <Card style={{ textAlign: "center", padding: "36px" }}>
            <p style={{ color: "var(--text-secondary)" }}>
              Tidak teridentifikasi indikator risiko spesifik pada pola gejala yang dimasukkan. Jika keluhan berlanjut, harap periksakan diri ke dokter.
            </p>
          </Card>
        ) : (
          data.results.map((result) => {
            const isHighRisk = result.confidence_score >= 60;
            const badgeVariant = isHighRisk ? "warning" : "info";

            return (
              <Card key={result.id} style={{ padding: "24px" }}>
                <div
                  style={{
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "flex-start",
                    marginBottom: "12px",
                  }}
                >
                  <div>
                    <h3 style={{ fontSize: "1.25rem", fontWeight: 700 }}>
                      {result.disease_name}
                    </h3>
                    {result.severity_level && (
                      <span
                        style={{
                          fontSize: "0.8rem",
                          color: "var(--text-muted)",
                          textTransform: "uppercase",
                          letterSpacing: "0.05em",
                        }}
                      >
                        Tingkat Keparahan: {result.severity_level}
                      </span>
                    )}
                  </div>
                  <Badge variant={badgeVariant}>
                    Estimasi Kecocokan: {result.confidence_score}%
                  </Badge>
                </div>

                <p style={{ color: "var(--text-secondary)", fontSize: "0.95rem" }}>
                  {result.reasoning}
                </p>

                {/* Visual Progress Bar */}
                <div
                  style={{
                    width: "100%",
                    height: "8px",
                    backgroundColor: "var(--border-color)",
                    borderRadius: "4px",
                    overflow: "hidden",
                    marginTop: "16px",
                  }}
                >
                  <div
                    style={{
                      width: `${Math.min(result.confidence_score, 100)}%`,
                      height: "100%",
                      backgroundColor: isHighRisk ? "var(--accent-amber)" : "var(--primary)",
                      borderRadius: "4px",
                    }}
                  />
                </div>
              </Card>
            );
          })
        )}
      </div>

      {/* Tindakan Lanjutan & Rujukan */}
      <div
        style={{
          display: "grid",
          gridTemplateColumns: "repeat(auto-fit, minmax(280px, 1fr))",
          gap: "16px",
          marginBottom: "40px",
        }}
      >
        <Card style={{ padding: "24px" }}>
          <h4 style={{ fontSize: "1.1rem", fontWeight: 700, marginBottom: "8px" }}>
            🏥 Fasilitas Kesehatan Terdekat
          </h4>
          <p style={{ fontSize: "0.875rem", color: "var(--text-secondary)", marginBottom: "16px" }}>
            Temukan Puskesmas atau Rumah Sakit terdekat di sekitar lokasi Anda untuk pemeriksaan fisik langsung.
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
            Hubungi dokter umum atau spesialis paru secara daring melalui platform mitra terpercaya.
          </p>
          <Link href={`/consultation?session=${sessionId}`}>
            <Button variant="primary" size="sm" style={{ width: "100%" }}>
              Lihat Profil Dokter Mitra
            </Button>
          </Link>
        </Card>
      </div>

      <div style={{ textAlign: "center" }}>
        <Link href="/screening">
          <Button variant="secondary">Mulai Skrining Baru</Button>
        </Link>
      </div>
    </div>
  );
};
