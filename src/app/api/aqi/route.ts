import { NextRequest, NextResponse } from 'next/server';

export async function GET(request: NextRequest) {
  const searchParams = request.nextUrl.searchParams;
  const lat = searchParams.get('lat');
  const lon = searchParams.get('lon');
  const token = process.env.WAQI_API_TOKEN;

  // Jika token belum diset di .env.local
  if (!token) {
    return NextResponse.json({ error: 'WAQI_API_TOKEN belum dikonfigurasi' }, { status: 500 });
  }

  // Gunakan koordinat jika ada, fallback ke stasiun Balikpapan
  let url = lat && lon 
    ? `https://api.waqi.info/feed/geo:${lat};${lon}/?token=${token}`
    : `https://api.waqi.info/feed/balikpapan/?token=${token}`;

  try {
    const res = await fetch(url, { cache: 'no-store' });
    const data = await res.json();

    if (data.status === 'ok') {
      // Pastikan nilai AQI berupa angka valid
      const rawAqi = data.data.aqi;
      const parsedAqi = typeof rawAqi === 'number' ? rawAqi : parseInt(rawAqi, 10);

      return NextResponse.json({
        aqi: isNaN(parsedAqi) ? null : parsedAqi,
        cityName: data.data.city?.name || 'Balikpapan',
      });
    }

    // Jika geo-location gagal/stasiun terdekat offline, coba fallback ke Balikpapan
    if (lat && lon) {
      const fallbackRes = await fetch(`https://api.waqi.info/feed/balikpapan/?token=${token}`, { cache: 'no-store' });
      const fallbackData = await fallbackRes.json();
      if (fallbackData.status === 'ok') {
        const rawAqi = fallbackData.data.aqi;
        const parsedAqi = typeof rawAqi === 'number' ? rawAqi : parseInt(rawAqi, 10);
        return NextResponse.json({
          aqi: isNaN(parsedAqi) ? null : parsedAqi,
          cityName: fallbackData.data.city?.name || 'Balikpapan',
        });
      }
    }

    return NextResponse.json({ error: 'Gagal mengambil data dari WAQI' }, { status: 500 });
  } catch (error) {
    return NextResponse.json({ error: 'Terjadi kesalahan server' }, { status: 500 });
  }
}