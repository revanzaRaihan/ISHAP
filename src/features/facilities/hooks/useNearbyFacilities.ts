"use client";

import { useState, useEffect, useCallback } from "react";
import { facilityService } from "../services/facilityService";
import type { HealthFacility } from "../types/facility.types";

interface UserLocation {
  lat: number;
  long: number;
  isCustomOrFallback: boolean;
  cityName?: string;
}

// Preset kota besar di Indonesia untuk opsi pemilihan cepat / fallback lokasi
export const LOCATION_PRESETS: { name: string; lat: number; long: number }[] = [
  { name: "Jakarta Pusat", lat: -6.1754, long: 106.8272 },
  { name: "Jakarta Selatan", lat: -6.2615, long: 106.8106 },
  { name: "Jakarta Timur", lat: -6.2250, long: 106.9004 },
  { name: "Surabaya", lat: -7.2575, long: 112.7521 },
  { name: "Bandung", lat: -6.9175, long: 107.6191 },
];

export function useNearbyFacilities(initialLimit: number = 8) {
  const [facilities, setFacilities] = useState<HealthFacility[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [location, setLocation] = useState<UserLocation>({
    lat: -6.1754,
    long: 106.8272,
    isCustomOrFallback: true,
    cityName: "Jakarta Pusat (Default)",
  });
  const [filterType, setFilterType] = useState<string>("all");

  const [radiusKm] = useState<number>(25); // Batas maksimal radius dalam kota

  const fetchFacilities = useCallback(async (lat: number, long: number) => {
    setLoading(true);
    setError(null);
    try {
      const data = await facilityService.getNearbyFacilities(lat, long, initialLimit, radiusKm);
      setFacilities(data.facilities || []);
      if ((data.facilities || []).length === 0) {
        setError(
          `Tidak ada fasilitas kesehatan yang terdaftar dalam radius kota Anda (maks. ${radiusKm} km). Silakan pilih area kota terdaftar di bawah.`
        );
      }
    } catch (err: any) {
      setError(err.message || "Gagal memuat fasilitas kesehatan terdekat");
    } finally {
      setLoading(false);
    }
  }, [initialLimit, radiusKm]);

  // Deteksi lokasi browser (GPS)
  const detectUserLocation = useCallback(() => {
    if (!navigator.geolocation) {
      setError("Browser Anda tidak mendukung deteksi lokasi otomatis");
      fetchFacilities(location.lat, location.long);
      return;
    }

    setLoading(true);
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const newLoc: UserLocation = {
          lat: pos.coords.latitude,
          long: pos.coords.longitude,
          isCustomOrFallback: false,
          cityName: "Lokasi Perangkat Anda (GPS)",
        };
        setLocation(newLoc);
        fetchFacilities(pos.coords.latitude, pos.coords.longitude);
      },
      (geoErr) => {
        console.warn("GPS ditolak/tidak tersedia, beralih ke lokasi default:", geoErr.message);
        setError("Izin akses lokasi GPS dinonaktifkan. Menampilkan faskes di area Jakarta.");
        fetchFacilities(location.lat, location.long);
      },
      { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
    );
  }, [fetchFacilities, location.lat, location.long]);

  // Set lokasi manual berdasarkan preset kota
  const selectPresetLocation = (preset: { name: string; lat: number; long: number }) => {
    const newLoc: UserLocation = {
      lat: preset.lat,
      long: preset.long,
      isCustomOrFallback: true,
      cityName: preset.name,
    };
    setLocation(newLoc);
    fetchFacilities(preset.lat, preset.long);
  };

  useEffect(() => {
    detectUserLocation();
  }, [detectUserLocation]);

  const filteredFacilities = facilities.filter((item) => {
    if (filterType === "all") return true;
    return item.type?.toLowerCase() === filterType.toLowerCase();
  });

  return {
    facilities: filteredFacilities,
    allFacilitiesCount: facilities.length,
    loading,
    error,
    location,
    radiusKm,
    filterType,
    setFilterType,
    detectUserLocation,
    selectPresetLocation,
    refresh: () => fetchFacilities(location.lat, location.long),
  };
}
