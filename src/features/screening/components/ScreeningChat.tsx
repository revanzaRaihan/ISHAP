"use client";

import React, { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { screeningService } from "../services/screeningService";
import type { Symptom } from "../types/screening.types";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Badge } from "@/components/ui/Badge";

export const ScreeningChat: React.FC = () => {
  const router = useRouter();
  const [symptoms, setSymptoms] = useState<Symptom[]>([]);
  const [selectedIds, setSelectedIds] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  useEffect(() => {
    let mounted = true;
    async function load() {
      try {
        const data = await screeningService.getSymptoms();
        if (mounted) {
          setSymptoms(data);
        }
      } catch (err: any) {
        if (mounted) {
          // Fallback mockup gejala jika DB belum terisi seed
          setSymptoms([
            { id: "e1a10001-0000-0000-0000-000000000001", name: "Batuk Kering atau Berdahak", category: "Saluran Napas Atas", description: "Batuk terus-menerus selama beberapa hari" },
            { id: "e1a10001-0000-0000-0000-000000000002", name: "Demam atau Rasa Menggigil", category: "Sistemik", description: "Suhu tubuh terasa hangat atau > 37.5°C" },
            { id: "e1a10001-0000-0000-0000-000000000003", name: "Hidung Tersumbat / Pilek", category: "Saluran Napas Atas", description: "Keluar cairan hidung bening atau kental" },
            { id: "e1a10001-0000-0000-0000-000000000004", name: "Nyeri Tenggorokan", category: "Saluran Napas Atas", description: "Rasa perih atau sakit saat menelan" },
            { id: "e1a10001-0000-0000-0000-000000000005", name: "Sesak Napas / Napas Cepat", category: "Saluran Napas Bawah", description: "Kesulitan bernapas atau dada terasa berat" },
            { id: "e1a10001-0000-0000-0000-000000000006", name: "Sakit Kepala & Pegal Tubuh", category: "Sistemik", description: "Nyeri otot dan kelelahan berat" },
          ]);
        }
      } finally {
        if (mounted) setLoading(false);
      }
    }
    load();
    return () => {
      mounted = false;
    };
  }, []);

  const toggleSymptom = (id: string) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
    );
  };

  const handleSubmit = async () => {
    if (selectedIds.length === 0) {
      setErrorMessage("Silakan pilih minimal 1 keluhan atau gejala yang Anda rasakan.");
      return;
    }

    setErrorMessage(null);
    setSubmitting(true);

    try {
      // 1. Buat sesi di server
      const { sessionId } = await screeningService.createSession();

      // 2. Submit gejala ke API terverifikasi server
      const res = await screeningService.submitSymptoms(sessionId, {
        symptomIds: selectedIds,
      });

      router.push(res.resultUrl);
    } catch (err: any) {
      setErrorMessage(err.message || "Gagal memproses skrining. Coba lagi.");
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div style={{ textAlign: "center", padding: "60px 0" }}>
        <p style={{ color: "var(--text-secondary)" }}>Menyiapkan instrumen skrining...</p>
      </div>
    );
  }

  return (
    <div style={{ maxWidth: "760px", margin: "0 auto" }}>
      <div style={{ marginBottom: "28px", textAlign: "center" }}>
        <Badge variant="info" style={{ marginBottom: "12px" }}>
          Langkah 1: Pilih Gejala
        </Badge>
        <h1 style={{ fontSize: "2rem", fontWeight: 800, color: "var(--text-primary)" }}>
          Skrining Mandiri Gejala ISPA
        </h1>
        <p style={{ color: "var(--text-secondary)", marginTop: "8px" }}>
          Pilih keluhan yang sedang Anda alami selama beberapa hari terakhir untuk melihat perkiraan risiko awal.
        </p>
      </div>

      {errorMessage && (
        <div
          style={{
            padding: "14px 18px",
            backgroundColor: "var(--accent-red-light)",
            color: "var(--accent-red)",
            borderRadius: "var(--radius-sm)",
            marginBottom: "20px",
            fontSize: "0.95rem",
          }}
        >
          {errorMessage}
        </div>
      )}

      <div style={{ display: "grid", gap: "12px", marginBottom: "32px" }}>
        {symptoms.map((symptom) => {
          const isSelected = selectedIds.includes(symptom.id);
          return (
            <Card
              key={symptom.id}
              onClick={() => toggleSymptom(symptom.id)}
              style={{
                cursor: "pointer",
                border: isSelected
                  ? "2px solid var(--primary)"
                  : "1px solid var(--border-color)",
                backgroundColor: isSelected ? "var(--primary-light)" : "var(--bg-card)",
                transition: "all 0.15s ease",
              }}
            >
              <div style={{ display: "flex", alignItems: "flex-start", gap: "16px" }}>
                <input
                  type="checkbox"
                  checked={isSelected}
                  onChange={() => {}} // Controlled via card click
                  style={{
                    width: "20px",
                    height: "20px",
                    accentColor: "var(--primary)",
                    marginTop: "2px",
                    cursor: "pointer",
                  }}
                />
                <div>
                  <div
                    style={{
                      fontWeight: 700,
                      fontSize: "1.05rem",
                      color: isSelected ? "var(--primary-dark)" : "var(--text-primary)",
                    }}
                  >
                    {symptom.name}
                  </div>
                  {symptom.description && (
                    <div style={{ fontSize: "0.875rem", color: "var(--text-secondary)", marginTop: "4px" }}>
                      {symptom.description}
                    </div>
                  )}
                  {symptom.category && (
                    <Badge variant="info" style={{ marginTop: "8px", fontSize: "0.75rem" }}>
                      {symptom.category}
                    </Badge>
                  )}
                </div>
              </div>
            </Card>
          );
        })}
      </div>

      <div
        style={{
          position: "sticky",
          bottom: "20px",
          background: "rgba(255, 255, 255, 0.95)",
          backdropFilter: "blur(12px)",
          padding: "16px 24px",
          borderRadius: "var(--radius-md)",
          boxShadow: "var(--shadow-lg)",
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
          border: "1px solid var(--border-color)",
        }}
      >
        <span style={{ fontSize: "0.95rem", color: "var(--text-secondary)" }}>
          <strong>{selectedIds.length}</strong> gejala dipilih
        </span>
        <Button
          onClick={handleSubmit}
          disabled={submitting || selectedIds.length === 0}
          size="lg"
        >
          {submitting ? "Memproses Skrining..." : "Lihat Perkiraan Risiko"}
        </Button>
      </div>
    </div>
  );
};
