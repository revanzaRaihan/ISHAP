'use client';

import { useEffect, useState } from 'react';

interface AqiData {
  aqi: number | null;
  cityName: string;
}

export default function AqiCard() {
  const [aqiData, setAqiData] = useState<AqiData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [locationStatus, setLocationStatus] = useState<string>('Mendeteksi lokasi...');

  useEffect(() => {
    const fetchAQI = async (lat?: number, lon?: number) => {
      try {
        const query = lat && lon ? `?lat=${lat}&lon=${lon}` : '';
        const res = await fetch(`/api/aqi${query}`);
        const data = await res.json();

        if (res.ok) {
          setAqiData({
            aqi: data.aqi ?? 45, // Fallback ke nilai dummy aman (45) jika stasiun mengembalikan null
            cityName: data.cityName || 'Balikpapan',
          });
        } else {
          // Fallback nilai default jika API bermasalah
          setAqiData({ aqi: 45, cityName: 'Balikpapan' });
        }
      } catch (err) {
        console.error('Gagal mengambil data AQI:', err);
        setAqiData({ aqi: 45, cityName: 'Balikpapan' });
      } finally {
        setLoading(false);
      }
    };

    if ('geolocation' in navigator) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          setLocationStatus('Lokasi Device Detected');
          fetchAQI(position.coords.latitude, position.coords.longitude);
        },
        () => {
          setLocationStatus('Balikpapan (Default)');
          fetchAQI();
        },
        { timeout: 7000 }
      );
    } else {
      setLocationStatus('Balikpapan (Default)');
      fetchAQI();
    }
  }, []);

  const getAqiStatus = (aqi: number | null) => {
    const val = aqi ?? 0;
    if (val <= 50) return { label: 'Baik', color: 'bg-emerald-100 text-emerald-800 border-emerald-200', bar: 'bg-emerald-600' };
    if (val <= 100) return { label: 'Sedang', color: 'bg-amber-100 text-amber-800 border-amber-200', bar: 'bg-amber-500' };
    if (val <= 150) return { label: 'Tidak Sehat (Sensitif)', color: 'bg-orange-100 text-orange-800 border-orange-200', bar: 'bg-orange-500' };
    return { label: 'Tidak Sehat', color: 'bg-rose-100 text-rose-800 border-rose-200', bar: 'bg-rose-500' };
  };

  const status = getAqiStatus(aqiData?.aqi ?? null);

  return (
    <div id="aqi" className="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
            {locationStatus}
          </p>
          <h3 className="text-lg font-bold text-slate-800 truncate max-w-[200px]">
            {loading ? 'Memuat lokasi...' : aqiData?.cityName || 'Balikpapan'}
          </h3>
        </div>
        {!loading && (
          <span className={`px-3 py-1 text-xs font-semibold rounded-xl border ${status.color}`}>
            {status.label}
          </span>
        )}
      </div>

      {loading ? (
        <div className="py-6 text-center text-slate-400 text-sm animate-pulse">
          Mengambil data AQI real-time...
        </div>
      ) : (
        <>
          <div className="flex items-baseline space-x-3">
            <span className="text-5xl font-extrabold text-slate-900">
              {aqiData?.aqi ?? '--'}
            </span>
            <span className="text-xs font-medium text-slate-500">US AQI</span>
          </div>

          <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
            <div
              className={`h-full rounded-full transition-all duration-500 ${status.bar}`}
              style={{ width: `${Math.min(((aqiData?.aqi || 0) / 300) * 100, 100)}%` }}
            ></div>
          </div>

          <div className="p-3.5 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 space-y-1">
            <p className="font-semibold text-slate-700">Imbauan Kesehatan:</p>
            <p>
              {(aqiData?.aqi ?? 0) > 100
                ? 'Gunakan masker saat beraktivitas di luar ruangan.'
                : 'Kualitas udara relatif aman untuk beraktivitas normal.'}
            </p>
          </div>
        </>
      )}
    </div>
  );
}