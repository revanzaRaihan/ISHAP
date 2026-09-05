export default function AqiCard() {
  return (
    <div id="aqi" className="bg-white p-8 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(0,0,0,0,0.03)] space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Wilayah Balikpapan</p>
          <h3 className="text-xl font-bold text-slate-800">Kualitas Udara Saat Ini</h3>
        </div>
        <span className="px-3.5 py-1.5 bg-warningSoft text-warningText text-xs font-bold rounded-2xl">
          Sedang (Moderate)
        </span>
      </div>

      <div className="flex items-baseline space-x-3">
        <span className="text-6xl font-extrabold text-slate-800">85</span>
        <span className="text-sm font-medium text-slate-400">US AQI</span>
      </div>

      <div className="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
        <div className="bg-amber-400 h-full rounded-full" style={{ width: '42%' }}></div>
      </div>

      <div className="p-4 bg-secondaryBlue/60 rounded-2xl border border-sky-100/50 text-xs text-slate-600 space-y-1">
        <p className="font-semibold text-slate-700">Rekomendasi Kesehatan:</p>
        <p>Masyarakat rentan (anak-anak, lansia, & penderita asthma) disarankan mengurangi aktivitas outdoor yang berat.</p>
      </div>
    </div>
  );
}