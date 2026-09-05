<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIService
{
    protected $apiKey;
    protected $baseUrl;
    protected $workspace;

    public function __construct()
    {
        $this->apiKey = config('services.anything_llm.key');
        $this->baseUrl = rtrim(config('services.anything_llm.url'), '/');
        $this->workspace = config('services.anything_llm.workspace');
    }

    /**
     * PROSES 1: Upload File Fisik ke AnythingLLM agar muncul di Document Management (RAG)
     */
    public function uploadFileToAnythingLLM($filePath)
    {
        try {
            $url = rtrim($this->baseUrl, '/');
            $fileName = basename($filePath);
            // Jika file dari Laravel temp, namanya suka phpXXX.tmp, kita beri nama asli
            $finalName = Str::random(10) . '.pdf';

            // 1. Upload ke Library
            $uploadResponse = Http::withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->attach('file', file_get_contents($filePath), $finalName) // Gunakan file_get_contents agar lebih stabil
                ->post("{$url}/document/upload");

            if (!$uploadResponse->successful()) {
                \Log::error("AnythingLLM Upload Error: " . $uploadResponse->body());
                return false;
            }

            $docData = $uploadResponse->json();

            // Pastikan path dokumen ada
            if (!isset($docData['documents'][0]['location'])) {
                \Log::error("AnythingLLM Response Format Salah: " . json_encode($docData));
                return false;
            }

            $docLocation = $docData['documents'][0]['location'];

            // 2. Masukkan ke Workspace (Pakai slug huruf kecil)
            $workspaceSlug = strtolower($this->workspace);

            $moveResponse = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$url}/workspace/{$workspaceSlug}/update-embeddings", [
                        'adds' => [$docLocation]
                    ]);

            if (!$moveResponse->successful()) {
                \Log::error("AnythingLLM Embedding Error: " . $moveResponse->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error("Exception di AIService: " . $e->getMessage());
            return false;
        }
    }

    /**
     * PROSES 2: Ekstrak data dari teks PDF untuk disimpan ke Database Lokal
     */
    public function extractMetadataFromText($text)
    {
        $cleanText = $this->cleanMedicalText($text);
        $truncatedText = mb_strimwidth($cleanText, 0, 40000, "... [Dokumen Terpotong]");

        $prompt = "Tugas: Ekstrak INFORMASI KLINIS SPESIFIK dari dokumen pernapasan.

    PERINGATAN KERAS:
    1. JANGAN gunakan nama umum seperti 'Gangguan Saluran Pernapasan' atau 'Penyakit Paru'.
    2. CARI nama penyakit/diagnosa spesifik yang menjadi topik utama dokumen (Lihat Judul atau Abstrak).
    3. Jika dokumen adalah penelitian tentang 'ISPA pada Mahasiswa', maka disease_name harus 'ISPA (Infeksi Saluran Pernapasan Akut)'.
    4. Deskripsi harus menjelaskan MENGAPA penyakit ini terjadi berdasarkan dokumen.
    5. WEIGHT gejala harus antara 0.1 hingga 1.0, dengan 1.0 untuk gejala yang paling khas.
    6. JAWAB DALAM BAHASA INDONESIA & JSON MURNI.

    STRUKTUR:
    {
      \"disease_name\": \"[NAMA SPESIFIK DARI TEKS]\",
      \"severity\": \"Ringan/Sedang/Berat\",
      \"description\": \"...\",
      \"pathogenesis_overview\": \"...\",
      \"recovery_tips\": [],
      \"red_flags\": [],
      \"symptoms\": [
         {\"name\": \"Nama Gejala Medis\", \"category\": \"...\", \"weight\": (0.1-1.0)}
      ]
    }

    Teks untuk dianalisis:
    " . $truncatedText;

        try {
            $response = Http::withoutVerifying()
                ->timeout(600) // Naikkan ke 10 menit (32k token butuh waktu prefilling lama)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$this->baseUrl}/workspace/{$this->workspace}/chat", [
                        'message' => $prompt,
                        'mode' => 'chat', // Tetap 'chat' untuk ekstraksi data dari teks yang kita kirim langsung
                    ]);

            if (!$response->successful()) {
                \Log::error("AnythingLLM Error: " . $response->body());
                return null;
            }

            $rawContent = $response->json()['textResponse'] ?? '';

            // --- PEMBERSIHAN JSON ---
            $firstBracket = strpos($rawContent, '{');
            $lastBracket = strrpos($rawContent, '}');

            if ($firstBracket !== false && $lastBracket !== false) {
                $jsonString = substr($rawContent, $firstBracket, ($lastBracket - $firstBracket) + 1);
            } else {
                $jsonString = $rawContent;
            }

            $jsonString = str_replace(['```json', '```'], '', $jsonString);
            $decodedData = json_decode(trim($jsonString), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error("Gagal Decode JSON: " . json_last_error_msg() . " | Respon: " . $rawContent);
                return null;
            }

            return $decodedData;

        } catch (\Exception $e) {
            \Log::error("Exception: " . $e->getMessage());
            return null;
        }
    }

    // Fungsi pembantu untuk membuang "sampah" token
    private function cleanMedicalText($text)
    {
        $text = preg_replace('/https?:\/\/\S+/', '', $text); // Hapus URL
        $text = preg_replace('/\S+@\S+\.\S+/', '', $text); // Hapus Email
        $text = preg_replace('/DOI:\s\S+/', '', $text);    // Hapus DOI
        return preg_replace('/\s+/', ' ', $text);         // Hapus spasi berlebih
    }

    /**
     * PROSES 3: Analisis Gejala untuk User (Hasil Screening)
     */
    public function analyzeSymptoms($symptoms, $topDisease)
    {
        $prompt = "Analisis gejala: " . implode(', ', $symptoms) . ". Penyakit: " . $topDisease . ". 
        Jawab dalam JSON format: {
            \"analisis_mendalam\": \"...\",
            \"panduan_mandiri\": [\"...\"],
            \"tanda_bahaya\": [\"...\"],
            \"kemungkinan_lain\": \"...\"
        }";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/workspace/{$this->workspace}/chat", [
                        'message' => $prompt,
                        'mode' => 'query', // 'query' akan menggunakan data dari PDF yang diupload tadi
                    ]);

            return $response->json()['textResponse'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFinalAssessment($symptoms, $candidates)
    {
        $candidateList = json_encode($candidates);
        $prompt = "Tugas: Diagnosa Pasien berdasarkan Dokumen Medis di Library.
    
    Gejala Pasien: " . implode(', ', $symptoms) . "
    Kandidat Penyakit dari database: " . $candidateList . "

    Instruksi:
    1. Cari di dokumen/PDF yang relevan di library Anda mengenai gejala di atas.
    2. Bandingkan temuan di dokumen dengan kandidat penyakit.
    3. Pilih satu penyakit yang paling akurat menurut dokumen.
    4. Berikan analisis mendalam MENGAPA dokumen tersebut mendukung pilihan Anda.
    
    Jawab dalam JSON Bahasa Indonesia...";


        try {
            $response = Http::withoutVerifying()
                ->timeout(120)
                ->post("{$this->baseUrl}/workspace/{$this->workspace}/chat", [
                    'message' => $prompt,
                    'mode' => 'query', // Gunakan RAG agar AI membaca PDF yang diupload
                ]);

            $raw = $response->json()['textResponse'] ?? '';

            // Pembersihan JSON (seperti yang kita bahas sebelumnya)
            if (preg_match('/\{.*\}/s', $raw, $matches)) {
                return json_decode($matches[0], true);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error("AI Diagnosis Error: " . $e->getMessage());
            return null;
        }
    }
}