@extends('layouts.app')

@section('title', 'Fasilitas Kesehatan Terdekat (OSM) — ISHAP')

@push('styles')
<!-- Leaflet CSS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map {
        height: 400px;
        border-radius: 1.5rem;
        z-index: 10;
    }
</style>
@endpush

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-emerald-600 transition mb-3">
                &larr; Kembali ke Beranda
            </a>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Fasilitas Kesehatan Terdekat</h1>
                    <p class="text-sm text-slate-600 mt-1">
                        Pencarian real-time Rumah Sakit, Puskesmas, dan Klinik rujukan via OpenStreetMap Overpass.
                    </p>
                </div>

                <!-- GPS Location Button -->
                <button onclick="detectUserLocation()" id="gpsBtn" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition self-start md:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Gunakan Lokasi Saya (GPS)</span>
                </button>
            </div>
        </div>

        <!-- City Presets Filter -->
        <div class="mb-6 flex items-center gap-2 overflow-x-auto pb-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Pilihan Kota:</span>
            @foreach ($cityPresets as $city)
                <a href="{{ route('facilities.index', ['lat' => $city['lat'], 'lon' => $city['lon']]) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 {{ abs($lat - $city['lat']) < 0.05 && abs($lon - $city['lon']) < 0.05 ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}">
                    {{ $city['name'] }}
                </a>
            @endforeach
        </div>

        <!-- Interactive Map Card -->
        <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-sm border border-slate-200/80 mb-8">
            <div class="flex items-center justify-between mb-4 px-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-bold text-slate-900">Peta Faskes Terdekat</h3>
                </div>
                <span class="text-xs text-slate-400">Koordinat: {{ number_format($lat, 4) }}, {{ number_format($lon, 4) }}</span>
            </div>
            <div id="map"></div>
        </div>

        <!-- Facility Cards Grid -->
        <div class="space-y-4 mb-16">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">
                    Daftar Faskes Ditemukan ({{ count($facilities) }})
                </h3>
                <span class="text-xs text-slate-500 font-medium">Radius hingga {{ $radiusKm }} km</span>
            </div>

            @forelse ($facilities as $fac)
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200 hover:border-emerald-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1.5 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-base font-bold text-slate-900">{{ $fac['name'] }}</h4>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $fac['type'] }}
                            </span>
                            @if (!empty($fac['emergency']))
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-rose-50 text-rose-800 border border-rose-200">
                                    IGD 24 Jam
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $fac['address'] }}</p>
                        @if (!empty($fac['phone']))
                            <p class="text-xs text-slate-600">
                                <span class="text-slate-400">Telp:</span> {{ $fac['phone'] }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-4 shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        <div class="text-left sm:text-right">
                            <span class="text-lg font-extrabold text-emerald-600 block">{{ $fac['distance_km'] }} km</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Estimasi Jarak</span>
                        </div>
                        <a href="{{ $fac['google_maps_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition shadow-sm">
                            <span>Buka Rute</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-sm text-slate-500">Tidak ada fasilitas kesehatan ditemukan dalam radius ini.</p>
                </div>
            @endforelse
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
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // User Position Marker
    const userIcon = L.divIcon({
        className: 'user-marker',
        html: '<div style="background:#059669;width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 0 10px rgba(0,0,0,0.3)"></div>',
        iconSize: [16, 16],
        iconAnchor: [8, 8]
    });
    L.marker([userLat, userLon], { icon: userIcon }).addTo(map)
        .bindPopup('<strong>Lokasi Anda / Titik Pencarian</strong>').openPopup();

    // Facility Markers
    facilities.forEach(fac => {
        if (fac.lat && fac.lon) {
            L.marker([fac.lat, fac.lon]).addTo(map)
                .bindPopup(`
                    <div style="font-size:12px;max-width:200px">
                        <strong>${fac.name}</strong><br>
                        <span style="color:#059669">${fac.type} (${fac.distance_km} km)</span><br>
                        <a href="${fac.google_maps_url}" target="_blank" style="color:#2563eb;font-weight:bold;margin-top:4px;display:inline-block">Navigasi Google Maps &rarr;</a>
                    </div>
                `);
        }
    });

    function detectUserLocation() {
        const btn = document.getElementById('gpsBtn');
        btn.textContent = 'Mendeteksi koordinat...';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lon = pos.coords.longitude;
                    window.location.href = `{{ route('facilities.index') }}?lat=${lat}&lon=${lon}`;
                },
                (err) => {
                    alert('Gagal mendeteksi lokasi: ' + err.message);
                    btn.textContent = 'Gunakan Lokasi Saya (GPS)';
                }
            );
        } else {
            alert('Browser Anda tidak mendukung geolokasi.');
            btn.textContent = 'Gunakan Lokasi Saya (GPS)';
        }
    }
</script>
@endpush
