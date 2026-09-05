import { createClient as createBrowserClient } from "@/lib/supabase/client";
import type {
  Symptom,
  CreateSessionResponse,
  ScreeningResultResponse,
  SubmitSymptomsPayload,
} from "../types/screening.types";

/**
 * Service untuk fitur skrining ISPA.
 * Mengisolasi semua akses data dan pemanggilan API backend terverifikasi.
 */
export const screeningService = {
  /**
   * Mengambil daftar gejala aktif dari database untuk checklist skrining.
   * Aman dibaca dari client karena merupakan data referensi publik.
   */
  async getSymptoms(): Promise<Symptom[]> {
    const supabase = createBrowserClient();
    const { data, error } = await supabase
      .from("symptoms")
      .select("id, name, category, description")
      .order("name", { ascending: true });

    if (error) {
      console.error("Gagal memuat gejala:", error.message);
      throw new Error("Gagal memuat data gejala skrining");
    }

    return data || [];
  },

  /**
   * Membuat sesi skrining baru melalui API route yang divalidasi server.
   * Mencegah injeksi atau manipulasi status sesi dari browser.
   */
  async createSession(): Promise<CreateSessionResponse> {
    const res = await fetch("/api/screening-sessions", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
    });

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.error || "Gagal membuat sesi skrining");
    }

    return res.json();
  },

  /**
   * Mengirimkan gejala yang dipilih untuk diproses scoring di server.
   * Server memvalidasi UUID, menghitung skor risiko, dan menyimpan hasil dengan aman.
   */
  async submitSymptoms(
    sessionId: string,
    payload: SubmitSymptomsPayload
  ): Promise<{ success: boolean; resultUrl: string }> {
    const res = await fetch(`/api/screening-sessions/${sessionId}/symptoms`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.error || "Gagal memproses gejala skrining");
    }

    return res.json();
  },

  /**
   * Mengambil hasil skrining terverifikasi dari server.
   */
  async getScreeningResult(sessionId: string): Promise<ScreeningResultResponse> {
    const res = await fetch(`/api/screening-sessions/${sessionId}/result`);

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.error || "Gagal mengambil hasil skrining");
    }

    return res.json();
  },
};
