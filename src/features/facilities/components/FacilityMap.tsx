import React from "react";
import type { HealthFacility } from "../types/facility.types";

interface FacilityMapProps {
  userLat: number;
  userLong: number;
  facilities: HealthFacility[];
}

export const FacilityMap: React.FC<FacilityMapProps> = ({
  userLat,
  userLong,
  facilities,
}) => {
  const delta = 0.06;
  const bbox = `${userLong - delta}%2C${userLat - delta}%2C${userLong + delta}%2C${userLat + delta}`;
  const embedUrl = `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${userLat}%2C${userLong}`;

  return (
    <div
      style={{
        borderRadius: "var(--radius-md)",
        overflow: "hidden",
        border: "1px solid var(--border-color)",
        backgroundColor: "#ffffff",
        boxShadow: "var(--shadow-sm)",
      }}
    >
      <div
        style={{
          padding: "12px 18px",
          backgroundColor: "var(--bg-surface)",
          borderBottom: "1px solid var(--border-color)",
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          flexWrap: "wrap",
          gap: "8px",
        }}
      >
        <div style={{ display: "flex", alignItems: "center", gap: "8px", fontSize: "0.9rem", fontWeight: 600 }}>
          <span>🗺️ Peta Interaktif & Titik Lokasi Anda</span>
        </div>
        <div style={{ fontSize: "0.8rem", color: "var(--text-muted)" }}>
          Koordinat: {userLat.toFixed(4)}, {userLong.toFixed(4)}
        </div>
      </div>

      <div style={{ position: "relative", width: "100%", height: "360px", background: "#e5e7eb" }}>
        <iframe
          title="Peta Fasilitas Kesehatan Terdekat"
          width="100%"
          height="100%"
          frameBorder="0"
          scrolling="no"
          marginHeight={0}
          marginWidth={0}
          src={embedUrl}
          style={{ border: 0 }}
        />
      </div>

      <div
        style={{
          padding: "12px 18px",
          fontSize: "0.8rem",
          color: "var(--text-secondary)",
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          background: "#fafafa",
        }}
      >
        <span>Pin merah menunjukkan titik acuan lokasi GPS Anda.</span>
        <a
          href={`https://www.openstreetmap.org/?mlat=${userLat}&mlon=${userLong}#map=14/${userLat}/${userLong}`}
          target="_blank"
          rel="noopener noreferrer"
          style={{ color: "var(--primary)", fontWeight: 600 }}
        >
          Buka Peta Penuh &rarr;
        </a>
      </div>
    </div>
  );
};
