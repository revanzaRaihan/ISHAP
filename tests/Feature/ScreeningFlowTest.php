<?php

namespace Tests\Feature;

use App\Models\ScreeningSession;
use App\Models\Symptom;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreeningFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('ISHAP');
        $response->assertSee('Live Kualitas Udara');
    }

    public function test_screening_page_displays_symptoms(): void
    {
        $response = $this->get('/screening');
        $response->assertStatus(200);
        $response->assertSee('Batuk Kering');
        $response->assertSee('Saluran Napas Atas');
    }

    public function test_submitting_symptoms_creates_session_and_redirects_to_result(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $symptoms = Symptom::take(3)->pluck('id')->toArray();

        $response = $this->post('/screening', [
            'symptom_ids' => $symptoms,
        ]);

        $session = ScreeningSession::latest()->first();
        $this->assertNotNull($session);

        $response->assertRedirect("/screening/{$session->id}/result");

        // Verify result page renders properly
        $resultResponse = $this->get("/screening/{$session->id}/result");
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Hasil Skrining Mandiri');
        $resultResponse->assertSee('Mengapa Anda Mengalami Gejala Ini?');
    }

    public function test_facilities_page_is_accessible(): void
    {
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $response->assertSee('Fasilitas Kesehatan Terdekat');
    }

    public function test_doctors_page_is_accessible(): void
    {
        $response = $this->get('/doctors');
        $response->assertStatus(200);
        $response->assertSee('Konsultasi Dokter Online Mitra');
        $response->assertSee('Dokter Spesialis Paru & Pernapasan');
    }
}
