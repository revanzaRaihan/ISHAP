<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\OnlineDoctorProfile;
use App\Models\ScreeningResult;
use App\Models\ScreeningSession;
use App\Models\SessionSymptom;
use App\Models\Symptom;
use App\Models\SymptomDiseaseMap;
use App\Services\OverpassOsmService;
use App\Services\ScreeningEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScreeningController extends Controller
{
    /**
     * Tampilkan formulir checklist gejala interaktif untuk skrining mandiri ISPA.
     */
    public function index(\App\Services\SupabaseService $supabase): View
    {
        // Self-healing: jika tabel gejala kosong, tarik dari Supabase atau seeder secara otomatis
        if (Symptom::count() === 0) {
            $supabase->syncRemoteToLocal();
            if (Symptom::count() === 0) {
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
        }

        $symptomsByCategory = Symptom::all()->groupBy('category');

        return view('screening.index', compact('symptomsByCategory'));
    }

    /**
     * Ekstraksi keluhan subjektif teks bebas ke daftar ID gejala menggunakan Gemini LLM.
     */
    public function extractSymptoms(Request $request, \App\Services\GeminiSymptomMapperService $gemini): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'complaint' => 'required|string|min:4|max:500',
        ], [
            'complaint.required' => 'Silakan tuliskan keluhan yang Anda rasakan.',
            'complaint.min' => 'Keluhan minimal 4 karakter.',
            'complaint.max' => 'Keluhan maksimal 500 karakter.',
        ]);

        $result = $gemini->mapComplaintToSymptoms($validated['complaint']);

        return response()->json($result);
    }

    /**
     * Proses submisi gejala dan kalkulasi skor risiko via pure function ScreeningEngine.
     */
    public function submit(Request $request, ScreeningEngine $engine, \App\Services\SupabaseService $supabase): RedirectResponse
    {
        $validated = $request->validate([
            'symptom_ids' => 'required|array|min:1',
            'symptom_ids.*' => 'required|string|exists:symptoms,id',
        ], [
            'symptom_ids.required' => 'Pilih minimal satu gejala yang Anda rasakan untuk memulai skrining.',
            'symptom_ids.min' => 'Pilih minimal satu gejala yang Anda rasakan.',
        ]);

        $selectedSymptomIds = $validated['symptom_ids'];

        // 1. Buat sesi skrining baru
        $session = ScreeningSession::create([
            'id' => (string) Str::uuid(),
            'status' => 'completed',
        ]);

        // 2. Catat gejala yang dipilih
        foreach ($selectedSymptomIds as $symptomId) {
            SessionSymptom::create([
                'session_id' => $session->id,
                'symptom_id' => $symptomId,
            ]);
        }

        // 3. Ambil data master bobot dan penyakit untuk kalkulasi
        $weights = SymptomDiseaseMap::all(['symptom_id', 'disease_id', 'weight'])->toArray();
        $diseases = Disease::all(['id', 'name', 'severity_level', 'description'])->toArray();

        // 4. Hitung perkiraan risiko menggunakan pure function engine
        $riskAssessments = $engine->calculateScreeningRisk($selectedSymptomIds, $weights, $diseases);

        // 5. Simpan hasil penilaian risiko
        foreach ($riskAssessments as $assessment) {
            ScreeningResult::create([
                'session_id' => $session->id,
                'disease_id' => $assessment['disease_id'],
                'confidence_score' => $assessment['confidence_score'],
                'matched_symptoms_count' => $assessment['matched_symptoms_count'],
                'total_symptoms_for_disease' => $assessment['total_symptoms_for_disease'],
                'reasoning' => $assessment['reasoning'],
            ]);
        }

        // 6. Sinkronisasi sesi skrining ke Supabase (Database cloud)
        $supabase->saveScreeningSession($session->id, $selectedSymptomIds, $riskAssessments);

        return redirect()->route('screening.result', ['sessionId' => $session->id]);
    }

    /**
     * Tampilkan hasil skrining mandiri (Primary Assessment), patogenesis, pemulihan, faskes terdekat, dan dokter mitra.
     */
    public function result(string $sessionId, Request $request, OverpassOsmService $osmService): View
    {
        $session = ScreeningSession::with(['symptoms'])->findOrFail($sessionId);

        $results = ScreeningResult::with('disease')
            ->where('session_id', $sessionId)
            ->orderByDesc('confidence_score')
            ->get();

        $topResult = $results->first();
        $secondaryResults = $results->slice(1);
        // 1. Tentukan Kategori Dokter Spesialis Halodoc (Deep Link dinamis sesuai organ pernapasan)
        $diseaseNameLower = strtolower($topResult->disease->name ?? '');
        $isUpperAirway = str_contains($diseaseNameLower, 'faringitis') 
            || str_contains($diseaseNameLower, 'tenggorokan') 
            || str_contains($diseaseNameLower, 'cold') 
            || str_contains($diseaseNameLower, 'pilek') 
            || str_contains($diseaseNameLower, 'rhinitis');

        $specialistCategory = $isUpperAirway ? [
            'title' => 'Dokter Spesialis THT',
            'specialty' => 'Telinga, Hidung & Tenggorokan',
            'url' => 'https://www.halodoc.com/tanya-dokter/kategori/spesialis-tht',
            'alternate_title' => 'Spesialis Paru',
            'alternate_url' => 'https://www.halodoc.com/tanya-dokter/kategori/kesehatan-paru',
            'image' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=300&auto=format&fit=crop&q=80',
            'description' => 'Konsultasi online via chat & video call untuk keluhan tenggorokan, amandel, dan hidung di Halodoc.',
        ] : [
            'title' => 'Dokter Spesialis Paru',
            'specialty' => 'Pulmonologi & Respirasi',
            'url' => 'https://www.halodoc.com/tanya-dokter/kategori/kesehatan-paru',
            'alternate_title' => 'Spesialis THT',
            'alternate_url' => 'https://www.halodoc.com/tanya-dokter/kategori/spesialis-tht',
            'image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=300&auto=format&fit=crop&q=80',
            'description' => 'Konsultasi online via chat & video call untuk batuk persisten, bronkus, sesak, dan paru-paru di Halodoc.',
        ];

        // 2. Ambil 1 Faskes Terdekat berdasarkan koordinat pengguna / IP
        $userLoc = $this->detectUserLocation($request);
        $nearbyFacilities = $osmService->findNearbyFacilities($userLoc['lat'], $userLoc['lon'], 25.0, 1);
        $nearestFacility = $nearbyFacilities[0] ?? null;

        return view('screening.result', compact('session', 'topResult', 'secondaryResults', 'specialistCategory', 'nearestFacility', 'userLoc'));
    }


    /**
     * Deteksi koordinat lokasi pengguna berdasarkan IP client atau fallback.
     */
    protected function detectUserLocation(Request $request): array
    {
        $ip = $request->ip();

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
            // Fallback jika layanan IP lookup offline
        }

        return [
            'lat' => -6.1754,
            'lon' => 106.8272,
            'city' => 'Jakarta Pusat',
        ];
    }
}
