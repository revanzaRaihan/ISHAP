import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { createClient } from "@/lib/supabase/server";
import { calculateScreeningRisk } from "@/lib/scoring/screeningEngine";

// Validasi skema input dengan Zod untuk mencegah payload tampering & injection
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

    // 1. Validasi UUID session id
    const uuidRegex =
      /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
    if (!uuidRegex.test(sessionId)) {
      return NextResponse.json(
        { error: "Format ID sesi skrining tidak valid" },
        { status: 400 }
      );
    }

    // 2. Parse & validasi body JSON
    const body = await request.json().catch(() => ({}));
    const parseResult = submitSymptomsSchema.safeParse(body);

    if (!parseResult.success) {
      return NextResponse.json(
        {
          error: "Validasi gagal",
          details: parseResult.error.errors.map((e) => e.message),
        },
        { status: 422 }
      );
    }

    const { symptomIds } = parseResult.data;
    const supabase = await createClient();

    // 3. Pastikan sesi ada dan masih aktif
    const { data: session, error: sessionErr } = await supabase
      .from("screening_sessions")
      .select("id, status")
      .eq("id", sessionId)
      .single();

    if (sessionErr || !session) {
      return NextResponse.json(
        { error: "Sesi skrining tidak ditemukan" },
        { status: 404 }
      );
    }

    // 4. Catat gejala yang dipilih user ke session_symptoms
    const sessionSymptomsToInsert = symptomIds.map((sId) => ({
      session_id: sessionId,
      symptom_id: sId,
    }));

    await supabase.from("session_symptoms").insert(sessionSymptomsToInsert);

    // 5. Ambil data master penyakit dan pemetaan bobot untuk kalkulasi risiko di server
    const [{ data: diseases }, { data: weights }] = await Promise.all([
      supabase.from("diseases").select("id, name, severity_level, description"),
      supabase.from("symptom_disease_map").select("symptom_id, disease_id, weight"),
    ]);

    const mappedWeights = (weights || []).map((w) => ({
      symptomId: w.symptom_id,
      diseaseId: w.disease_id,
      weight: Number(w.weight),
    }));

    const mappedDiseases = (diseases || []).map((d) => ({
      id: d.id,
      name: d.name,
      severityLevel: d.severity_level,
      description: d.description,
    }));

    // 6. Eksekusi pure function scoring engine di server
    const riskAssessments = calculateScreeningRisk(
      symptomIds,
      mappedWeights,
      mappedDiseases
    );

    // 7. Simpan hasil skrining ke screening_results
    if (riskAssessments.length > 0) {
      const resultsToInsert = riskAssessments.map((ra) => ({
        session_id: sessionId,
        disease_id: ra.diseaseId,
        confidence_score: ra.confidenceScore,
        reasoning: ra.reasoning,
      }));

      await supabase.from("screening_results").insert(resultsToInsert);
    }

    // 8. Tandai status sesi selesai
    await supabase
      .from("screening_sessions")
      .update({ status: "completed" })
      .eq("id", sessionId);

    return NextResponse.json({
      success: true,
      resultUrl: `/screening/${sessionId}/result`,
    });
  } catch (err: any) {
    console.error("API error /screening-sessions/[sessionId]/symptoms:", err);
    return NextResponse.json(
      { error: "Terjadi kesalahan saat memproses data skrining" },
      { status: 500 }
    );
  }
}
