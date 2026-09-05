<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OverpassOsmService
{
    /**
     * Hitung jarak dua koordinat menggunakan formula Haversine (km).
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Cari faskes terdekat (Rumah Sakit, Puskesmas, Klinik) dari OpenStreetMap Overpass API.
     *
     * @return array<array{
     *   id: string,
     *   name: string,
     *   type: string,
     *   address: string,
     *   distance_km: float,
     *   lat: float,
     *   lon: float,
     *   google_maps_url: string
     * }>
     */
    public function findNearbyFacilities(float $lat, float $lon, float $radiusKm = 25.0, int $limit = 10): array
    {
        $radiusMeters = (int) round($radiusKm * 1000);

        $overpassQuery = "[out:json][timeout:15];
(
  node[\"amenity\"~\"hospital|clinic\"](around:{$radiusMeters},{$lat},{$lon});
  way[\"amenity\"~\"hospital|clinic\"](around:{$radiusMeters},{$lat},{$lon});
);
out center 40;";

        $facilities = [];

        try {
            $response = Http::asForm()
                ->timeout(12)
                ->withHeaders([
                    'User-Agent' => 'ISHAP-Laravel-ISPA-Screening/1.0',
                ])
                ->post('https://overpass-api.de/api/interpreter', [
                    'data' => $overpassQuery,
                ]);

            if ($response->successful()) {
                $elements = $response->json('elements') ?? [];

                foreach ($elements as $el) {
                    $tags = $el['tags'] ?? [];
                    $fLat = $el['lat'] ?? ($el['center']['lat'] ?? null);
                    $fLon = $el['lon'] ?? ($el['center']['lon'] ?? null);

                    if ($fLat === null || $fLon === null) {
                        continue;
                    }

                    $name = $tags['name'] ?? ($tags['operator'] ?? null);
                    if (!$name) {
                        $amenityType = $tags['amenity'] ?? 'health_facility';
                        $name = $amenityType === 'hospital' ? 'Rumah Sakit (OSM)' : 'Klinik / Puskesmas (OSM)';
                    }

                    $amenity = $tags['amenity'] ?? 'clinic';
                    $typeLabel = match ($amenity) {
                        'hospital' => 'Rumah Sakit',
                        'clinic' => 'Klinik / Faskes Tingkat 1',
                        default => 'Fasilitas Kesehatan',
                    };

                    $addressParts = array_filter([
                        $tags['addr:street'] ?? null,
                        $tags['addr:housenumber'] ?? null,
                        $tags['addr:city'] ?? null,
                    ]);

                    $address = !empty($addressParts)
                        ? implode(', ', $addressParts)
                        : ($tags['address'] ?? 'Dekat lokasi koordinat');

                    $distance = $this->calculateDistance($lat, $lon, (float) $fLat, (float) $fLon);

                    $facilities[] = [
                        'id' => 'osm-' . ($el['id'] ?? uniqid()),
                        'name' => $name,
                        'type' => $typeLabel,
                        'address' => $address,
                        'distance_km' => $distance,
                        'lat' => (float) $fLat,
                        'lon' => (float) $fLon,
                        'phone' => $tags['phone'] ?? ($tags['contact:phone'] ?? null),
                        'emergency' => ($tags['emergency'] ?? 'no') === 'yes',
                        'google_maps_url' => "https://www.google.com/maps/dir/?api=1&destination={$fLat},{$fLon}",
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Overpass API query failed: ' . $e->getMessage());
        }

        // Jika API OSM kosong atau gagal, sediakan fasilitas rujukan utama
        if (empty($facilities)) {
            $fallbacks = [
                [
                    'name' => 'RSUP Persahabatan (Pusat Respirasi Nasional)',
                    'type' => 'Rumah Sakit Rujukan Paru',
                    'address' => 'Jl. Persahabatan Raya No.1, Rawamangun, Jakarta Timur',
                    'lat' => -6.1956,
                    'lon' => 106.8837,
                    'phone' => '(021) 4891708',
                    'emergency' => true,
                ],
                [
                    'name' => 'RSUD Tarakan (Poli Paru & IGD 24 Jam)',
                    'type' => 'Rumah Sakit Umum Daerah',
                    'address' => 'Jl. Kyai Caringin No.7, Cideng, Gambir, Jakarta Pusat',
                    'lat' => -6.1712,
                    'lon' => 106.8123,
                    'phone' => '(021) 3842952',
                    'emergency' => true,
                ],
                [
                    'name' => 'Puskesmas Kecamatan Gambir',
                    'type' => 'Fasilitas Kesehatan Tingkat Pertama (FKTP)',
                    'address' => 'Jl. Petojo Enklek No.23, Petojo Selatan, Gambir',
                    'lat' => -6.1745,
                    'lon' => 106.8180,
                    'phone' => '(021) 3844827',
                    'emergency' => false,
                ],
            ];

            foreach ($fallbacks as $idx => $fb) {
                $dist = $this->calculateDistance($lat, $lon, $fb['lat'], $fb['lon']);
                $facilities[] = [
                    'id' => 'ref-' . ($idx + 1),
                    'name' => $fb['name'],
                    'type' => $fb['type'],
                    'address' => $fb['address'],
                    'distance_km' => $dist,
                    'lat' => $fb['lat'],
                    'lon' => $fb['lon'],
                    'phone' => $fb['phone'],
                    'emergency' => $fb['emergency'],
                    'google_maps_url' => "https://www.google.com/maps/dir/?api=1&destination={$fb['lat']},{$fb['lon']}",
                ];
            }
        }

        // Urutkan berdasarkan jarak terdekat
        usort($facilities, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);

        return array_slice($facilities, 0, $limit);
    }
}
