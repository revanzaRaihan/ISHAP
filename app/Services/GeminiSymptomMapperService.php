<?php

namespace App\Services;

use App\Models\Symptom;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiSymptomMapperService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
        $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    }

    /**
     * Memeriksa apakah API Key Gemini telah disetel.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Memetakan keluhan subjektif teks bebas pengguna ke array ID gejala master ISHAP.
     *
     * @param string $complaintText Keluhan pengguna dalam bahasa sehari-hari
     * @param array<int, array{id: int|string, name: string, description: ?string, category: ?string}>|null $customSymptoms
     * @return array{
     *   success: bool,
     *   matched_symptom_ids: array<int|string>,
     *   summary: string,
     *   configured: bool
     * }
     */
    public function mapComplaintToSymptoms(string $complaintText, ?array $customSymptoms = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'matched_symptom_ids' => [],
                'summary' => 'API Key Google AI Studio belum dipasang di .env. Anda dapat memilih gejala secara manual di bawah.',
                'configured' => false,
            ];
        }

        $symptoms = $customSymptoms ?? Symptom::all(['id', 'name', 'category', 'description'])->toArray();
        if (empty($symptoms)) {
            return [
                'success' => false,
                'matched_symptom_ids' => [],
                'summary' => 'Daftar master gejala belum tersedia di sistem.',
                'configured' => true,
            ];
        }

        // Susun daftar gejala untuk knowledge context prompt
        $symptomCatalog = [];
        $validIdMap = [];
        foreach ($symptoms as $sym) {
            $id = $sym['id'];
            $validIdMap[(string) $id] = true;
            $symptomCatalog[] = "- ID {$id}: {$sym['name']} (Kategori: {$sym['category']}) — {$sym['description']}";
        }
        $catalogText = implode("\n", $symptomCatalog);

        $systemInstruction = <<<EOT
Anda adalah asisten klinis cerdas untuk aplikasi skrining mandiri ISPA (ISHAP).
Tugas Anda HANYA memetakan keluhan subjektif pengguna (bahasa Indonesia percakapan/sehari-hari) ke daftar gejala klinis yang tersedia.

BATASAN KETAT:
1. DILARANG membuat diagnosis penyakit (misal jangan simpulkan "Anda terkena pneumonia").
2. DILARANG meresepkan obat atau terapi.
3. HANYA pilih ID gejala yang BENAR-BENAR relevan dan terindikasi dari keluhan pengguna.
4. Jangan sertakan ID gejala jika pengguna tidak menyebutkan keluhan yang cocok.
5. Berikan ringkasan empati singkat (1-2 kalimat) yang menjelaskan gejala apa saja yang Anda tandai.

DAFTAR MASTER GEJALA RESMI:
{$catalogText}

Format output WAJIB JSON persis seperti ini:
{
  "matched_symptom_ids": [1, 3, 4],
  "summary": "Berdasarkan keluhan yang Anda ceritakan, kami mengidentifikasi indikasi batuk kering, hidung tersumbat, dan nyeri tenggorokan."
}
EOT;

        try {
            $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => "Keluhan Pengguna:\n\"" . trim($complaintText) . "\""]
                            ]
                        ]
                    ],
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.1,
                    ]
                ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text');
                $parsed = json_decode($content, true);

                if (is_array($parsed) && isset($parsed['matched_symptom_ids'])) {
                    // Validasi ketat: HANYA terima ID yang benar-benar ada di database kita
                    $validIds = [];
                    foreach ($parsed['matched_symptom_ids'] as $id) {
                        $strId = (string) $id;
                        if (isset($validIdMap[$strId])) {
                            // Konversi ke integer jika tipe ID angka
                            $validIds[] = is_numeric($id) ? (int) $id : $strId;
                        }
                    }

                    $summary = $parsed['summary'] ?? 'Gejala telah diidentifikasi dan ditandai otomatis pada checklist di bawah.';

                    return [
                        'success' => true,
                        'matched_symptom_ids' => array_values(array_unique($validIds)),
                        'summary' => $summary,
                        'configured' => true,
                    ];
                }
            }

            Log::warning('Gemini API response format error: ' . $response->body());
            return [
                'success' => false,
                'matched_symptom_ids' => [],
                'summary' => 'Maaf, asisten AI sedang tidak dapat memproses keluhan Anda saat ini. Silakan centang gejala secara manual di bawah.',
                'configured' => true,
            ];

        } catch (\Throwable $e) {
            Log::warning('Gemini Symptom Mapper Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'matched_symptom_ids' => [],
                'summary' => 'Terjadi kendala koneksi ke layanan AI. Silakan centang gejala secara manual pada formulir di bawah.',
                'configured' => true,
            ];
        }
    }
}
