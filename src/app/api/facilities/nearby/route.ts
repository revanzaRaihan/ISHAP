import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";

const nearbyQuerySchema = z.object({
  lat: z.coerce.number().min(-90).max(90, { message: "Latitude tidak valid" }),
  long: z.coerce.number().min(-180).max(180, { message: "Longitude tidak valid" }),
  limit: z.coerce.number().int().min(1).max(30).default(10),
  radius_km: z.coerce.number().min(1).max(50).default(25), // Batas radius kota default 25 km
});

// Haversine formula untuk menghitung jarak dalam kilometer
function calculateHaversineDistance(
  lat1: number,
  lon1: number,
  lat2: number,
  lon2: number
): number {
  const R = 6371; // Radius bumi dalam km
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return Number((R * c).toFixed(2));
}

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const parseResult = nearbyQuerySchema.safeParse({
      lat: searchParams.get("lat"),
      long: searchParams.get("long"),
      limit: searchParams.get("limit") || 10,
      radius_km: searchParams.get("radius_km") || 25,
    });

    if (!parseResult.success) {
      return NextResponse.json(
        {
          error: "Parameter lokasi tidak valid",
          details: parseResult.error.errors.map((e) => e.message),
        },
        { status: 400 }
      );
    }

    const { lat, long, limit, radius_km } = parseResult.data;
    const radiusMeters = Math.round(radius_km * 1000);

    // Overpass QL query untuk mencari rumah sakit, puskesmas, dan klinik di sekitar koordinat user
    const overpassQuery = `[out:json][timeout:25];
(
  node["amenity"~"hospital|clinic"](around:${radiusMeters},${lat},${long});
  way["amenity"~"hospital|clinic"](around:${radiusMeters},${lat},${long});
);
out center 40;`;

    // Fetch ke OpenStreetMap Overpass API dengan timeout 25 detik
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 25000);

    let overpassData: any = null;
    try {
      const osmResponse = await fetch("https://overpass-api.de/api/interpreter", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "User-Agent": "ISHAP-ISPA-Screening-App/1.0",
        },
        body: `data=${encodeURIComponent(overpassQuery)}`,
        signal: controller.signal,
      });

      if (osmResponse.ok) {
        overpassData = await osmResponse.json();
      } else {
        console.warn("Overpass API status error:", osmResponse.status);
      }
    } catch (fetchErr: any) {
      console.warn("Overpass API request warning/timeout:", fetchErr?.message);
    } finally {
      clearTimeout(timeoutId);
    }

    const rawElements = overpassData?.elements || [];

    // Format dan petakan elemen OpenStreetMap menjadi struktur data HealthFacility
    const mappedFacilities = rawElements
      .map((el: any) => {
        const tags = el.tags || {};
        const fLat = el.lat ?? el.center?.lat;
        const fLon = el.lon ?? el.center?.lon;

        if (typeof fLat !== "number" || typeof fLon !== "number") {
          return null;
        }

        // Tentukan nama
        const rawName =
          tags.name ||
          tags["name:id"] ||
          tags["official_name"] ||
          tags.operator ||
          "Fasilitas Kesehatan";

        // Filter nama yang terlalu generik tanpa identitas jelas jika ada
        const nameLower = rawName.toLowerCase();

        // Tentukan kategori faskes
        let type: "puskesmas" | "rumah_sakit" | "klinik" = "klinik";
        if (
          tags.amenity === "hospital" ||
          tags.healthcare === "hospital" ||
          nameLower.includes("rumah sakit") ||
          nameLower.includes("rsud") ||
          nameLower.includes("rsia") ||
          nameLower.includes("rs ")
        ) {
          type = "rumah_sakit";
        } else if (
          nameLower.includes("puskesmas") ||
          tags.healthcare === "centre" ||
          tags["healthcare:speciality"] === "community"
        ) {
          type = "puskesmas";
        }

        // Format alamat
        const addressParts = [
          tags["addr:street"]
            ? `${tags["addr:street"]} ${tags["addr:housenumber"] || ""}`.trim()
            : null,
          tags["addr:subdistrict"] || tags["addr:district"],
          tags["addr:city"] || tags["addr:county"],
        ].filter(Boolean);

        const address =
          addressParts.length > 0
            ? addressParts.join(", ")
            : tags["addr:full"] || "Area sekitar lokasi Anda";

        const distance_km = calculateHaversineDistance(lat, long, fLat, fLon);

        return {
          id: `osm-${el.type}-${el.id}`,
          name: rawName,
          type,
          address,
          latitude: fLat,
          longitude: fLon,
          distance_km,
        };
      })
      .filter((item: any) => item !== null && item.distance_km <= radius_km)
      .sort((a: any, b: any) => a.distance_km - b.distance_km)
      .slice(0, limit);

    return NextResponse.json({
      latitude: lat,
      longitude: long,
      radius_km,
      facilities: mappedFacilities,
    });
  } catch (err: any) {
    console.error("API error /facilities/nearby:", err);
    return NextResponse.json(
      { error: "Terjadi kesalahan saat memproses data faskes terdekat" },
      { status: 500 }
    );
  }
}
