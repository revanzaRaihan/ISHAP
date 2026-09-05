<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AqiController extends Controller
{
    public function getAqiByCoords(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $token = config('services.waqi.token');
        $lat = $request->lat;
        $lng = $request->lng;

        // Memanggil API WAQI berdasarkan Geo-Feed (geo:lat;lng)
        $response = Http::get("https://api.waqi.info/feed/geo:{$lat};{$lng}/", [
            'token' => $token,
        ]);

        if ($response->successful() && $response->json('status') === 'ok') {
            return response()->json([
                'success' => true,
                'data' => $response->json('data'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data AQI',
        ], 500);
    }
}
