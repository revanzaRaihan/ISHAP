@extends('layouts.app')

@section('title', 'Health Report Card — ISHAP')

@section('content')
<div class="py-10 bg-gradient-to-b from-slate-50 via-emerald-50/20 to-white min-h-screen relative overflow-hidden">
    
    <!-- Subtle Background Organic Wave Accent -->
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-emerald-100/40 blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full bg-teal-100/30 blur-3xl pointer-events-none"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 relative">
        
        <!-- Top Action Bar -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('screening.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-[#0F5144] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Skrining Ulang</span>
            </a>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-slate-200 bg-white/90 backdrop-blur-sm text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Resume</span>
                </button>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-100/80 text-[#0F5144] border border-emerald-200 font-mono">
                    #{{ substr($session->id, 0, 8) }}
                </span>
            </div>
        </div>

        @if (!$topResult)
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 shadow-sm max-w-lg mx-auto">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    !
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Tidak Ditemukan Pola Spesifik</h2>
                <p class="text-xs text-slate-500 mb-6">
                    Gejala yang Anda laporkan belum mencukupi kriteria klinis ISPA dalam sistem skrining kami.
                </p>
                <a href="{{ route('screening.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0F5144] text-white text-xs font-bold hover:bg-[#0B3C32] transition">
                    Pilih Gejala Lain
                </a>
            </div>
        @else
            @php
                $disease = $topResult->disease;
                $severity = strtolower($disease->severity_level ?? 'ringan');
                $isSevere = $severity === 'berat';
                $isModerate = $severity === 'sedang';

                // Hitung skor titik indikator (skala 1-5 dots seperti di gambar referensi)
                $scoreDots = min(5, max(1, (int) round(($topResult->confidence_score / 100) * 5)));
                $severityDots = match($severity) {
                    'berat' => 5,
                    'sedang' => 3,
                    default => 1,
                };

                $diseaseNameLower = strtolower($disease->name ?? '');
                $primaryTarget = 'lungs';
                $primaryTargetName = 'Paru-Paru (Pulmo)';
                if (str_contains($diseaseNameLower, 'cold') || str_contains($diseaseNameLower, 'pilek') || str_contains($diseaseNameLower, 'rhinitis')) {
                    $primaryTarget = 'nasal';
                    $primaryTargetName = 'Rongga Hidung & Sinus';
                } elseif (str_contains($diseaseNameLower, 'faringitis') || str_contains($diseaseNameLower, 'tenggorokan')) {
                    $primaryTarget = 'pharynx';
                    $primaryTargetName = 'Faring & Amandel';
                } elseif (str_contains($diseaseNameLower, 'bronkitis')) {
                    $primaryTarget = 'bronchi';
                    $primaryTargetName = 'Percabangan Bronkus';
                } elseif (str_contains($diseaseNameLower, 'pneumonia')) {
                    $primaryTarget = 'alveoli';
                    $primaryTargetName = 'Kantung Udara Alveolus';
                } elseif (str_contains($diseaseNameLower, 'asma')) {
                    $primaryTarget = 'bronchi';
                    $primaryTargetName = 'Bronkus & Saluran Napas';
                }
            @endphp

            <!-- Hero Section: Headline & Body Silhouette Banner -->
            <div class="mb-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    <!-- Kolom Teks Utama -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-[#0F5144] text-[11px] font-extrabold uppercase tracking-wider">
                            <span>Laporan Skrining Mandiri</span>
                            <span>&bull;</span>
                            <span>ISHAP Clinical Report</span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            Hasil Evaluasi Kondisi <br>
                            <span class="text-[#0F5144] underline decoration-emerald-300 underline-offset-4">{{ $disease->name }}</span>
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-lg">
                            {{ $disease->description }}
                        </p>

                        <!-- Reported Symptom Chips -->
                        <div class="pt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] font-bold text-slate-400 mr-1">Gejala Terdeteksi:</span>
                            @foreach ($session->symptoms as $sym)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $sym->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kolom Visual: Silhouette Manusia Bersih & Concentric Rings (Sesuai Referensi Gambar) -->
                    <div class="lg:col-span-5 flex justify-center relative">
                        <!-- Concentric Ripples -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-64 h-64 rounded-full border border-emerald-200/60 animate-pulse"></div>
                            <div class="w-48 h-48 rounded-full border border-emerald-200/80 absolute"></div>
                            <div class="w-32 h-32 rounded-full border border-emerald-300/80 absolute"></div>
                        </div>

                        <!-- Clean Lime/Emerald Silhouette with Vital Organ Heartbeat -->
                        <div class="relative z-10 w-full max-w-[280px] flex flex-col items-center">
                            <svg viewBox="0 0 360 440" class="w-full h-auto select-none" id="cleanAnatomicalSvg">
                                <!-- Silhouette Tubuh Manusia Bersih (Light Lime/Emerald Aesthetic) -->
                                <path d="M 180,30 C 158,30 148,50 148,76 C 148,102 160,118 165,126 C 138,132 105,148 88,185 C 70,225 65,300 65,420 L 295,420 C 295,300 290,225 272,185 C 255,148 222,132 195,126 C 200,118 212,102 212,76 C 212,50 202,30 180,30 Z" 
                                      fill="#dcfce7" stroke="#86efac" stroke-width="2.5" />

                                <!-- Jantung / Titik Vital Berpendar di Tengah Tubuh (Sesuai Gambar) -->
                                <circle cx="180" cy="195" r="32" fill="#bbf7d0" fill-opacity="0.6" stroke="#4ade80" stroke-width="1.5" />
                                
                                <!-- Saluran Napas Vektor Transparan di Dalam Siluet -->
                                <g stroke="#15803d" stroke-width="2" fill="none" opacity="0.6">
                                    <!-- Hidung ke Faring -->
                                    <path d="M 180,70 L 180,135" />
                                    <!-- Percabangan Bronkus -->
                                    <path d="M 180,135 Q 165,150 145,170" stroke-width="3" />
                                    <path d="M 180,135 Q 195,150 215,170" stroke-width="3" />
                                    <!-- Paru Kanan & Kiri -->
                                    <path d="M 140,165 C 120,160 95,180 90,220 C 85,260 95,300 120,320 C 135,330 155,310 155,270 Z" fill="#bbf7d0" fill-opacity="0.4" stroke="#22c55e" stroke-width="1.5" />
                                    <path d="M 220,165 C 240,160 265,180 270,220 C 275,260 265,300 240,320 C 225,330 205,310 205,270 Z" fill="#bbf7d0" fill-opacity="0.4" stroke="#22c55e" stroke-width="1.5" />
                                </g>

                                <!-- Organ Inflamasi Aktif (Pulsing Pin Target Sesuai Penyakit) -->
                                <g id="activeVitalTarget" class="transition-all duration-300">
                                    <circle id="cleanPointerTarget" cx="180" cy="130" r="16" fill="#f43f5e" fill-opacity="0.25" stroke="#f43f5e" stroke-width="2" class="animate-ping" />
                                    <circle id="cleanPointerCore" cx="180" cy="130" r="8" fill="#e11d48" stroke="#ffffff" stroke-width="2" />
                                    <!-- Icon Pulse ECG di titik vital -->
                                    <path d="M 175,130 L 178,130 L 180,126 L 182,134 L 184,130 L 186,130" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                            </svg>

                            <!-- Organ Selector Pills (Minimalis) -->
                            <div class="flex flex-wrap justify-center gap-1 mt-2 bg-white/80 backdrop-blur-sm p-1.5 rounded-full border border-emerald-200/80 shadow-xs">
                                <button type="button" onclick="selectCleanOrgan('nasal')" id="btn_nasal" class="clean-tab px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-500 hover:text-slate-900 transition">
                                    Hidung
                                </button>
                                <button type="button" onclick="selectCleanOrgan('pharynx')" id="btn_pharynx" class="clean-tab px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-500 hover:text-slate-900 transition">
                                    Faring
                                </button>
                                <button type="button" onclick="selectCleanOrgan('bronchi')" id="btn_bronchi" class="clean-tab px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-500 hover:text-slate-900 transition">
                                    Bronkus
                                </button>
                                <button type="button" onclick="selectCleanOrgan('lungs')" id="btn_lungs" class="clean-tab px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-500 hover:text-slate-900 transition">
                                    Paru-Paru
                                </button>
                                <button type="button" onclick="selectCleanOrgan('alveoli')" id="btn_alveoli" class="clean-tab px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-500 hover:text-slate-900 transition">
                                    Alveolus
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- THE HEALTH REPORT CARD (Dengan Spacing-Y Antar Teks yang Nyaman & Lega) -->
            <div class="bg-white rounded-3xl p-6 sm:p-9 border border-slate-200/80 shadow-xl shadow-emerald-950/5 mb-8 relative">
                
                <!-- Card Header (Clipboard Icon + Title + Subtitle dengan Spacing-Y Lega) -->
                <div class="flex items-center gap-4 pb-6 mb-2 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-[#0F5144] flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 leading-tight">Health Report Card</h2>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium leading-normal">Evaluasi Parameter Klinis Pernapasan Anda</p>
                    </div>
                </div>

                <!-- Structured Metric Rows (Spacing Y Lega Antar Baris & Antar Teks) -->
                <div class="divide-y divide-slate-100 text-sm">
                    
                    <!-- Row 1: Tingkat Kecocokan Gejala -->
                    <div class="py-5 sm:py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-800 block text-sm sm:text-base leading-snug">Kecocokan Gejala</span>
                                <span class="text-xs sm:text-sm text-slate-400 block leading-normal">Tingkat indikasi klinis dengan database</span>
                            </div>
                        </div>

                        <!-- Circular Rating Dots + Percentage -->
                        <div class="flex items-center gap-4 self-end sm:self-auto pt-1 sm:pt-0">
                            <div class="flex items-center gap-1.5" title="{{ $topResult->confidence_score }}%">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="w-4 h-4 rounded-full border-2 {{ $i <= $scoreDots ? 'bg-emerald-500 border-emerald-500 shadow-2xs' : 'bg-white border-slate-300' }}"></span>
                                @endfor
                            </div>
                            <span class="text-base sm:text-lg font-black text-[#0F5144] min-w-[55px] text-right font-mono">
                                {{ $topResult->confidence_score }}%
                            </span>
                        </div>
                    </div>

                    <!-- Row 2: Tingkat Risiko & Keparahan -->
                    <div class="py-5 sm:py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-800 block text-sm sm:text-base leading-snug">Tingkat Risiko</span>
                                <span class="text-xs sm:text-sm text-slate-400 block leading-normal">Klasifikasi tingkat keparahan infeksi</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 self-end sm:self-auto pt-1 sm:pt-0">
                            <div class="flex items-center gap-1.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="w-4 h-4 rounded-full border-2 {{ $i <= $severityDots ? ($isSevere ? 'bg-rose-500 border-rose-500' : ($isModerate ? 'bg-amber-500 border-amber-500' : 'bg-emerald-500 border-emerald-500')) : 'bg-white border-slate-300' }}"></span>
                                @endfor
                            </div>
                            <span class="text-xs font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider min-w-[95px] text-center {{ $isSevere ? 'bg-rose-100 text-rose-800' : ($isModerate ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-[#0F5144]') }}">
                                Risiko {{ ucfirst($severity) }}
                            </span>
                        </div>
                    </div>

                    <!-- Row 3: Fokus Organ Inflamasi -->
                    <div class="py-5 sm:py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-800 block text-sm sm:text-base leading-snug">Fokus Organ</span>
                                <span class="text-xs sm:text-sm text-slate-400 block leading-normal" id="cardOrganCategory">Saluran Napas</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-auto pt-1 sm:pt-0">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200" id="cardOrganTitle">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                {{ $primaryTargetName }}
                            </span>
                        </div>
                    </div>

                    <!-- Row 4: Jalur Transmisi Patogen -->
                    <div class="py-5 sm:py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-800 block text-sm sm:text-base leading-snug">Jalur Penularan</span>
                                <span class="text-xs sm:text-sm text-slate-400 block leading-normal">Rute masuk mikroorganisme</span>
                            </div>
                        </div>

                        <span class="text-xs font-semibold text-slate-700 bg-slate-50 px-3.5 py-1.5 rounded-full border border-slate-200 self-end sm:self-auto max-w-md text-right">
                            {{ $disease->pathogenesis_causes[0] ?? 'Droplet Udara & Kontak Langsung' }}
                        </span>
                    </div>

                    <!-- Row 5: Rekomendasi Tindakan -->
                    <div class="py-5 sm:py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-[#0F5144] flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <span class="font-bold text-slate-800 block text-sm sm:text-base leading-snug">Tindakan Medis</span>
                                <span class="text-xs sm:text-sm text-slate-400 block leading-normal">Rekomendasi langkah perawatan</span>
                            </div>
                        </div>

                        <span class="text-xs font-bold px-3.5 py-1.5 rounded-full border self-end sm:self-auto {{ $isSevere ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-[#0F5144] border-emerald-200' }}">
                            {{ $isSevere ? 'Pemeriksaan Klinis di Faskes' : 'Perawatan Mandiri di Rumah' }}
                        </span>
                    </div>

                </div>

                <!-- Anjuran & Peringatan Mini Chips Bar (Dengan Spacing Lega) -->
                <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs">
                        <div class="font-bold text-[#0F5144] flex items-center gap-2 mb-2">
                            <span class="w-4 h-4 rounded-full bg-emerald-200 text-[#0F5144] flex items-center justify-center text-[10px] font-black">✓</span>
                            <span class="text-xs sm:text-sm">Anjuran Perawatan Utama:</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed text-xs sm:text-sm">
                            {{ $disease->recovery_tips[0] ?? 'Istirahat cukup 7-8 jam dan perbanyak hidrasi air hangat.' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-100 text-xs">
                        <div class="font-bold text-rose-800 flex items-center gap-2 mb-2">
                            <span class="w-4 h-4 rounded-full bg-rose-200 text-rose-800 flex items-center justify-center text-[10px] font-black">!</span>
                            <span class="text-xs sm:text-sm">Segera ke Faskes Bila:</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed text-xs sm:text-sm">
                            {{ $disease->red_flags[0] ?? 'Napas terasa sesak berat atau demam tinggi persisten >39°C.' }}
                        </p>
                    </div>
                </div>

            </div>

            <!-- Kemungkinan Kondisi Lainnya (Minimalist Pill Strip) -->
            @if ($secondaryResults->isNotEmpty())
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs mb-8">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kemungkinan Kondisi Lainnya</span>
                        <span class="text-xs text-slate-400">Diferensial sekunder</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                        @foreach ($secondaryResults->take(3) as $sec)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs flex items-center justify-between gap-2">
                                <div class="truncate">
                                    <span class="font-bold text-slate-800 block truncate">{{ $sec->disease->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $sec->disease->severity_level ?? 'Ringan' }}</span>
                                </div>
                                <span class="font-mono font-bold text-slate-700 shrink-0 px-2 py-0.5 rounded bg-white border border-slate-200">
                                    {{ $sec->confidence_score }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif


            <!-- Langkah Rujukan Lanjutan: Faskes Terdekat & Telemedika Halodoc (Compact Card Design) -->
            <div class="mb-8">
                <div class="flex items-center justify-between gap-3 mb-4 pb-2 border-b border-slate-200/80">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#0F5144] block">Langkah Rujukan Lanjutan</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Rekomendasi Penanganan Klinis & Konsultasi Online</h3>
                    </div>
                    <span class="text-xs text-slate-400 hidden sm:block">Akses Faskes & Telemedika</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Card 1: Faskes Fisik Terdekat (Compact Card dengan Foto Faskes) -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start gap-3.5 mb-3">
                            <!-- Foto Faskes -->
                            <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden shrink-0 border border-slate-100 shadow-2xs bg-slate-100">
                                <img src="https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?w=300&auto=format&fit=crop&q=80" 
                                     alt="Foto Fasilitas Kesehatan" 
                                     class="w-full h-full object-cover">
                                @if (!empty($nearestFacility['distance_km']))
                                    <span class="absolute bottom-1 left-1 bg-slate-900/80 backdrop-blur-xs text-emerald-300 text-[10px] font-bold px-1.5 py-0.5 rounded">
                                        {{ $nearestFacility['distance_km'] }} km
                                    </span>
                                @endif
                            </div>

                            <!-- Info Faskes -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 text-[#0F5144] border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $nearestFacility['type'] ?? 'Fasilitas Kesehatan' }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 leading-snug truncate" title="{{ $nearestFacility['name'] ?? 'RSUP Persahabatan' }}">
                                    {{ $nearestFacility['name'] ?? 'RSUP Persahabatan' }}
                                </h4>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-1 leading-normal">
                                    {{ $nearestFacility['address'] ?? 'Dekat lokasi koordinat Anda' }}
                                </p>
                            </div>
                        </div>

                        <!-- Tombol Aksi Faskes -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ $nearestFacility['google_maps_url'] ?? route('facilities.index') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-900 bg-emerald-400 hover:bg-emerald-300 transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span>Rute Google Maps</span>
                            </a>
                            <a href="{{ route('facilities.index') }}" class="text-[11px] font-semibold text-slate-500 hover:text-[#0F5144] transition">
                                Cari Faskes Lain &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Halodoc Telemedicine (Compact Specialist Deep Link Card) -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start gap-3.5 mb-3">
                            <!-- Foto Dokter Spesialis -->
                            <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden shrink-0 border border-slate-100 shadow-2xs bg-slate-100">
                                <img src="{{ $specialistCategory['image'] ?? 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=300&auto=format&fit=crop&q=80' }}" 
                                     alt="Foto Dokter Spesialis Halodoc" 
                                     class="w-full h-full object-cover">
                                <span class="absolute bottom-1 right-1 bg-slate-900/80 backdrop-blur-xs text-emerald-400 text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Online
                                </span>
                            </div>

                            <!-- Info Spesialis -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-100">
                                        Telemedika &bull; Halodoc
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 leading-snug truncate" title="{{ $specialistCategory['title'] ?? 'Dokter Spesialis' }}">
                                    {{ $specialistCategory['title'] ?? 'Dokter Spesialis THT / Paru' }}
                                </h4>
                                <p class="text-xs text-emerald-700 font-semibold mt-0.5">
                                    {{ $specialistCategory['specialty'] ?? 'Konsultasi Spesialis' }}
                                </p>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-1 leading-normal">
                                    {{ $specialistCategory['description'] ?? 'Konsultasi online via chat & video call langsung di Halodoc.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Tombol Aksi Deep Link Spesialis -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ $specialistCategory['url'] ?? 'https://www.halodoc.com/tanya-dokter/kategori/kesehatan-paru' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span>Pilih {{ $specialistCategory['title'] ?? 'Dokter Spesialis' }}</span>
                            </a>
                            @if (!empty($specialistCategory['alternate_url']))
                                <a href="{{ $specialistCategory['alternate_url'] }}" target="_blank" rel="noopener noreferrer" class="text-[11px] font-semibold text-slate-500 hover:text-rose-600 transition truncate">
                                    Opsi {{ $specialistCategory['alternate_title'] }} &rarr;
                                </a>
                            @else
                                <a href="https://www.halodoc.com/tanya-dokter" target="_blank" rel="noopener noreferrer" class="text-[11px] font-semibold text-slate-500 hover:text-rose-600 transition">
                                    Kategori Lain &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <!-- Disclaimer Bawah Minimalis -->
            <div class="text-center text-xs text-slate-400 py-1">
                <p>⚠️ <strong>Pemberitahuan:</strong> Hasil skrining ini adalah instrumen asesmen risiko awal berdasarkan referensi klinis ISPA, bukan pengganti diagnosis langsung dokter.</p>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    const primaryInitialTarget = "{{ $primaryTarget ?? 'lungs' }}";

    const cleanOrganCoordinates = {
        nasal: {
            title: 'Rongga Hidung & Sinus',
            category: 'Saluran Napas Atas',
            pointer: { x: 180, y: 75 }
        },
        pharynx: {
            title: 'Faring & Amandel',
            category: 'Saluran Napas Atas',
            pointer: { x: 180, y: 110 }
        },
        bronchi: {
            title: 'Percabangan Bronkus',
            category: 'Saluran Napas Bawah',
            pointer: { x: 160, y: 155 }
        },
        lungs: {
            title: 'Paru-Paru (Lobus Pulmo)',
            category: 'Saluran Napas Bawah',
            pointer: { x: 230, y: 230 }
        },
        alveoli: {
            title: 'Kantung Udara Alveolus',
            category: 'Mikroskopis Respirasi',
            pointer: { x: 220, y: 280 }
        }
    };

    function selectCleanOrgan(key) {
        const organ = cleanOrganCoordinates[key];
        if (!organ) return;

        // Update Text inside the Health Report Card Row
        const cardTitle = document.getElementById('cardOrganTitle');
        const cardCategory = document.getElementById('cardOrganCategory');
        if (cardTitle) {
            cardTitle.innerHTML = `<span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span> ${organ.title}`;
        }
        if (cardCategory) {
            cardCategory.textContent = organ.category;
        }

        // Update Pointer Pin in Silhouette
        const pointerTarget = document.getElementById('cleanPointerTarget');
        const pointerCore = document.getElementById('cleanPointerCore');
        if (pointerTarget && pointerCore) {
            pointerTarget.setAttribute('cx', organ.pointer.x);
            pointerTarget.setAttribute('cy', organ.pointer.y);
            pointerCore.setAttribute('cx', organ.pointer.x);
            pointerCore.setAttribute('cy', organ.pointer.y);
        }

        // Update button tabs style
        document.querySelectorAll('.clean-tab').forEach(tab => {
            tab.classList.remove('bg-[#0F5144]', 'text-white');
            tab.classList.add('text-slate-500');
        });
        const activeBtn = document.getElementById('btn_' + key);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-500');
            activeBtn.classList.add('bg-[#0F5144]', 'text-white');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        selectCleanOrgan(primaryInitialTarget);
    });
</script>
@endpush
