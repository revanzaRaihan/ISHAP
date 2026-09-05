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

    // Pastikan sesi ada
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

    // Ambil hasil skrining dan gabungkan dengan master penyakit
    const { data: results, error: resultsErr } = await supabase
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

    if (resultsErr) {
      console.error("Gagal mengambil hasil skrining:", resultsErr);
      return NextResponse.json(
        { error: "Gagal memuat hasil skrining" },
        { status: 500 }
      );
    }

    const formattedResults = (results || []).map((r: any) => ({
      id: r.id,
      session_id: r.session_id,
      disease_id: r.disease_id,
      disease_name: r.diseases?.name || "Kondisi Terkait ISPA",
      disease_description: r.diseases?.description || null,
      severity_level: r.diseases?.severity_level || null,
      confidence_score: Number(r.confidence_score),
      reasoning: r.reasoning,
      created_at: r.created_at,
    }));

    return NextResponse.json({
      sessionId: session.id,
      status: session.status,
      results: formattedResults,
      disclaimer:
        "PERHATIAN: Hasil ini adalah estimasi perkiraan risiko skrining mandiri awal berbasis algoritma dan BUKAN diagnosis medis resmi. Konsultasikan dengan dokter atau kunjungi fasilitas kesehatan terdekat untuk pemeriksaan fisik dan penanganan medis lebih lanjut.",
    });
  } catch (err: any) {
    console.error("API error /screening-sessions/[sessionId]/result:", err);
    return NextResponse.json(
      { error: "Terjadi kesalahan internal server" },
      { status: 500 }
    );
  }
}
