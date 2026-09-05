<?php

namespace Tests\Feature;

use App\Models\Symptom;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScreeningExtractionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_complaint_extraction_validates_required_and_min_length(): void
    {
        // 1. Empty input
        $res = $this->postJson('/screening/extract-symptoms', [
            'complaint' => '',
        ]);
        $res->assertStatus(422);
        $res->assertJsonValidationErrors(['complaint']);

        // 2. Too short (< 4 chars)
        $resShort = $this->postJson('/screening/extract-symptoms', [
            'complaint' => 'bat',
        ]);
        $resShort->assertStatus(422);
        $resShort->assertJsonValidationErrors(['complaint']);
    }

    public function test_complaint_extraction_returns_matched_symptoms_with_mock(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'matched_symptom_ids' => [1, 4],
                                        'summary' => 'Terdeteksi indikasi batuk kering dan nyeri tenggorokan.'
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/screening/extract-symptoms', [
            'complaint' => 'Tenggorokan sakit sekali untuk menelan dan batuk kering tidak berdahak',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'matched_symptom_ids' => [1, 4],
            'summary' => 'Terdeteksi indikasi batuk kering dan nyeri tenggorokan.',
            'configured' => true,
        ]);
    }

    public function test_complaint_extraction_handles_gemini_api_error_gracefully(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response('Quota exceeded', 429)
        ]);

        $response = $this->postJson('/screening/extract-symptoms', [
            'complaint' => 'Saya merasa sesak napas dan dada sesak',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'matched_symptom_ids' => [],
        ]);
    }
}
