import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { createClient } from "@/lib/supabase/server";
import { calculateScreeningRisk } from "@/lib/scoring/screeningEngine";

const submitSymptomsSchema = z.object({
  symptomIds: z
    .array(z.string().uuid({ message: "ID gejala harus berupa format UUID yang valid" }))
    .min(1, { message: "Pilih minimal 1 gejala untuk skrining" }),
});

export async function POST(
  request: NextRequest,
  context: { params: Promise<{ sessionId: string }> }
) {
  try {
    const { sessionId } = await context.params;
    const uuidRegex =
      /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

    if (!uuidRegex.test(sessionId)) {
      return NextResponse.json(
        { error: "Format ID sesi skrining tidak valid" },
        { status: 400 }
      );
    }

    const parseResult = submitSymptomsSchema.safeParse(
      await request.json().catch(() => ({}))
    );

    if (!parseResult.success) {
      return NextResponse.json(
        {
          error: "Validasi gagal",
          details: parseResult.error.errors.map((error) => error.message),
        },
        { status: 422 }
      );
    }

    const { symptomIds } = parseResult.data;
    const supabase = await createClient();
    const { data: session, error: sessionError } = await supabase
      .from("screening_sessions")
      .select("id, status")
      .eq("id", sessionId)
      .single();

    if (sessionError || !session) {
      return NextResponse.json(
        { error: "Sesi skrining tidak ditemukan" },
        { status: 404 }
      );
    }

    const { error: symptomsError } = await supabase
      .from("session_symptoms")
      .insert(symptomIds.map((symptomId) => ({ session_id: sessionId, symptom_id: symptomId })));

    if (symptomsError) {
      console.error("Gagal menyimpan gejala skrining:", symptomsError);
      return NextResponse.json(
        { error: "Gagal menyimpan gejala skrining" },
        { status: 500 }
      );
    }

    const [{ data: diseases }, { data: weights }] = await Promise.all([
      supabase.from("diseases").select("id, name, severity_level, description"),
      supabase.from("symptom_disease_map").select("symptom_id, disease_id, weight"),
    ]);

    const riskAssessments = calculateScreeningRisk(
      symptomIds,
      (weights || []).map((weight) => ({
        symptomId: weight.symptom_id,
        diseaseId: weight.disease_id,
        weight: Number(weight.weight),
      })),
      (diseases || []).map((disease) => ({
        id: disease.id,
        name: disease.name,
        severityLevel: disease.severity_level,
        description: disease.description,
      }))
    );

    if (riskAssessments.length > 0) {
      const { error: resultsError } = await supabase.from("screening_results").insert(
        riskAssessments.map((assessment) => ({
          session_id: sessionId,
          disease_id: assessment.diseaseId,
          confidence_score: assessment.confidenceScore,
          reasoning: assessment.reasoning,
        }))
      );

      if (resultsError) {
        console.error("Gagal menyimpan hasil skrining:", resultsError);
        return NextResponse.json(
          { error: "Gagal menyimpan hasil skrining" },
          { status: 500 }
        );
      }
    }

    const { error: updateError } = await supabase
      .from("screening_sessions")
      .update({ status: "completed" })
      .eq("id", sessionId);

    if (updateError) {
      console.error("Gagal memperbarui status sesi skrining:", updateError);
      return NextResponse.json(
        { error: "Gagal menyelesaikan sesi skrining" },
        { status: 500 }
      );
    }

    return NextResponse.json({
      success: true,
      resultUrl: `/screening/${sessionId}/result`,
    });
  } catch (error) {
    console.error("API error /screening-sessions/[sessionId]/symptoms:", error);
    return NextResponse.json(
      { error: "Terjadi kesalahan saat memproses data skrining" },
      { status: 500 }
    );
  }
}
