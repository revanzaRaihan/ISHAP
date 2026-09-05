import Link from 'next/link';

export default function Navbar() {
  return (
    <header className="sticky top-0 z-50 bg-bg-app/90 backdrop-blur-md border-b border-slate-200/60">
      <div className="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
        <Link href="/" className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-[#0F5144] rounded-xl flex items-center justify-center text-white shadow-sm">
            <svg
              className="w-5 h-5 fill-current"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path d="M19 10.5h-5.5V5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v5.5H5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5h5.5V19c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5v-5.5H19c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5z" />
            </svg>
          </div>
          <div>
            <span className="font-extrabold text-xl text-slate-800 tracking-tight block leading-none">
              I-SHAP
            </span>
            <span className="text-[10px] text-primary-health font-semibold tracking-wider uppercase">
              Health Screening
            </span>
          </div>
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
            href="/facilities"
            style={{
              fontSize: "0.95rem",
              fontWeight: 600,
              color: "var(--text-secondary)",
            }}
          >
            Faskes Terdekat
          </Link>
          <Link
            href="/screening"
            className="btn btn-primary"
            style={{ padding: "8px 18px", fontSize: "0.9rem" }}
          >
            Mulai Skrining
          </Link>
        </nav>

        <Link
          href="/screening"
          className="bg-primary-health hover:bg-primary-hover text-white px-5 py-2.5 rounded-2xl font-semibold text-sm transition shadow-sm"
        >
          Mulai Skrining
        </Link>
      </div>
    </header>
  );
}