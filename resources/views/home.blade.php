@extends('layouts.app')

@section('title', 'ISHAP — Layanan Skrining Mandiri ISPA')

@section('content')


    <section class="bg-white py-12 sm:py-16 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <div class="lg:col-span-7 space-y-6">

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        Deteksi Dini & Skrining Mandiri Gejala Pernapasan (ISPA)
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl">
                        Evaluasi mandiri risiko gangguan saluran pernapasan seperti batuk, flu, bronkitis, hingga indikasi
                        awal pneumonia secara cepat, aman, dan terstruktur.
                    </p>


                    <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                        <a href="{{ route('screening.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded text-sm font-semibold text-white bg-emerald-950 hover:bg-emerald-900 transition-colors shadow-sm">
                            <span>Mulai Skrining Mandiri</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="{{ route('facilities.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>Cari Faskes Terdekat</span>
                        </a>
                    </div>


                    {{-- <div class="pt-6 border-t border-slate-100 grid grid-cols-3 gap-4 text-left">
                        <div>
                            <div class="text-lg font-extrabold text-slate-900">{{ $symptomsCount }}+</div>
                            <div class="text-xs text-slate-500">Indikator Gejala</div>
                        </div>
                        <div>
                            <div class="text-lg font-extrabold text-slate-900">5</div>
                            <div class="text-xs text-slate-500">Kondisi Terdeteksi</div>
                        </div>
                        <div>
                            <div class="text-lg font-extrabold text-emerald-950">100%</div>
                            <div class="text-xs text-slate-500">Akses Tanpa Biaya</div>
                        </div>
                    </div> --}}
                </div>


                <div class="lg:col-span-5">
                    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                        <!-- Header -->
                        <!-- Header Kartu dengan Link Peta Interaktif -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span id="aqi-indicator"
                                    class="w-2.5 h-2.5 rounded-full bg-slate-300 transition-colors duration-300"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Pemantauan Kualitas
                                    Udara</span>
                            </div>

                            <!-- Link Peta AQI (Klik untuk membuka peta full) -->
                            <a href="https://aqicn.org/map/world/" target="_blank" rel="noopener noreferrer"
                                title="Klik untuk melihat peta AQI lengkap"
                                class="flex items-center gap-1.5 text-xs text-slate-600 font-medium bg-slate-50 hover:bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 transition-colors group cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#0F5144] transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span id="aqi-location" class="group-hover:text-slate-900 group-hover:underline">Mendeteksi
                                    lokasi...</span>
                                <!-- Ikon Eksternal Kecil -->
                                <svg class="w-3 h-3 text-slate-400 group-hover:text-[#0F5144]" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>

                        <!-- Body Main Info -->
                        <div class="py-5 flex items-center justify-between gap-4">
                            <div>
                                <div class="text-4xl font-black tracking-tight text-slate-900">
                                    <span id="aqi-value">-</span>
                                    <span class="text-xs font-semibold text-slate-400 tracking-normal">US AQI</span>
                                </div>
                                <div class="mt-1">
                                    <span id="aqi-status"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Memuat...
                                    </span>
                                </div>
                            </div>

                            <!-- Particle Indicators -->
                            <div class="grid grid-cols-2 gap-2 text-right">
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">PM
                                        2.5</span>
                                    <span class="text-xs font-bold text-slate-800">
                                        <span id="aqi-pm25">-</span> <span
                                            class="text-[10px] font-normal text-slate-500">µg/m³</span>
                                    </span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">PM
                                        10</span>
                                    <span class="text-xs font-bold text-slate-800">
                                        <span id="aqi-pm10">-</span> <span
                                            class="text-[10px] font-normal text-slate-500">µg/m³</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Anjuran Medis -->
                        <div
                            class="p-3.5 bg-slate-50/80 border border-slate-200/80 rounded-lg text-xs leading-relaxed mb-4">
                            <div class="flex items-center gap-1.5 font-bold text-slate-900 mb-1">
                                <svg class="w-4 h-4 text-[#0F5144]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Anjuran Pencegahan Medis</span>
                            </div>
                            <p id="aqi-recommendation" class="text-slate-600 pl-5">
                                Mencari akses lokasi perangkat Anda...
                            </p>
                        </div>

                        <!-- Footer Info -->
                        <div
                            class="flex items-center justify-between text-[11px] text-slate-400 pt-3 border-t border-slate-100">
                            <span>Sumber: WAQI Real-time</span>
                            <a href="{{ route('screening.index') }}"
                                class="inline-flex items-center gap-1 font-semibold text-[#0F5144] hover:text-slate-900 transition-colors">
                                <span>Periksa Gejala</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="py-12 bg-slate-50/60 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-8">
                <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-950 mb-1">Edukasi Medis</h2>
                <h3 class="text-2xl font-bold text-slate-900">Klasifikasi Infeksi Saluran Pernapasan Akut</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Saluran Atas -->
                <div class="bg-white p-6 rounded border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <span
                            class="w-7 h-7 rounded bg-emerald-950 text-white font-bold text-xs flex items-center justify-center">1</span>
                        <div>
                            <h4 class="text-base font-bold text-slate-900">ISPA Saluran Napas Atas</h4>
                            <span class="text-[11px] font-medium text-slate-600">Umumnya Ringan (Hidung, Faring,
                                Laring)</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Infeksi pada bagian atas saluran pernapasan. Sebagian besar dipicu oleh virus dan dapat membaik
                        dengan penanganan mandiri yang tepat.
                    </p>
                    <div class="space-y-1.5 text-xs text-slate-700 border-t border-slate-100 pt-3">
                        <div><strong>Contoh:</strong> Batuk pilek biasa, Faringitis (Radang tenggorokan), Sinusitis.</div>
                        <div><strong>Gejala Utama:</strong> Hidung tersumbat, bersin, tenggorokan perih, demam ringan.</div>
                    </div>
                </div>

                <!-- Saluran Bawah -->
                <div class="bg-white p-6 rounded border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <span
                            class="w-7 h-7 rounded bg-emerald-950 text-white font-bold text-xs flex items-center justify-center">2</span>
                        <div>
                            <h4 class="text-base font-bold text-slate-900">ISPA Saluran Napas Bawah</h4>
                            <span class="text-[11px] font-medium text-slate-600">Perhatian Medis Khusus (Bronkus,
                                Paru)</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Infeksi pada jaringan paru-paru dan saluran napas bawah. Memerlukan perhatian lebih karena dapat
                        mengganggu masuknya oksigen.
                    </p>
                    <div class="space-y-1.5 text-xs text-slate-700 border-t border-slate-100 pt-3">
                        <div><strong>Contoh:</strong> Bronkitis Akut, Pneumonia (Radang Paru).</div>
                        <div><strong>Gejala Utama:</strong> Batuk berdahak kental, sesak napas, nyeri dada saat bernapas.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Langkah Alur Skrining -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-8">
                <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-950 mb-1">Alur Pelayanan</h2>
                <h3 class="text-2xl font-bold text-slate-900">Prosedur Skrining Mandiri</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-5 rounded border border-slate-200 bg-white">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Langkah 01</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Pilih Indikator Gejala</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tandai gejala yang dirasakan saat ini pada lembar checklist interaktif.
                    </p>
                </div>

                <div class="p-5 rounded border border-slate-200 bg-white">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Langkah 02</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Kalkulasi Otomatis</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sistem menganalisis tingkat kecocokan gejala dengan pola klinis ISPA.
                    </p>
                </div>

                <div class="p-5 rounded border border-slate-200 bg-white">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Langkah 03</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Hasil & Panduan Medis</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Dapatkan estimasi kondisi, saran penanganan awal, serta rujukan faskes.
                    </p>
                </div>
            </div>

            
            <div
                class="mt-8 p-6 rounded border border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="text-base font-bold text-slate-900">Mulai Skrining Kesehatan Pernapasan</h4>
                    <p class="text-xs text-slate-600 mt-0.5">Proses cepat, tidak berbayar, dan tanpa perlu mendaftar akun.
                    </p>
                </div>
                <a href="{{ route('screening.index') }}"
                    class="px-5 py-2.5 rounded text-xs font-semibold text-white bg-emerald-950 hover:bg-emerald-900 transition-colors whitespace-nowrap">
                    Mulai Skrining Sekarang &rarr;
                </a>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const elLocation = document.getElementById("aqi-location");
            const elValue = document.getElementById("aqi-value");
            const elStatus = document.getElementById("aqi-status");
            const elPm25 = document.getElementById("aqi-pm25");
            const elPm10 = document.getElementById("aqi-pm10");
            const elRecommendation = document.getElementById("aqi-recommendation");
            const elIndicator = document.getElementById("aqi-indicator");

            function setEmptyState(msg) {
                elLocation.textContent = "Akses Lokasi Ditolak";
                elValue.textContent = "-";
                elStatus.textContent = "Tidak Ada Data";
                elStatus.className =
                    "inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200";
                elPm25.textContent = "-";
                elPm10.textContent = "-";
                elRecommendation.textContent = msg ||
                    "Izinkan akses lokasi pada browser Anda untuk melihat pemantauan indeks kualitas udara setempat.";
                elIndicator.className = "w-2.5 h-2.5 rounded-full bg-slate-400";
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    async (pos) => {
                            try {
                                const res = await fetch(
                                    `/api/aqi?lat=${pos.coords.latitude}&lng=${pos.coords.longitude}`);
                                const json = await res.json();

                                if (json.success && json.data) {
                                    const data = json.data;
                                    const aqi = data.aqi;

                                    elLocation.textContent = data.city.name || "Lokasi Terdeteksi";
                                    elValue.textContent = aqi;
                                    elPm25.textContent = data.iaqi?.pm25?.v ?? "-";
                                    elPm10.textContent = data.iaqi?.pm10?.v ?? "-";


                                    if (aqi <= 50) {
                                        elStatus.textContent = "Baik";
                                        elStatus.className =
                                            "inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-200";
                                        elIndicator.className = "w-2.5 h-2.5 rounded-full bg-emerald-600";
                                        elRecommendation.textContent =
                                            "Kualitas udara sangat baik. Aman untuk beraktivitas di luar ruangan tanpa masker.";
                                    } else if (aqi <= 100) {
                                        elStatus.textContent = "Sedang";
                                        elStatus.className =
                                            "inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-900 border border-yellow-200";
                                        elIndicator.className = "w-2.5 h-2.5 rounded-full bg-yellow-500";
                                        elRecommendation.textContent =
                                            "Kualitas udara relatif aman. Kelompok sensitif disarankan mengurangi aktivitas luar ruangan berlebih.";
                                    } else {
                                        elStatus.textContent = "Tidak Sehat";
                                        elStatus.className =
                                            "inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-900 border border-rose-200";
                                        elIndicator.className = "w-2.5 h-2.5 rounded-full bg-rose-600";
                                        elRecommendation.textContent =
                                            "Gunakan masker medis saat beraktivitas di luar rumah untuk mencegah risiko iritasi saluran pernapasan.";
                                    }
                                } else {
                                    setEmptyState("Gagal mengambil data dari stasiun WAQI.");
                                }
                            } catch (e) {
                                setEmptyState("Koneksi server terganggu.");
                            }
                        },
                        () => setEmptyState("Akses lokasi tidak diizinkan oleh pengguna."), {
                            timeout: 8000
                        }
                );
            } else {
                setEmptyState("Browser tidak mendukung geolocation.");
            }
        });
    </script>

@endsection
