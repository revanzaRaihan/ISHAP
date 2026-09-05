import { Suspense } from "react";
import { NearbyFacilitiesView } from "@/features/facilities/components/NearbyFacilitiesView";

export const metadata = {
  title: "Fasilitas Kesehatan Terdekat — ISHAP",
  description: "Daftar Puskesmas dan Rumah Sakit terdekat untuk rujukan dan penanganan ISPA",
};

export default function FacilitiesPage() {
  return (
    <Suspense
      fallback={
        <div style={{ textAlign: "center", padding: "80px 0" }}>
          <p style={{ color: "var(--text-secondary)" }}>Memuat rekomendasi fasilitas kesehatan...</p>
        </div>
      }
    >
      <NearbyFacilitiesView />
    </Suspense>
  );
}
