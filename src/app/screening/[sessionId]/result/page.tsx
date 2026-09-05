import { ScreeningResultView } from "@/features/screening/components/ScreeningResultView";

export const metadata = {
  title: "Hasil Perkiraan Risiko Skrining ISPA — ISHAP",
  description: "Hasil evaluasi perkiraan risiko skrining mandiri gejala ISPA",
};

interface PageProps {
  params: Promise<{ sessionId: string }>;
}

export default async function ScreeningResultPage({ params }: PageProps) {
  const { sessionId } = await params;
  return <ScreeningResultView sessionId={sessionId} />;
}
