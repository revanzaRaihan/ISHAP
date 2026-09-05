export interface OnlineDoctorProfile {
  id: string;
  name: string;
  platform: string | null;
  profile_url: string | null;
  specialty: string | null;
}

export interface ConsultationReferral {
  id: string;
  session_id: string;
  doctor_profile_id: string;
}
