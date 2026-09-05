import Link from 'next/link';
import Navbar from '@/components/layout/Navbar';
import AqiCard from '@/components/layout/AqiCard';
// import FeatureCards from '@/components/layout/FeatureCards';
import Footer from '@/components/layout/Footer';

export default function Home() {
  return (
    <div className="min-h-screen bg-[#F8FAFC] text-slate-800 font-sans antialiased">
      <Navbar />

      {/* Hero Section */}
      <section className="pt-16 pb-24 max-w-6xl mx-auto px-6">
        <div className="grid lg:grid-cols-12 gap-12 items-center">
          
          {/* Kolom Teks */}
          <div className="lg:col-span-7 space-y-8">
            <span className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-950/5 text-[#0F5144] rounded-xl text-xs font-semibold border border-emerald-900/10">
              <span className="w-2 h-2 rounded-full bg-[#0F5144]"></span>
              Sistem Pencegahan ISPA Balikpapan
            </span>

            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.15] tracking-tight">
              Intelligent Screening for <span className="text-[#0F5144]">Health Awareness</span> & Prevention
            </h1>

            <p className="text-slate-600 text-base sm:text-lg leading-relaxed max-w-xl">
              Platform skrining mandiri yang cepat, tenang, dan akurat untuk mendeteksi risiko ISPA serta memantau kualitas udara secara langsung.
            </p>

            <div className="flex flex-wrap items-center gap-4 pt-2">
              <Link
                href="/skrining"
                className="bg-[#0F5144] hover:bg-[#0B3C32] text-white px-7 py-3.5 rounded-xl font-semibold text-base transition shadow-sm"
              >
                Mulai Skrining Mandiri
              </Link>
              <a
                href="#fitur"
                className="bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 px-6 py-3.5 rounded-xl font-semibold text-base transition shadow-sm"
              >
                Pelajari Fitur
              </a>
            </div>
          </div>

          {/* Kolom Widget AQI */}
          <div className="lg:col-span-5">
            <AqiCard />
          </div>

        </div>
      </section>

      {/* <FeatureCards /> */}
      <Footer />
    </div>
  );
}