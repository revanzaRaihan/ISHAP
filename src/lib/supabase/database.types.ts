export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[];

export type Database = {
  public: {
    Tables: {
      profiles: {
        Row: {
          id: string;
          name: string | null;
          phone: string | null;
          created_at: string;
        };
        Insert: {
          id: string;
          name?: string | null;
          phone?: string | null;
          created_at?: string;
        };
        Update: {
          id?: string;
          name?: string | null;
          phone?: string | null;
          created_at?: string;
        };
        Relationships: [];
      };
      symptoms: {
        Row: {
          id: string;
          name: string;
          category: string | null;
          description: string | null;
          created_at: string;
        };
        Insert: {
          id?: string;
          name: string;
          category?: string | null;
          description?: string | null;
          created_at?: string;
        };
        Update: {
          id?: string;
          name?: string;
          category?: string | null;
          description?: string | null;
          created_at?: string;
        };
        Relationships: [];
      };
      diseases: {
        Row: {
          id: string;
          name: string;
          severity_level: string | null;
          description: string | null;
          created_at: string;
        };
        Insert: {
          id?: string;
          name: string;
          severity_level?: string | null;
          description?: string | null;
          created_at?: string;
        };
        Update: {
          id?: string;
          name?: string;
          severity_level?: string | null;
          description?: string | null;
          created_at?: string;
        };
        Relationships: [];
      };
      symptom_disease_map: {
        Row: {
          id: string;
          symptom_id: string;
          disease_id: string;
          weight: number;
        };
        Insert: {
          id?: string;
          symptom_id: string;
          disease_id: string;
          weight: number;
        };
        Update: {
          id?: string;
          symptom_id?: string;
          disease_id?: string;
          weight?: number;
        };
        Relationships: [];
      };
      screening_sessions: {
        Row: {
          id: string;
          user_id: string | null;
          status: string;
          created_at: string;
        };
        Insert: {
          id?: string;
          user_id?: string | null;
          status?: string;
          created_at?: string;
        };
        Update: {
          id?: string;
          user_id?: string | null;
          status?: string;
          created_at?: string;
        };
        Relationships: [];
      };
      session_symptoms: {
        Row: {
          id: string;
          session_id: string;
          symptom_id: string;
        };
        Insert: {
          id?: string;
          session_id: string;
          symptom_id: string;
        };
        Update: {
          id?: string;
          session_id?: string;
          symptom_id?: string;
        };
        Relationships: [];
      };
      screening_results: {
        Row: {
          id: string;
          session_id: string;
          disease_id: string;
          confidence_score: number;
          reasoning: string | null;
          created_at: string;
        };
        Insert: {
          id?: string;
          session_id: string;
          disease_id: string;
          confidence_score: number;
          reasoning?: string | null;
          created_at?: string;
        };
        Update: {
          id?: string;
          session_id?: string;
          disease_id?: string;
          confidence_score?: number;
          reasoning?: string | null;
          created_at?: string;
        };
        Relationships: [];
      };
      online_doctor_profiles: {
        Row: {
          id: string;
          name: string;
          platform: string | null;
          profile_url: string | null;
          specialty: string | null;
        };
        Insert: {
          id?: string;
          name: string;
          platform?: string | null;
          profile_url?: string | null;
          specialty?: string | null;
        };
        Update: {
          id?: string;
          name?: string;
          platform?: string | null;
          profile_url?: string | null;
          specialty?: string | null;
        };
        Relationships: [];
      };
      consultation_referrals: {
        Row: {
          id: string;
          session_id: string;
          doctor_profile_id: string;
        };
        Insert: {
          id?: string;
          session_id: string;
          doctor_profile_id: string;
        };
        Update: {
          id?: string;
          session_id?: string;
          doctor_profile_id?: string;
        };
        Relationships: [];
      };
    };
    Views: {
      [_ in never]: never;
    };
    Functions: {
      [_ in never]: never;
    };
    Enums: {
      [_ in never]: never;
    };
    CompositeTypes: {
      [_ in never]: never;
    };
  };
};
