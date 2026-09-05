import { createClient as createBrowserClient } from "@/lib/supabase/client";
import type { OnlineDoctorProfile } from "../types/consultation.types";

/**
 * Service untuk rekomendasi dokter konsultasi online.
 */
export const consultationService = {
  /**
   * Mengambil daftar profil dokter online untuk rujukan konsultasi lanjutan.
   */
  async getDoctorProfiles(): Promise<OnlineDoctorProfile[]> {
    const supabase = createBrowserClient();
    const { data, error } = await supabase
      .from("online_doctor_profiles")
      .select("id, name, platform, profile_url, specialty")
      .order("name", { ascending: true });

    if (error) {
      console.error("Gagal memuat profil dokter:", error.message);
      throw new Error("Gagal memuat rekomendasi dokter konsultasi");
    }

    return data || [];
  },
};
