import type {
  HealthFacility,
  NearbyFacilitiesResponse,
} from "../types/facility.types";

/**
 * Service untuk rekomendasi fasilitas kesehatan terdekat.
 */
export const facilityService = {
  /**
   * Mengambil rekomendasi fasilitas kesehatan terdekat berdasarkan koordinat pengguna.
   * Diproses melalui server API yang memvalidasi rentang koordinat secara aman.
   */
  async getNearbyFacilities(
    lat: number,
    long: number,
    limit: number = 5
  ): Promise<NearbyFacilitiesResponse> {
    const res = await fetch(
      `/api/facilities/nearby?lat=${encodeURIComponent(lat)}&long=${encodeURIComponent(
        long
      )}&limit=${limit}`
    );

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(
        err.error || "Gagal mendapatkan fasilitas kesehatan terdekat"
      );
    }

    return res.json();
  },
};
