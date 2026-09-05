export default function Footer() {
  return (
    <footer className="bg-white border-t border-slate-100 py-12 text-slate-500 text-sm">
      <div className="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
          <p className="font-bold text-slate-800 text-base">ISHAP</p>
          <p className="text-xs text-slate-400 mt-1">Intelligent Screening for Health Awareness & Prevention</p>
        </div>
        <p className="text-xs text-slate-400">
          © 2026 ISHAP Balikpapan. Dibuat untuk kesadaran kesehatan masyarakat.
        </p>
      </div>
    </footer>
  );
}