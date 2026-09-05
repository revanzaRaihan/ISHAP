<?php

namespace Tests\Unit;

use App\Services\ScreeningEngine;
use PHPUnit\Framework\TestCase;

class ScreeningEngineTest extends TestCase
{
    private ScreeningEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new ScreeningEngine();
    }

    public function test_returns_empty_when_no_symptoms_selected(): void
    {
        $result = $this->engine->calculateScreeningRisk([], [], []);
        $this->assertEmpty($result);
    }

    public function test_calculates_correct_confidence_score_and_ordering(): void
    {
        $selectedSymptoms = ['sym-1', 'sym-2'];

        $weights = [
            // Disease A (total possible = 5.0, matched = 2.0 + 3.0 = 5.0 -> 100%)
            ['symptom_id' => 'sym-1', 'disease_id' => 'dis-a', 'weight' => 2.0],
            ['symptom_id' => 'sym-2', 'disease_id' => 'dis-a', 'weight' => 3.0],

            // Disease B (total possible = 6.0, matched = 2.0 -> 33.3%)
            ['symptom_id' => 'sym-1', 'disease_id' => 'dis-b', 'weight' => 2.0],
            ['symptom_id' => 'sym-3', 'disease_id' => 'dis-b', 'weight' => 4.0],
        ];

        $diseases = [
            ['id' => 'dis-a', 'name' => 'Kondisi Alpha', 'severity_level' => 'ringan', 'description' => 'Desc A'],
            ['id' => 'dis-b', 'name' => 'Kondisi Beta', 'severity_level' => 'sedang', 'description' => 'Desc B'],
        ];

        $results = $this->engine->calculateScreeningRisk($selectedSymptoms, $weights, $diseases);

        $this->assertCount(2, $results);

        // Highest risk must be Disease A (100%)
        $this->assertEquals('dis-a', $results[0]['disease_id']);
        $this->assertEquals(100.0, $results[0]['confidence_score']);
        $this->assertEquals(2, $results[0]['matched_symptoms_count']);

        // Second must be Disease B (33.3%)
        $this->assertEquals('dis-b', $results[1]['disease_id']);
        $this->assertEquals(33.3, $results[1]['confidence_score']);
    }
}
