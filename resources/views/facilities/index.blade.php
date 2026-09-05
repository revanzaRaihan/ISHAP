@extends('layouts.app')

@section('title', 'Fasilitas Kesehatan Terdekat — ISHAP')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 320px;
        border-radius: 0.5rem;
        z-index: 10;
    }
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endpush

@section('content')
<div class="py-6 bg-slate-50 min-h-screen text-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Navigation & Header -->
        <div class="mb-5">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Fasilitas Kesehatan Terdekat</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Pemetaan lokasi Rumah Sakit, Puskesmas, dan Klinik rujukan.</p>
                </div>

                <button onclick="detectUserLocation()" id="gpsBtn" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-md bg-[#0F5144] hover:bg-[#0B3C32] text-white text-xs font-bold transition border border-[#0B3C32] shrink-0 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Gunakan GPS Saya</span>
                </button>
            </div>
        </div>

        <!-- Filter Kota (Pills Horizontal) -->
        <div class="mb-5 flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Kota:</span>
            @foreach ($cityPresets as $city)
                <a href="{{ route('facilities.index', ['lat' => $city['lat'], 'lon' => $city['lon']]) }}" 
                   class="px-3 py-1 rounded text-xs font-semibold transition shrink-0 border {{ abs($lat - $city['lat']) < 0.05 && abs($lon - $city['lon']) < 0.05 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    {{ $city['name'] }}
                </a>
            @endforeach
        </div>

        <!-- Layout Grid Utama (Peta & List Faskes) -->
        <div class="space-y-5">
            
            <!-- Map Container -->
            <div class="bg-white rounded-lg p-3 border border-slate-200">
                <div class="flex items-center justify-between mb-2.5 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#0F5144]"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Peta Sebaran Faskes</span>
                    </div>
                    <span class="text-[11px] font-mono text-slate-400">{{ number_format($lat, 4) }}, {{ number_format($lon, 4) }}</span>
                </div>
                <div id="map" class="border border-slate-200"></div>
            </div>

            <!-- List Faskes Header -->
            <div class="flex items-center justify-between pt-2 border-b border-slate-200 pb-2">
                <h2 class="text-sm font-bold text-slate-900">
                    Daftar Faskes <span class="text-slate-500 font-normal">({{ count($facilities) }} Ditemukan)</span>
                </h2>
                <span class="text-xs text-slate-400">Radius ~{{ $radiusKm }} km</span>
            </div>

            <!-- Grid Kartu Faskes (2 Kolom Desktop) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-10">
                @forelse ($facilities as $fac)
                    <div class="bg-white rounded-lg p-4 border border-slate-200 hover:border-slate-400 transition-colors flex flex-col justify-between gap-3">
                        <div class="space-y-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $fac['name'] }}</h3>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-[#0F5144] border border-slate-200 shrink-0">
                                    {{ $fac['type'] }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 leading-relaxed">{{ $fac['address'] }}</p>

                            <div class="flex items-center gap-2 text-[11px] text-slate-600 pt-1">
                                @if (!empty($fac['emergency']))
                                    <span class="font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 text-[10px]">
                                        IGD 24 Jam
                                    </span>
                                @endif
                                @if (!empty($fac['phone']))
                                    <span class="text-slate-400">Telp: <strong class="text-slate-700">{{ $fac['phone'] }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <div>
                                <span class="text-xs font-bold text-[#0F5144]">{{ $fac['distance_km'] }} km</span>
                                <span class="text-[10px] text-slate-400 uppercase block font-semibold">Jarak</span>
                            </div>
                            <a href="{{ $fac['google_maps_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-slate-900 hover:bg-[#0F5144] text-white text-xs font-bold transition">
                                <span>Petunjuk Rute</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-white rounded-lg border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium">Tidak ada fasilitas kesehatan ditemukan dalam radius ini.</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const userLat = {{ $lat }};
    const userLon = {{ $lon }};
    const facilities = @json($facilities);

    // Initialize Map
    const map = L.map('map').setView([userLat, userLon], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Pin Lokasi User (Titik Minimalis)
    const userIcon = L.divIcon({
        className: 'user-marker',
        html: '<div style="background:#0F5144;width:12px;height:12px;border-radius:50%;border:2px solid white;box-shadow:0 1px 3px rgba(0,0,0,0.3)"></div>',
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });
    L.marker([userLat, userLon], { icon: userIcon }).addTo(map)
        .bindPopup('<strong>Titik Lokasi Anda</strong>').openPopup();

    // Pin Faskes
    facilities.forEach(fac => {
        if (fac.lat && fac.lon) {
            L.marker([fac.lat, fac.lon]).addTo(map)
                .bindPopup(`
                    <div style="font-size:11px;max-width:180px">
                        <strong>${fac.name}</strong><br>
                        <span style="color:#0F5144">${fac.type} (${fac.distance_km} km)</span><br>
                        <a href="${fac.google_maps_url}" target="_blank" style="color:#0F5144;font-weight:bold;margin-top:4px;display:inline-block">Navigasi Rute &rarr;</a>
                    </div>
                `);
        }
    });

    function detectUserLocation() {
        const btn = document.getElementById('gpsBtn');
        btn.textContent = 'Mendeteksi...';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lon = pos.coords.longitude;
                    window.location.href = `{{ route('facilities.index') }}?lat=${lat}&lon=${lon}`;
                },
                (err) => {
                    alert('Gagal mendeteksi lokasi: ' + err.message);
                    btn.textContent = 'Gunakan GPS Saya';
                }
            );
        } else {
            alert('Browser Anda tidak mendukung geolokasi.');
            btn.textContent = 'Gunakan GPS Saya';
        }
    }
</script>
@endpush