<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan landing page ISHAP dengan edukasi ISPA dan widget AQI.
     */
    public function index(): View
    {
        $symptomsCount = Symptom::count();
        $diseases = Disease::all();

        // Ambil data Indeks Kualitas Udara (AQI) aktual Jakarta dari Open-Meteo Air Quality API (gratis, tanpa API key)
        $aqiData = $this->getAirQualityData();

        return view('home', compact('symptomsCount', 'diseases', 'aqiData'));
    }

    /**
     * Ambil data kualitas udara (PM2.5, PM10, AQI).
     */
    private function getAirQualityData(): array
    {
        try {
            // Koordinat Jakarta Pusat (-6.2088, 106.8456)
            $response = Http::timeout(4)->get('https://air-quality-api.open-meteo.com/v1/air-quality', [
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'current' => 'pm10,pm2_5,european_aqi,us_aqi',
            ]);

            if ($response->successful()) {
                $current = $response->json('current') ?? [];
                $usAqi = (int) ($current['us_aqi'] ?? 85);
                $pm25 = round((float) ($current['pm2_5'] ?? 28.5), 1);
                $pm10 = round((float) ($current['pm10'] ?? 42.0), 1);

                return [
                    'location' => 'DKI Jakarta & Sekitarnya',
                    'aqi' => $usAqi,
                    'pm25' => $pm25,
                    'pm10' => $pm10,
                    'status' => $this->getAqiStatus($usAqi),
                    'color' => $this->getAqiColor($usAqi),
                    'recommendation' => $this->getAqiRecommendation($usAqi),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback gracefully
        }

        return [
            'location' => 'DKI Jakarta & Sekitarnya (Estimasi)',
            'aqi' => 92,
            'pm25' => 32.4,
            'pm10' => 48.1,
            'status' => 'Sedang (Moderate)',
            'color' => 'amber',
            'recommendation' => 'Kelompok sensitif (asma, balita, lansia) disarankan mengenakan masker medis saat beraktivitas di luar ruangan.',
        ];
    }

    private function getAqiStatus(int $aqi): string
    {
        return match (true) {
            $aqi <= 50 => 'Baik (Good)',
            $aqi <= 100 => 'Sedang (Moderate)',
            $aqi <= 150 => 'Tidak Sehat bagi Kelompok Sensitif',
            $aqi <= 200 => 'Tidak Sehat (Unhealthy)',
            default => 'Sangat Tidak Sehat (Hazardous)',
        };
    }

    private function getAqiColor(int $aqi): string
    {
        return match (true) {
            $aqi <= 50 => 'emerald',
            $aqi <= 100 => 'amber',
            $aqi <= 150 => 'orange',
            $aqi <= 200 => 'rose',
            default => 'purple',
        };
    }

    private function getAqiRecommendation(int $aqi): string
    {
        return match (true) {
            $aqi <= 50 => 'Kualitas udara sangat baik untuk berolahraga dan ventilasi rumah terbuka.',
            $aqi <= 100 => 'Kelompok rentan respirasi dianjurkan menggunakan masker medis di jalan raya.',
            $aqi <= 150 => 'Gunakan masker N95/KF94 jika terpapar polusi luar dan nyalakan air purifier.',
            default => 'Hindari aktivitas fisik berat di luar ruangan dan tutup ventilasi jendela saat siang hari.',
        };
    }
}
