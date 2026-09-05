import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { createClient } from "@/lib/supabase/server";

const nearbyQuerySchema = z.object({
  lat: z.coerce.number().min(-90).max(90, { message: "Latitude tidak valid" }),
  long: z.coerce.number().min(-180).max(180, { message: "Longitude tidak valid" }),
  limit: z.coerce.number().int().min(1).max(20).default(5),
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
      limit: searchParams.get("limit") || 5,
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

    const { lat, long, limit } = parseResult.data;
    const supabase = await createClient();

    const { data: facilities, error } = await supabase
      .from("health_facilities")
      .select("id, name, type, address, latitude, longitude");

    if (error) {
      console.error("Gagal mengambil fasilitas kesehatan:", error);
      return NextResponse.json(
        { error: "Gagal memuat data fasilitas kesehatan" },
        { status: 500 }
      );
    }

    // Hitung jarak dan urutkan faskes terdekat
    const nearbyFacilities = (facilities || [])
      .filter((f) => f.latitude !== null && f.longitude !== null)
      .map((f) => ({
        ...f,
        distance_km: calculateHaversineDistance(
          lat,
          long,
          f.latitude!,
          f.longitude!
        ),
      }))
      .sort((a, b) => a.distance_km - b.distance_km)
      .slice(0, limit);

    return NextResponse.json({
      latitude: lat,
      longitude: long,
      facilities: nearbyFacilities,
    });
  } catch (err: any) {
    console.error("API error /facilities/nearby:", err);
    return NextResponse.json(
      { error: "Terjadi kesalahan internal server" },
      { status: 500 }
    );
  }
}
