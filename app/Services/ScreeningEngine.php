<?php

namespace App\Services;

/**
 * Pure function scoring engine untuk skrining mandiri ISPA.
 * Deterministik, tanpa side effect, murni mengolah data masukan.
 * Sesuai terminologi medis: Skrining & Perkiraan Risiko (bukan diagnosis).
 */
class ScreeningEngine
{
    /**
     * Menghitung perkiraan risiko penyakit ISPA berdasarkan gejala yang dipilih.
     *
     * @param array<string> $selectedSymptomIds
     * @param array<array{symptom_id: string, disease_id: string, weight: float}> $weights
     * @param array<array{id: string, name: string, severity_level: ?string, description: ?string}> $diseases
     * @return array<array{
     *   disease_id: string,
     *   disease_name: string,
     *   severity_level: ?string,
     *   confidence_score: float,
     *   matched_symptoms_count: int,
     *   total_symptoms_for_disease: int,
     *   reasoning: string
     * }>
     */
    public function calculateScreeningRisk(
        array $selectedSymptomIds,
        array $weights,
        array $diseases
    ): array {
        if (empty($selectedSymptomIds)) {
            return [];
        }

        $selectedSet = array_flip($selectedSymptomIds);
        $assessments = [];

        foreach ($diseases as $disease) {
            $diseaseId = $disease['id'];
            $diseaseWeights = array_values(array_filter($weights, fn ($w) => $w['disease_id'] === $diseaseId));

            if (empty($diseaseWeights)) {
                continue;
            }

            $matchedScore = 0.0;
            $totalPossibleScore = 0.0;
            $matchedCount = 0;

            foreach ($diseaseWeights as $item) {
                $weight = (float) ($item['weight'] ?? 1.0);
                $totalPossibleScore += $weight;

                if (isset($selectedSet[$item['symptom_id']])) {
                    $matchedScore += $weight;
                    $matchedCount++;
                }
            }

            $confidenceScore = $totalPossibleScore > 0
                ? round(($matchedScore / $totalPossibleScore) * 100, 1)
                : 0.0;

            if ($matchedCount > 0) {
                $diseaseName = $disease['name'];
                $totalSymptoms = count($diseaseWeights);
                $reasoning = "Skrining mengidentifikasi {$matchedCount} dari {$totalSymptoms} indikator risiko untuk {$diseaseName} dengan estimasi kecocokan {$confidenceScore}%.";

                $assessments[] = [
                    'disease_id' => $diseaseId,
                    'disease_name' => $diseaseName,
                    'severity_level' => $disease['severity_level'] ?? null,
                    'confidence_score' => $confidenceScore,
                    'matched_symptoms_count' => $matchedCount,
                    'total_symptoms_for_disease' => $totalSymptoms,
                    'reasoning' => $reasoning,
                ];
            }
        }

        // Urutkan dari perkiraan risiko tertinggi ke terendah
        usort($assessments, fn ($a, $b) => $b['confidence_score'] <=> $a['confidence_score']);

        return $assessments;
    }
}
