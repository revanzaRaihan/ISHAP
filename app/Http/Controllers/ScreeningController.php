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
