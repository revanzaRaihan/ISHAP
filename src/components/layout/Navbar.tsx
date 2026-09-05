import React from "react";
import Link from "next/link";

export const Navbar: React.FC = () => {
  return (
    <header className="header-nav">
      <div
        className="container"
        style={{
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
          height: "72px",
        }}
      >
        <Link
          href="/"
          style={{
            display: "flex",
            alignItems: "center",
            gap: "10px",
            fontSize: "1.35rem",
            fontWeight: 800,
            letterSpacing: "-0.03em",
          }}
        >
          <span
            style={{
              display: "inline-flex",
              alignItems: "center",
              justifyContent: "center",
              width: "36px",
              height: "36px",
              background: "linear-gradient(135deg, #0284c7 0%, #0d9488 100%)",
              color: "#ffffff",
              borderRadius: "10px",
              fontSize: "1.1rem",
              fontWeight: 800,
            }}
          >
            I
          </span>
          <span className="gradient-text">ISHAP</span>
        </Link>

        <nav style={{ display: "flex", alignItems: "center", gap: "24px" }}>
          <Link
            href="/"
            style={{
              fontSize: "0.95rem",
              fontWeight: 600,
              color: "var(--text-secondary)",
            }}
          >
            Beranda & AQI
          </Link>
          <Link
            href="/screening"
            className="btn btn-primary"
            style={{ padding: "8px 18px", fontSize: "0.9rem" }}
          >
            Mulai Skrining
          </Link>
        </nav>
      </div>
    </header>
  );
};
