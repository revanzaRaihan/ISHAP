import { NextResponse } from "next/server";
import { createClient } from "@/lib/supabase/server";

export async function POST() {
  try {
    const supabase = await createClient();
    const {
      data: { user },
    } = await supabase.auth.getUser();

    const { data: session, error } = await supabase
      .from("screening_sessions")
      .insert({
        user_id: user?.id || null,
        status: "in_progress",
      })
      .select("id, status")
      .single();

    if (error || !session) {
      console.error("Gagal membuat sesi skrining:", error);
      return NextResponse.json(
        { error: "Gagal menginisialisasi sesi skrining mandiri" },
        { status: 500 }
      );
    }

    return NextResponse.json({
      sessionId: session.id,
      status: session.status,
    });
  } catch (error) {
    console.error("API error /screening-sessions:", error);
    return NextResponse.json(
      { error: "Terjadi kesalahan internal pada server" },
      { status: 500 }
    );
  }
}
