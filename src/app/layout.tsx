import type { Metadata } from "next";
import "./globals.css";
import Navbar from "@/components/layout/Navbar";
import Footer from "@/components/layout/Footer";

export const metadata: Metadata = {
  title: "ISHAP — Skrining Mandiri ISPA & Deteksi Dini",
  description:
    "Aplikasi skrining mandiri berbasis web untuk penilaian risiko awal gejala ISPA, terintegrasi dengan informasi kualitas udara (AQI) dan rujukan fasilitas kesehatan terdekat.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id">
      <body>
        <Navbar />
        <main className="main-content">{children}</main>
        <Footer />
      </body>
    </html>
  );
}
