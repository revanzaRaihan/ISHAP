<?php

namespace Tests\Unit;

use App\Services\GeminiSymptomMapperService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiSymptomMapperTest extends TestCase
{
    public function test_mapper_filters_out_invalid_symptom_ids(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        // 999 is not in custom symptoms catalog
                                        'matched_symptom_ids' => [1, 999, 3],
                                        'summary' => 'Ditemukan gejala relevan.'
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        config(['services.gemini.api_key' => 'test-key']);

        $service = new GeminiSymptomMapperService();
        $customSymptoms = [
            ['id' => 1, 'name' => 'Batuk Kering', 'category' => 'Saluran Napas Atas', 'description' => 'Batuk tanpa lendir'],
            ['id' => 2, 'name' => 'Batuk Berdahak', 'category' => 'Saluran Napas Bawah', 'description' => 'Batuk berlendir'],
            ['id' => 3, 'name' => 'Hidung Tersumbat', 'category' => 'Saluran Napas Atas', 'description' => 'Hidung mampet'],
        ];

        $result = $service->mapComplaintToSymptoms('batuk kering dan hidung mampet', $customSymptoms);

        $this->assertTrue($result['success']);
        $this->assertContains(1, $result['matched_symptom_ids']);
        $this->assertContains(3, $result['matched_symptom_ids']);
        $this->assertNotContains(999, $result['matched_symptom_ids']);
    }

    public function test_mapper_returns_unconfigured_when_key_is_missing(): void
    {
        config(['services.gemini.api_key' => '']);

        $service = new GeminiSymptomMapperService();
        $result = $service->mapComplaintToSymptoms('batuk');

        $this->assertFalse($result['success']);
        $this->assertFalse($result['configured']);
        $this->assertEmpty($result['matched_symptom_ids']);
    }
}
