<?php

namespace App\Http\Controllers;

use App\Services\OverpassOsmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Tampilkan halaman fasilitas kesehatan terdekat dengan integrasi OpenStreetMap Overpass.
     */
    public function index(Request $request, OverpassOsmService $osmService): View|JsonResponse
    {
        // 1. Ambil koordinat dari parameter request jika ada (dari GPS browser atau preset kota)
        if ($request->filled('lat') && $request->filled('lon')) {
            $lat = (float) $request->input('lat');
            $lon = (float) $request->input('lon');
        } else {
            // 2. Default otomatis: Deteksi lokasi riil pengguna berdasarkan IP
            $userLocation = $this->detectUserLocation($request);
            $lat = $userLocation['lat'];
            $lon = $userLocation['lon'];
        }

        $radiusKm = (float) $request->input('radius_km', 25.0);

        $facilities = $osmService->findNearbyFacilities($lat, $lon, $radiusKm, 12);

        if ($request->wantsJson()) {
            return response()->json([
                'latitude' => $lat,
                'longitude' => $lon,
                'radius_km' => $radiusKm,
                'facilities' => $facilities,
            ]);
        }

        $cityPresets = [
            ['name' => 'Jakarta Pusat / Monas', 'lat' => -6.1754, 'lon' => 106.8272],
            ['name' => 'Jakarta Selatan / Blok M', 'lat' => -6.2443, 'lon' => 106.7978],
            ['name' => 'Surabaya', 'lat' => -7.2575, 'lon' => 112.7521],
            ['name' => 'Bandung', 'lat' => -6.9175, 'lon' => 107.6191],
            ['name' => 'Medan', 'lat' => 3.5952, 'lon' => 98.6722],
            ['name' => 'Makassar', 'lat' => -5.1477, 'lon' => 119.4327],
        ];

        return view('facilities.index', compact('facilities', 'lat', 'lon', 'radiusKm', 'cityPresets'));
    }

    /**
     * Deteksi otomatis koordinat lokasi pengguna berdasarkan IP client.
     */
    protected function detectUserLocation(Request $request): array
    {
        $ip = $request->ip();

        // Jika request dari localhost / loopback, deteksi via external public IP
        $endpoint = in_array($ip, ['127.0.0.1', '::1', 'localhost'])
            ? 'http://ip-api.com/json/'
            : "http://ip-api.com/json/{$ip}";

        try {
            $response = Http::timeout(3)->get($endpoint);
            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'lat' => (float) $response->json('lat'),
                    'lon' => (float) $response->json('lon'),
                    'city' => $response->json('city'),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback gracefully jika service geolocation offline
        }

        // Fallback default jika tidak ada koneksi / gagal deteksi
        return [
            'lat' => -6.1754,
            'lon' => 106.8272,
            'city' => 'Jakarta Pusat',
        ];
    }
}
