<?php

namespace App\Http\Controllers;

use App\Services\OverpassOsmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Tampilkan halaman fasilitas kesehatan terdekat dengan integrasi OpenStreetMap Overpass.
     */
    public function index(Request $request, OverpassOsmService $osmService): View|JsonResponse
    {
        $lat = (float) $request->input('lat', -6.1754); // Default: Monas / Jakarta Pusat
        $lon = (float) $request->input('lon', 106.8272);
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
}
