import { NextRequest, NextResponse } from "next/server";
import { createClient } from "@/lib/supabase/server";

export async function GET(
  _request: NextRequest,
  context: { params: Promise<{ sessionId: string }> }
) {
  try {
    const { sessionId } = await context.params;
    const uuidRegex =
      /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;

    if (!uuidRegex.test(sessionId)) {
      return NextResponse.json(
        { error: "Format ID sesi tidak valid" },
        { status: 400 }
      );
    }

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

    const { data: results, error: resultsError } = await supabase
      .from("screening_results")
      .select(
        `
        id,
        session_id,
        disease_id,
        confidence_score,
        reasoning,
        created_at,
        diseases:disease_id (
          name,
          severity_level,
          description
        )
      `
      )
      .eq("session_id", sessionId)
      .order("confidence_score", { ascending: false });

    if (resultsError) {
      console.error("Gagal mengambil hasil skrining:", resultsError);
      return NextResponse.json(
        { error: "Gagal memuat hasil skrining" },
        { status: 500 }
      );
    }

    return NextResponse.json({
      sessionId: session.id,
      status: session.status,
      results: (results || []).map((result: any) => ({
        id: result.id,
        session_id: result.session_id,
        disease_id: result.disease_id,
        disease_name: result.diseases?.name || "Kondisi Terkait ISPA",
        severity_level: result.diseases?.severity_level || null,
        confidence_score: Number(result.confidence_score),
        reasoning: result.reasoning,
        created_at: result.created_at,
      })),
      disclaimer:
        "PERHATIAN: Hasil ini adalah estimasi perkiraan risiko skrining mandiri awal berbasis algoritma dan BUKAN diagnosis medis resmi. Konsultasikan dengan dokter atau kunjungi fasilitas kesehatan terdekat untuk pemeriksaan lebih lanjut.",
    });
  } catch (error) {
    console.error("API error /screening-sessions/[sessionId]/result:", error);
    return NextResponse.json(
      { error: "Terjadi kesalahan internal server" },
      { status: 500 }
    );
  }
}
