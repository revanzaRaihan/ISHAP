"use client";

import React, { useState } from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useNearbyFacilities, LOCATION_PRESETS } from "../hooks/useNearbyFacilities";
import { FacilityCard } from "./FacilityCard";
import { FacilityMap } from "./FacilityMap";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";

export const NearbyFacilitiesView: React.FC = () => {
  const searchParams = useSearchParams();
  const sessionId = searchParams.get("session");
  const [activeTab, setActiveTab] = useState<"list" | "map">("list");

  const {
    facilities,
    allFacilitiesCount,
    loading,
    error,
    location,
    radiusKm,
    filterType,
    setFilterType,
    detectUserLocation,
    selectPresetLocation,
    refresh,
  } = useNearbyFacilities(10);

  return (
    <div className="container" style={{ maxWidth: "1080px", margin: "0 auto", paddingBottom: "60px" }}>
      {/* Back Link to result if coming from screening */}
      {sessionId && (
        <div style={{ marginBottom: "20px" }}>
          <Link
            href={`/screening/${sessionId}/result`}
            style={{
              display: "inline-flex",
              alignItems: "center",
              gap: "6px",
              fontSize: "0.9rem",
              color: "var(--primary)",
              fontWeight: 600,
            }}
          >
            &larr; Kembali ke Hasil Skrining Sesi Anda
          </Link>
        </div>
      )}

      {/* Header Section */}
      <div style={{ marginBottom: "32px", textAlign: "center" }}>
        <Badge variant="info" style={{ marginBottom: "12px" }}>
          Rujukan Layanan Medis Terdekat
        </Badge>
        <h1 style={{ fontSize: "2.25rem", fontWeight: 800, color: "var(--text-primary)" }}>
          Fasilitas Kesehatan di Kota Anda
        </h1>
        <p style={{ color: "var(--text-secondary)", marginTop: "8px", maxWidth: "680px", margin: "8px auto 0" }}>
          Temukan Puskesmas Pratama dan Rumah Sakit rujukan di sekitar kota Anda (maksimal radius {radiusKm || 25} km) untuk pemeriksaan fisik langsung, penanganan gejala ISPA berat, atau konsultasi tatap muka.
        </p>
      </div>

      {/* Location Bar & Quick Action */}
      <Card
        style={{
          marginBottom: "28px",
          padding: "20px 24px",
          backgroundColor: "#ffffff",
          display: "flex",
          flexDirection: "column",
          gap: "16px",
        }}
      >
        <div
          style={{
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            flexWrap: "wrap",
            gap: "12px",
          }}
        >
          <div>
            <div style={{ fontSize: "0.8rem", textTransform: "uppercase", color: "var(--text-muted)", fontWeight: 700 }}>
              Titik Referensi Lokasi Saat Ini:
            </div>
            <div style={{ fontSize: "1.1rem", fontWeight: 700, color: "var(--text-primary)", display: "flex", alignItems: "center", gap: "8px", marginTop: "2px", flexWrap: "wrap" }}>
              <span>📍 {location.cityName || "Lokasi Anda"}</span>
              {!location.isCustomOrFallback && (
                <Badge variant="success" style={{ fontSize: "0.75rem" }}>
                  GPS Aktif
                </Badge>
              )}
              <Badge variant="info" style={{ fontSize: "0.75rem" }}>
                Radius Maks. {radiusKm || 25} km (Dalam Kota)
              </Badge>
            </div>
          </div>

          <div style={{ display: "flex", gap: "10px", flexWrap: "wrap" }}>
            <Button
              size="sm"
              variant="secondary"
              onClick={detectUserLocation}
              disabled={loading}
              style={{ fontSize: "0.85rem", gap: "6px" }}
            >
              📡 Deteksi Ulang GPS
            </Button>
            <Button
              size="sm"
              variant="secondary"
              onClick={refresh}
              disabled={loading}
              style={{ fontSize: "0.85rem" }}
            >
              🔄 Refresh
            </Button>
          </div>
        </div>

        {/* Quick Presets */}
        <div
          style={{
            borderTop: "1px solid var(--border-color)",
            paddingTop: "12px",
            display: "flex",
            alignItems: "center",
            gap: "8px",
            flexWrap: "wrap",
            fontSize: "0.85rem",
          }}
        >
          <span style={{ color: "var(--text-muted)" }}>Pilih Area Kota Terdaftar:</span>
          {LOCATION_PRESETS.map((p) => (
            <button
              key={p.name}
              onClick={() => selectPresetLocation(p)}
              style={{
                background: location.cityName === p.name ? "var(--primary-light)" : "var(--bg-surface)",
                border: location.cityName === p.name ? "1px solid var(--primary)" : "1px solid var(--border-color)",
                color: location.cityName === p.name ? "var(--primary-dark)" : "var(--text-secondary)",
                padding: "4px 10px",
                borderRadius: "var(--radius-sm)",
                fontSize: "0.8rem",
                fontWeight: 600,
                cursor: "pointer",
                transition: "all 0.15s ease",
              }}
            >
              {p.name}
            </button>
          ))}
        </div>
      </Card>

      {/* Warning/Error Banner */}
      {error && (
        <div
          style={{
            padding: "12px 18px",
            backgroundColor: "var(--accent-amber-light)",
            borderLeft: "4px solid var(--accent-amber)",
            borderRadius: "var(--radius-sm)",
            color: "#78350f",
            fontSize: "0.875rem",
            marginBottom: "24px",
          }}
        >
          ℹ️ {error}
        </div>
      )}

      {/* Filter and View Tabs */}
      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          flexWrap: "wrap",
          gap: "16px",
          marginBottom: "24px",
        }}
      >
        {/* Category Filter */}
        <div style={{ display: "flex", gap: "8px" }}>
          {[
            { id: "all", label: "Semua Faskes" },
            { id: "puskesmas", label: "Puskesmas" },
            { id: "rumah_sakit", label: "Rumah Sakit" },
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setFilterType(tab.id)}
              style={{
                padding: "8px 16px",
                borderRadius: "var(--radius-sm)",
                border: "none",
                fontWeight: 600,
                fontSize: "0.875rem",
                cursor: "pointer",
                backgroundColor: filterType === tab.id ? "var(--primary)" : "var(--bg-surface)",
                color: filterType === tab.id ? "#ffffff" : "var(--text-secondary)",
                transition: "all 0.15s ease",
              }}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* List / Map Switch */}
        <div
          style={{
            display: "inline-flex",
            backgroundColor: "var(--bg-surface)",
            padding: "4px",
            borderRadius: "var(--radius-sm)",
            border: "1px solid var(--border-color)",
          }}
        >
          <button
            onClick={() => setActiveTab("list")}
            style={{
              padding: "6px 14px",
              borderRadius: "4px",
              border: "none",
              fontSize: "0.85rem",
              fontWeight: 600,
              cursor: "pointer",
              backgroundColor: activeTab === "list" ? "#ffffff" : "transparent",
              color: activeTab === "list" ? "var(--primary)" : "var(--text-secondary)",
              boxShadow: activeTab === "list" ? "var(--shadow-sm)" : "none",
            }}
          >
            📋 Daftar ({facilities.length})
          </button>
          <button
            onClick={() => setActiveTab("map")}
            style={{
              padding: "6px 14px",
              borderRadius: "4px",
              border: "none",
              fontSize: "0.85rem",
              fontWeight: 600,
              cursor: "pointer",
              backgroundColor: activeTab === "map" ? "#ffffff" : "transparent",
              color: activeTab === "map" ? "var(--primary)" : "var(--text-secondary)",
              boxShadow: activeTab === "map" ? "var(--shadow-sm)" : "none",
            }}
          >
            🗺️ Tampilan Peta
          </button>
        </div>
      </div>

      {/* Main Content Area */}
      {loading ? (
        <div style={{ textAlign: "center", padding: "60px 0" }}>
          <p style={{ color: "var(--text-secondary)", fontSize: "1.05rem" }}>
            Mencari fasilitas kesehatan dalam radius {radiusKm || 25} km di kota Anda...
          </p>
        </div>
      ) : activeTab === "map" ? (
        <div style={{ display: "flex", flexDirection: "column", gap: "24px" }}>
          <FacilityMap userLat={location.lat} userLong={location.long} facilities={facilities} />

          <div>
            <h3 style={{ fontSize: "1.2rem", fontWeight: 700, marginBottom: "16px" }}>
              Faskes di Sekitar Wilayah Kota ({facilities.length})
            </h3>
            {facilities.length === 0 ? (
              <Card style={{ textAlign: "center", padding: "36px 20px" }}>
                <p style={{ color: "var(--text-secondary)", marginBottom: "14px" }}>
                  Tidak ada faskes terdaftar dalam radius {radiusKm || 25} km pada koordinat ini.
                </p>
                <a
                  href={`https://www.google.com/maps/search/rumah+sakit+puskesmas/@${location.lat},${location.long},13z`}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <Button size="sm">Cari Faskes Terdekat via Google Maps</Button>
                </a>
              </Card>
            ) : (
              <div
                style={{
                  display: "grid",
                  gridTemplateColumns: "repeat(auto-fit, minmax(300px, 1fr))",
                  gap: "16px",
                }}
              >
                {facilities.map((fac) => (
                  <FacilityCard key={fac.id} facility={fac} />
                ))}
              </div>
            )}
          </div>
        </div>
      ) : (
        <div>
          {facilities.length === 0 ? (
            <Card style={{ textAlign: "center", padding: "48px 24px" }}>
              <div style={{ fontSize: "2.5rem", marginBottom: "12px" }}>📍</div>
              <h3 style={{ fontSize: "1.2rem", fontWeight: 700, marginBottom: "8px" }}>
                Tidak Ada Faskes Terdaftar Dalam Radius Kota (Maks. {radiusKm || 25} km)
              </h3>
              <p style={{ color: "var(--text-secondary)", maxWidth: "560px", margin: "0 auto 24px", lineHeight: 1.6 }}>
                Sistem sengaja membatasi pencarian agar tidak melebar ke luar kota atau pulau lain. Jika Anda berada di luar area yang terdaftar di database, Anda dapat memilih kota terdaftar terdekat atau mencari langsung melalui Google Maps.
              </p>
              <div style={{ display: "flex", justifyContent: "center", gap: "12px", flexWrap: "wrap" }}>
                <Button size="sm" variant="secondary" onClick={() => selectPresetLocation(LOCATION_PRESETS[0])}>
                  Lihat Area Jakarta Pusat
                </Button>
                <a
                  href={`https://www.google.com/maps/search/rumah+sakit+puskesmas/@${location.lat},${location.long},13z`}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <Button size="sm">Buka Pencarian Maps Langsung</Button>
                </a>
              </div>
            </Card>
          ) : (
            <div
              style={{
                display: "grid",
                gridTemplateColumns: "repeat(auto-fit, minmax(320px, 1fr))",
                gap: "20px",
              }}
            >
              {facilities.map((fac) => (
                <FacilityCard key={fac.id} facility={fac} />
              ))}
            </div>
          )}
        </div>
      )}

      {/* Footer Emergency Notice */}
      <div
        style={{
          marginTop: "48px",
          padding: "20px 24px",
          borderRadius: "var(--radius-md)",
          backgroundColor: "var(--accent-red-light)",
          border: "1px solid #fca5a5",
          display: "flex",
          alignItems: "flex-start",
          gap: "16px",
        }}
      >
        <span style={{ fontSize: "1.5rem" }}>🚨</span>
        <div>
          <h4 style={{ fontSize: "1rem", fontWeight: 700, color: "var(--accent-red)", marginBottom: "4px" }}>
            Kondisi Kegawatdaruratan Saluran Pernapasan (Red Flag)
          </h4>
          <p style={{ fontSize: "0.85rem", color: "#991b1b", lineHeight: 1.5 }}>
            Jika Anda atau keluarga mengalami <strong>sesak napas parah, kebiruan pada bibir/kuku, dada terasa tertekan hebat, atau penurunan kesadaran</strong>, jangan menunggu. Segera kunjungi Instalasi Gawat Darurat (IGD) Rumah Sakit terdekat atau hubungi nomor darurat <strong>112 / 119</strong>.
          </p>
        </div>
      </div>
    </div>
  );
};
