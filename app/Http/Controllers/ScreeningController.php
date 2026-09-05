<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\OnlineDoctorProfile;
use App\Models\ScreeningResult;
use App\Models\ScreeningSession;
use App\Models\SessionSymptom;
use App\Models\Symptom;
use App\Models\SymptomDiseaseMap;
use App\Services\ScreeningEngine;
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Tampilkan hasil skrining mandiri (Primary Assessment), patogenesis, pemulihan, dan dokter mitra.
     */
    public function result(string $sessionId): View
    {
        $session = ScreeningSession::with(['symptoms'])->findOrFail($sessionId);

        $results = ScreeningResult::with('disease')
            ->where('session_id', $sessionId)
            ->orderByDesc('confidence_score')
            ->get();

        $topResult = $results->first();
        $secondaryResults = $results->slice(1);
        $doctors = OnlineDoctorProfile::all();

        return view('screening.result', compact('session', 'topResult', 'secondaryResults', 'doctors'));
    }
}
