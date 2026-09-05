import React from "react";
import type { HealthFacility } from "../types/facility.types";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";

interface FacilityCardProps {
  facility: HealthFacility;
}

export const FacilityCard: React.FC<FacilityCardProps> = ({ facility }) => {
  const isHospital = facility.type?.toLowerCase() === "rumah_sakit";
  const typeLabel = isHospital ? "Rumah Sakit / Rujukan" : "Puskesmas Pratama";
  const typeBadgeVariant = isHospital ? "warning" : "info";
  const mapsUrl =
    facility.latitude && facility.longitude
      ? `https://www.google.com/maps/dir/?api=1&destination=${facility.latitude},${facility.longitude}`
      : `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(facility.name + " " + (facility.address || ""))}`;

  return (
    <Card
      style={{
        display: "flex",
        flexDirection: "column",
        justifyContent: "space-between",
        gap: "16px",
        height: "100%",
        borderLeft: isHospital ? "4px solid var(--accent-amber)" : "4px solid var(--primary)",
      }}
    >
      <div>
        <div
          style={{
            display: "flex",
            justifyContent: "space-between",
            alignItems: "flex-start",
            gap: "12px",
            marginBottom: "10px",
          }}
        >
          <Badge variant={typeBadgeVariant}>
            {isHospital ? "🏥 " : "🩺 "} {typeLabel}
          </Badge>
          {facility.distance_km !== undefined && (
            <span
              style={{
                fontSize: "0.85rem",
                fontWeight: 700,
                color: "var(--primary-dark)",
                backgroundColor: "var(--primary-light)",
                padding: "3px 10px",
                borderRadius: "var(--radius-full)",
              }}
            >
              📍 ~{facility.distance_km} km
            </span>
          )}
        </div>

        <h3
          style={{
            fontSize: "1.15rem",
            fontWeight: 700,
            color: "var(--text-primary)",
            marginBottom: "6px",
            lineHeight: 1.35,
          }}
        >
          {facility.name}
        </h3>

        <p
          style={{
            fontSize: "0.875rem",
            color: "var(--text-secondary)",
            lineHeight: 1.5,
          }}
        >
          {facility.address || "Alamat lengkap tersedia melalui peta navigasi."}
        </p>
      </div>

      <div
        style={{
          borderTop: "1px solid var(--border-color)",
          paddingTop: "14px",
          display: "flex",
          gap: "10px",
          alignItems: "center",
        }}
      >
        <a
          href={mapsUrl}
          target="_blank"
          rel="noopener noreferrer"
          style={{ width: "100%" }}
        >
          <Button
            size="sm"
            variant={isHospital ? "primary" : "secondary"}
            style={{ width: "100%", fontSize: "0.875rem", gap: "6px" }}
          >
            🧭 Petunjuk Arah (Google Maps)
          </Button>
        </a>
      </div>
    </Card>
  );
};
