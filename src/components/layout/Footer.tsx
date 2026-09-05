import React from "react";

export const Footer: React.FC = () => {
  return (
    <footer
      style={{
        borderTop: "1px solid var(--border-color)",
        backgroundColor: "#ffffff",
        padding: "36px 0",
        color: "var(--text-secondary)",
        fontSize: "0.875rem",
      }}
    >
      <div
        className="container"
        style={{
          display: "flex",
          flexDirection: "column",
          gap: "16px",
          alignItems: "center",
          textAlign: "center",
        }}
      >
        <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
          <strong>ISHAP</strong> — Inisiatif Skrining Mandiri ISPA & Deteksi Dini
        </div>
        <p style={{ maxWidth: "720px", color: "var(--text-muted)", fontSize: "0.8rem" }}>
          Aplikasi ini adalah platform <strong>skrining mandiri risiko ISPA</strong>, bukan alat diagnosis resmi. Hasil penilaian tidak menggantikan saran, diagnosis, atau penanganan medis profesional dari dokter atau tenaga kesehatan berwenang.
        </p>
        <p style={{ fontSize: "0.8rem", color: "var(--text-muted)" }}>
          &copy; {new Date().getFullYear()} ISHAP. Hak Cipta Dilindungi.
        </p>
      </div>
    </footer>
  );
};
