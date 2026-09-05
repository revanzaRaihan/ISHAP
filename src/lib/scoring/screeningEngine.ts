/**
 * Pure function scoring engine untuk skrining mandiri ISPA.
 * Tidak bergantung pada Supabase client maupun UI.
 * 
 * Menggunakan istilah medis skrining & perkiraan risiko (bukan diagnosis).
 */

export interface SymptomWeightInput {
  symptomId: string;
  diseaseId: string;
  weight: number;
}

export interface DiseaseInput {
  id: string;
  name: string;
  severityLevel: string | null;
  description: string | null;
}

export interface ScreeningRiskAssessment {
  diseaseId: string;
  diseaseName: string;
  severityLevel: string | null;
  confidenceScore: number; // Nilai persentase 0.0 - 100.0
  matchedSymptomsCount: number;
  totalSymptomsForDisease: number;
  reasoning: string;
}

/**
 * Menghitung perkiraan risiko penyakit ISPA berdasarkan gejala yang dipilih.
 * Pure function: Deterministic, tidak ada side-effect, mudah di-unit test.
 */
export function calculateScreeningRisk(
  selectedSymptomIds: string[],
  weights: SymptomWeightInput[],
  diseases: DiseaseInput[]
): ScreeningRiskAssessment[] {
  if (!selectedSymptomIds || selectedSymptomIds.length === 0) {
    return [];
  }

  const selectedSet = new Set(selectedSymptomIds);
  const assessments: ScreeningRiskAssessment[] = [];

  for (const disease of diseases) {
    const diseaseWeights = weights.filter((w) => w.diseaseId === disease.id);

    if (diseaseWeights.length === 0) {
      continue;
    }

    let matchedScore = 0;
    let totalPossibleScore = 0;
    let matchedCount = 0;

    for (const item of diseaseWeights) {
      const weight = Number(item.weight) || 1;
      totalPossibleScore += weight;

      if (selectedSet.has(item.symptomId)) {
        matchedScore += weight;
        matchedCount += 1;
      }
    }

    // Hitung persentase kecocokan (confidence score)
    const confidenceScore =
      totalPossibleScore > 0
        ? Number(((matchedScore / totalPossibleScore) * 100).toFixed(1))
        : 0;

    // Hanya sertakan jika ada minimal 1 gejala yang cocok
    if (matchedCount > 0) {
      const reasoning = `Skrining mengidentifikasi ${matchedCount} dari ${diseaseWeights.length} indikator risiko untuk ${disease.name} dengan estimasi kecocokan ${confidenceScore}%.`;

      assessments.push({
        diseaseId: disease.id,
        diseaseName: disease.name,
        severityLevel: disease.severityLevel,
        confidenceScore,
        matchedSymptomsCount: matchedCount,
        totalSymptomsForDisease: diseaseWeights.length,
        reasoning,
      });
    }
  }

  // Urutkan dari perkiraan risiko tertinggi ke terendah
  return assessments.sort((a, b) => b.confidenceScore - a.confidenceScore);
}
