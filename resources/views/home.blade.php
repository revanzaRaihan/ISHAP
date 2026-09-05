@extends('layouts.app')

@section('title', 'ISHAP — Skrining Mandiri ISPA & Deteksi Dini Gejala Pernapasan')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/60 via-white to-slate-50 pt-12 pb-20 border-b border-slate-200/60">
    <div class="absolute inset-0 hero-pattern pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100/80 border border-emerald-300 text-emerald-800 text-xs font-semibold shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Sistem Skrining Mandiri ISPA Berbasis Algoritma Klinis</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15]">
                    Deteksi Dini Gejala <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 bg-clip-text text-transparent">
                        Saluran Pernapasan
                    </span> Secara Mandiri
                </h1>

                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Ketahui estimasi risiko kondisi ISPA Anda (batuk, radang tenggorokan, bronkitis, hingga pneumonia) secara cepat, aman, dan tanpa biaya. Dilengkapi penjelasan patogenesis kuman dan panduan penanganan awal.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('screening.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-xl shadow-emerald-600/25 hover:shadow-2xl transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                        <span>Mulai Skrining Sekarang</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ route('facilities.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-base font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm transition">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cari Faskes Terdekat</span>
                    </a>
                </div>

                <!-- Stats summary -->
                <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 text-center lg:text-left">
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $symptomsCount }}+</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Indikator Gejala Klinis</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">5</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Kondisi ISPA Terpetakan</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">100%</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Akses Skrining Gratis</div>
                    </div>
                </div>
            </div>

            <!-- Right Hero Card: Live AQI & Status Lingkungan -->
            <div class="lg:col-span-5">
                <div class="bg-white/95 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/60 border border-slate-200/80 backdrop-blur-sm relative overflow-hidden">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 badge-pulse"></div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Kualitas Udara</span>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">{{ $aqiData['location'] }}</span>
                    </div>

                    <!-- AQI Dial / Highlight -->
                    <div class="py-6 text-center">
                        <div class="inline-flex flex-col items-center justify-center w-32 h-32 rounded-full border-4 border-{{ $aqiData['color'] }}-400 bg-{{ $aqiData['color'] }}-50/60 shadow-inner mb-3">
                            <span class="text-3xl font-extrabold text-slate-900">{{ $aqiData['aqi'] }}</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">US AQI</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $aqiData['status'] }}</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                            PM2.5: <span class="font-semibold text-slate-700">{{ $aqiData['pm25'] }} µg/m³</span> &bull; 
                            PM10: <span class="font-semibold text-slate-700">{{ $aqiData['pm10'] }} µg/m³</span>
                        </p>
                    </div>

                    <!-- Health advisory box -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 leading-relaxed">
                        <strong class="text-slate-800 block mb-1">Saran Pencegahan Respirasi:</strong>
                        {{ $aqiData['recommendation'] }}
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span>Sumber: Open-Meteo & BMKG</span>
                        <a href="{{ route('screening.index') }}" class="font-semibold text-emerald-600 hover:text-emerald-700">Periksa Gejala Anda &rarr;</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Edukasi ISPA Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-2">Edukasi Kesehatan Pernapasan</h2>
            <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Memahami Klasifikasi ISPA: Saluran Atas vs Bawah</p>
            <p class="text-slate-600 mt-4 leading-relaxed text-sm sm:text-base">
                Infeksi Saluran Pernapasan Akut dibedakan berdasarkan lokasi anatomi organ yang diserang. Mengetahui letaknya penting untuk menentukan tingkat kegawatan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Saluran Napas Atas Card -->
            <div class="p-8 rounded-3xl bg-gradient-to-br from-emerald-50/50 to-teal-50/30 border border-emerald-100 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-emerald-600/20">
                        1
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">ISPA Saluran Napas Atas</h3>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Umumnya Ringan (Self-Limiting)</span>
                    </div>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed mb-5">
                    Meliputi organ hidung, sinus, faring, dan laring. Sebagian besar dipicu oleh virus flu musiman yang sembuh dengan sendirinya dalam 7–10 hari jika istirahat terpenuhi.
                </p>
                <div class="space-y-2.5 text-xs text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-bold">&check;</span>
                        <span><strong>Contoh:</strong> Batuk pilek biasa (Common Cold), Radang Tenggorokan (Faringitis), Sinusitis</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-bold">&check;</span>
                        <span><strong>Gejala Khas:</strong> Hidung tersumbat, bersin-bersin, tenggorokan gatal/perih, demam ringan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-bold">&check;</span>
                        <span><strong>Penanganan:</strong> Hidrasi tinggi, istirahat, kumur air garam, obat pereda gejala simptomatis</span>
                    </div>
                </div>
            </div>

            <!-- Saluran Napas Bawah Card -->
            <div class="p-8 rounded-3xl bg-gradient-to-br from-amber-50/50 to-rose-50/30 border border-amber-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-amber-600/20">
                        2
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">ISPA Saluran Napas Bawah</h3>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800">Perhatian Khusus / Potensi Gawat</span>
                    </div>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed mb-5">
                    Meliputi trakea, bronkus, dan kantung udara paru (alveoli). Infeksi di area ini dapat mengganggu pertukaran oksigen tubuh dan memerlukan evaluasi medis.
                </p>
                <div class="space-y-2.5 text-xs text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="text-amber-600 font-bold">&check;</span>
                        <span><strong>Contoh:</strong> Bronkitis Akut, Pneumonia (Radang Paru), Serangan Eksaserbasi Asma</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-amber-600 font-bold">&check;</span>
                        <span><strong>Gejala Khas:</strong> Batuk berdahak kental, sesak napas, nyeri dada saat bernapas, suara mengi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-amber-600 font-bold">&check;</span>
                        <span><strong>Penanganan:</strong> Konsultasi dokter segera, pemeriksaan saturasi oksigen, antibiotik jika ada infeksi bakteri</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Langkah Alur Skrining -->
<section class="py-20 bg-slate-50 border-y border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-2">Proses Skrining Mudah</h2>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">Hanya Butuh Waktu 2 Menit</p>
            <p class="text-slate-600 mt-2 text-sm">Alur terstruktur tanpa perlu mendaftar atau memasukkan data pribadi sensitif.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center mb-4">
                    1
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-2">Pilih Gejala yang Dirasakan</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Centang gejala yang sedang Anda alami dalam checklist interaktif (batuk, demam, sesak, tenggorokan).
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative">
                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 font-extrabold flex items-center justify-center mb-4">
                    2
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-2">Kalkulasi Perkiraan Risiko</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Algoritma scoring otomatis menghitung persentase kecocokan terhadap pola klinis ISPA tanpa bias.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 font-extrabold flex items-center justify-center mb-4">
                    3
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-2">Edukasi & Rekomendasi Faskes</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Dapatkan penjelasan patogenesis kuman, tips mandiri di rumah, serta rujukan fasilitas kesehatan terdekat.
                </p>
            </div>
        </div>

        <!-- Banner CTA -->
        <div class="mt-14 text-center">
            <a href="{{ route('screening.index') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-xl shadow-emerald-600/25 transition-all transform hover:-translate-y-0.5">
                <span>Mulai Skrining Mandiri Sekarang &rarr;</span>
            </a>
        </div>
    </div>
</section>
@endsection
