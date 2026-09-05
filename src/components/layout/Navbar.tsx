import Link from 'next/link';

export default function Navbar() {
  return (
    <header className="sticky top-0 z-50 bg-[#F7FAF9]/90 backdrop-blur-md border-b border-slate-200/60">
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
            <span className="text-[10px] text-[#168A70] font-semibold tracking-wider uppercase">
              Health Screening
            </span>
          </div>
        </Link>

        <nav className="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
          <Link href="#fitur" className="hover:text-[#168A70] transition">Fitur</Link>
          <Link href="#aqi" className="hover:text-[#168A70] transition">Kualitas Udara</Link>
          <Link href="#edukasi" className="hover:text-[#168A70] transition">Edukasi ISPA</Link>
        </nav>

        <Link
          href="/skrining"
          className="bg-[#168A70] hover:bg-[#12705B] text-white px-5 py-2.5 rounded-2xl font-semibold text-sm transition shadow-sm"
        >
          Mulai Skrining
        </Link>
      </div>
    </header>
  );
}