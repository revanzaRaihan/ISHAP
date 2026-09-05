export interface Symptom {
  id: string;
  name: string;
  category: string | null;
  description: string | null;
  created_at?: string;
}

export interface Disease {
  id: string;
  name: string;
  severity_level: string | null;
  description: string | null;
  created_at?: string;
}

export interface ScreeningSession {
  id: string;
  user_id: string | null;
  status: "in_progress" | "completed" | "abandoned";
  created_at: string;
}

export interface ScreeningResult {
  id: string;
  session_id: string;
  disease_id: string;
  disease_name?: string;
  severity_level?: string | null;
  confidence_score: number;
  reasoning: string | null;
  created_at: string;
}

export interface CreateSessionResponse {
  sessionId: string;
  status: string;
}

export interface SubmitSymptomsPayload {
  symptomIds: string[];
}

export interface ScreeningResultResponse {
  sessionId: string;
  status: string;
  results: ScreeningResult[];
  disclaimer: string;
}
