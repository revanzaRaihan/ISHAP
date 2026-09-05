<?php

namespace App\Services;

use App\Models\Disease;
use App\Models\OnlineDoctorProfile;
use App\Models\Symptom;
use App\Models\SymptomDiseaseMap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected ?string $url;
    protected ?string $anonKey;
    protected ?string $serviceRoleKey;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->anonKey = config('services.supabase.anon_key');
        $this->serviceRoleKey = config('services.supabase.service_role_key');
    }

    /**
     * Memeriksa apakah kredensial Supabase sudah terisi.
     */
    public function isConfigured(): bool
    {
        return !empty($this->url) && (!empty($this->serviceRoleKey) || !empty($this->anonKey));
    }

    /**
     * HTTP client terautentikasi ke Supabase REST API.
     */
    protected function client(bool $useServiceRole = true)
    {
        $key = ($useServiceRole && $this->serviceRoleKey) ? $this->serviceRoleKey : $this->anonKey;

        return Http::baseUrl(rtrim($this->url, '/') . '/rest/v1')
            ->timeout(10)
            ->withHeaders([
                'apikey' => $key,
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
            ]);
    }

    /**
     * Uji koneksi ke database Supabase.
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'status' => 'error',
                'message' => 'Kredensial Supabase (SUPABASE_URL / KEYS) belum dikonfigurasi di .env',
            ];
        }

        try {
            $start = microtime(true);
            $response = $this->client()->get('/symptoms', [
                'select' => 'id',
                'limit' => 1,
            ]);
            $latency = round((microtime(true) - $start) * 1000, 2);

            if ($response->successful()) {
                return [
                    'status' => 'connected',
                    'url' => $this->url,
                    'latency_ms' => $latency,
                    'message' => 'Berhasil terhubung ke Supabase REST API (' . $latency . ' ms)',
                ];
            }

            return [
                'status' => 'error',
                'url' => $this->url,
                'http_code' => $response->status(),
                'message' => 'Gagal terhubung: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'exception',
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil seluruh master gejala dari Supabase.
     */
    public function getSymptoms(): array
    {
        try {
            $response = $this->client()->get('/symptoms', [
                'select' => '*',
                'order' => 'category.asc,name.asc',
            ]);

            return $response->successful() ? ($response->json() ?? []) : [];
        } catch (\Throwable $e) {
            Log::warning('Supabase getSymptoms error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil seluruh master penyakit dari Supabase.
     */
    public function getDiseases(): array
    {
        try {
            $response = $this->client()->get('/diseases', [
                'select' => '*',
            ]);

            return $response->successful() ? ($response->json() ?? []) : [];
        } catch (\Throwable $e) {
            Log::warning('Supabase getDiseases error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil bobot pemetaan gejala-penyakit dari Supabase.
     */
    public function getSymptomDiseaseMaps(): array
    {
        try {
            $response = $this->client()->get('/symptom_disease_map', [
                'select' => 'symptom_id,disease_id,weight',
            ]);

            return $response->successful() ? ($response->json() ?? []) : [];
        } catch (\Throwable $e) {
            Log::warning('Supabase getSymptomDiseaseMaps error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil daftar dokter online dari Supabase.
     */
    public function getDoctors(): array
    {
        try {
            $response = $this->client()->get('/online_doctor_profiles', [
                'select' => '*',
            ]);

            return $response->successful() ? ($response->json() ?? []) : [];
        } catch (\Throwable $e) {
            Log::warning('Supabase getDoctors error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Simpan sesi skrining langsung ke tabel Supabase (screening_sessions, session_symptoms, screening_results).
     */
    public function saveScreeningSession(string $sessionId, array $symptomIds, array $riskAssessments): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            // 1. Insert Sesi ke Supabase
            $this->client()->post('/screening_sessions', [
                'id' => $sessionId,
                'status' => 'completed',
            ]);

            // 2. Insert Gejala Terpilih ke Supabase
            $sessionSymptoms = array_map(fn ($symId) => [
                'session_id' => $sessionId,
                'symptom_id' => $symId,
            ], $symptomIds);

            if (!empty($sessionSymptoms)) {
                $this->client()->post('/session_symptoms', $sessionSymptoms);
            }

            // 3. Insert Hasil Skrining ke Supabase
            $results = array_map(fn ($res) => [
                'session_id' => $sessionId,
                'disease_id' => $res['disease_id'],
                'confidence_score' => $res['confidence_score'],
                'reasoning' => $res['reasoning'],
            ], $riskAssessments);

            if (!empty($results)) {
                $this->client()->post('/screening_results', $results);
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Supabase saveScreeningSession error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sinkronisasi data master dari Supabase ke database lokal.
     */
    public function syncRemoteToLocal(): array
    {
        $stats = [
            'symptoms' => 0,
            'diseases' => 0,
            'maps' => 0,
            'doctors' => 0,
        ];

        // 1. Sync Symptoms
        $symptoms = $this->getSymptoms();
        foreach ($symptoms as $sym) {
            Symptom::updateOrCreate(['id' => $sym['id']], [
                'name' => $sym['name'],
                'category' => $sym['category'] ?? null,
                'description' => $sym['description'] ?? null,
            ]);
            $stats['symptoms']++;
        }

        // 2. Sync Diseases
        $diseases = $this->getDiseases();
        foreach ($diseases as $dis) {
            Disease::updateOrCreate(['id' => $dis['id']], [
                'name' => $dis['name'],
                'severity_level' => $dis['severity_level'] ?? null,
                'description' => $dis['description'] ?? null,
            ]);
            $stats['diseases']++;
        }

        // 3. Sync Maps
        $maps = $this->getSymptomDiseaseMaps();
        foreach ($maps as $map) {
            SymptomDiseaseMap::updateOrCreate(
                ['symptom_id' => $map['symptom_id'], 'disease_id' => $map['disease_id']],
                ['weight' => (float) $map['weight']]
            );
            $stats['maps']++;
        }

        // 4. Sync Doctors
        $doctors = $this->getDoctors();
        foreach ($doctors as $doc) {
            OnlineDoctorProfile::updateOrCreate(['id' => $doc['id']], [
                'name' => $doc['name'],
                'platform' => $doc['platform'] ?? 'Halodoc',
                'profile_url' => $doc['profile_url'] ?? 'https://www.halodoc.com',
                'specialty' => $doc['specialty'] ?? 'Spesialis Paru',
            ]);
            $stats['doctors']++;
        }

        return $stats;
    }
}
