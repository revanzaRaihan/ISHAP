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
use App\Services\AIService;
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
    public function submit(Request $request, ScreeningEngine $engine, AIService $aiService)
    {
        $validated = $request->validate([
            'symptom_ids' => 'required|array|min:1',
        ]);

        $selectedSymptomIds = $request->symptom_ids;
        $symptomNames = Symptom::whereIn('id', $selectedSymptomIds)->pluck('name')->toArray();

        // 1. Hitung skor awal secara matematis (Ambil 3 besar sebagai kandidat)
        $weights = SymptomDiseaseMap::all()->toArray();
        $diseases = Disease::all()->toArray();
        $initialResults = $engine->calculateScreeningRisk($selectedSymptomIds, $weights, $diseases);

        // Ambil detail kandidat (ID dan Nama) untuk dikirim ke AI
        $topCandidates = [];
        foreach (array_slice($initialResults, 0, 3) as $res) {
            $diseaseInfo = Disease::find($res['disease_id']);
            $topCandidates[] = [
                'id' => $res['disease_id'],
                'name' => $diseaseInfo->name,
                'math_score' => $res['confidence_score']
            ];
        }

        // 2. Panggil AI untuk menentukan diagnosa akhir & insight mendalam
        // Kita buat satu fungsi di AIService yang menangani semuanya sekaligus
        $aiAssessment = $aiService->getFinalAssessment($symptomNames, $topCandidates);

        // 3. Simpan Sesi Skrining
        $session = ScreeningSession::create([
            'id' => (string) \Str::uuid(),
            'status' => 'completed',
            'ai_insight' => json_encode($aiAssessment) // Simpan semua insight AI di sini
        ]);

        // 4. Simpan hasil pilihan AI ke tabel ScreeningResults (sebagai hasil utama)
        ScreeningResult::create([
            'session_id' => $session->id,
            'disease_id' => $aiAssessment['selected_disease_id'] ?? $topCandidates[0]['id'],
            'confidence_score' => $aiAssessment['confidence'] ?? $topCandidates[0]['math_score'],
            'reasoning' => $aiAssessment['analisis_mendalam'] ?? 'Berdasarkan kecocokan gejala klinis.',
            'matched_symptoms_count' => count($selectedSymptomIds),
            'total_symptoms_for_disease' => 0 // Opsional
        ]);

        return redirect()->route('screening.result', $session->id);
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
