export interface HealthFacility {
  id: string;
  name: string;
  type: "puskesmas" | "rumah_sakit" | string | null;
  address: string | null;
  latitude: number | null;
  longitude: number | null;
  distance_km?: number;
}

export interface NearbyFacilitiesResponse {
  latitude: number;
  longitude: number;
  radius_km?: number;
  facilities: HealthFacility[];
}
