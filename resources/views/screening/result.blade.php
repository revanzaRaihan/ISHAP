@extends('layouts.app')

@section('title', 'Hasil Skrining Mandiri ISPA — ISHAP')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation Back -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('screening.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-emerald-600 transition">
                &larr; Skrining Ulang
            </a>
            <span class="text-xs text-slate-400 font-mono">ID Sesi: {{ substr($session->id, 0, 8) }}</span>
        </div>

        @if (!$topResult)
            <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    !
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Tidak Ditemukan Indikator Spesifik</h2>
                <p class="text-sm text-slate-600 max-w-md mx-auto mb-6">
                    Gejala yang Anda pilih tidak cukup kuat mencocokkan pola klinis ISPA dalam sistem kami.
                </p>
                <a href="{{ route('screening.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                    Coba Pilih Gejala Lain
                </a>
            </div>
        @else
            @php
                $disease = $topResult->disease;
                $severity = strtolower($disease->severity_level ?? 'ringan');
                $isSevere = $severity === 'berat';
                $isModerate = $severity === 'sedang';

                $badgeClass = match($severity) {
                    'berat' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'sedang' => 'bg-amber-100 text-amber-800 border-amber-200',
                    default => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                };
            @endphp

            <!-- Hero Assessment Banner -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-md border border-slate-200/90 mb-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-br from-emerald-100/40 to-transparent rounded-bl-full pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-slate-100 relative">
                    <div>
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Hasil Skrining Mandiri</span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $badgeClass }}">
                                Risiko {{ ucfirst($severity) }}
                            </span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                            {{ $disease->name }}
                        </h1>
                        <p class="text-sm text-slate-600 mt-2 max-w-2xl leading-relaxed">
                            {{ $disease->description }}
                        </p>
                    </div>

                    <!-- Confidence Score Meter -->
                    <div class="flex items-center gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-200 shrink-0">
                        <div class="text-center">
                            <div class="text-3xl sm:text-4xl font-black text-emerald-600">
                                {{ $topResult->confidence_score }}%
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Estimasi Kecocokan</div>
                        </div>
                        <div class="h-10 w-px bg-slate-200"></div>
                        <div class="text-xs text-slate-500 max-w-[130px] leading-tight">
                            Berdasarkan <strong>{{ $topResult->matched_symptoms_count }}</strong> indikator dari gejala yang Anda rasakan.
                        </div>
                    </div>
                </div>

                <!-- Gejala Terpilih oleh Pengguna -->
                <div class="pt-6">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-3">Gejala yang Anda Laporkan:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($session->symptoms as $sym)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-medium text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $sym->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- PETA ANATOMI INTERAKTIF: Titik Fokus Infeksi & Peradangan -->
            @php
                $diseaseNameLower = strtolower($disease->name ?? '');
                // Tentukan fokus organ primer berdasarkan jenis penyakit
                $primaryTarget = 'lungs';
                if (str_contains($diseaseNameLower, 'cold') || str_contains($diseaseNameLower, 'pilek') || str_contains($diseaseNameLower, 'rhinitis')) {
                    $primaryTarget = 'nasal';
                } elseif (str_contains($diseaseNameLower, 'faringitis') || str_contains($diseaseNameLower, 'tenggorokan')) {
                    $primaryTarget = 'pharynx';
                } elseif (str_contains($diseaseNameLower, 'bronkitis')) {
                    $primaryTarget = 'bronchi';
                } elseif (str_contains($diseaseNameLower, 'pneumonia')) {
                    $primaryTarget = 'alveoli';
                } elseif (str_contains($diseaseNameLower, 'asma')) {
                    $primaryTarget = 'bronchi';
                }
            @endphp

            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 mb-8 overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold shadow-sm shadow-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                Peta Anatomi Titik Infeksi & Peradangan
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                    Interactive 2.5D Body Map
                                </span>
                            </h2>
                            <p class="text-xs text-slate-500">Visualisasi letak jaringan saluran pernapasan yang terdampak oleh kondisi klinis Anda</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 self-start sm:self-auto">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                        <span>Klik organ untuk melihat anatomi</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Kolom Kiri: Diagram Vektor Anatomi SVG Interaktif -->
                    <div class="lg:col-span-6 flex flex-col items-center justify-center p-6 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 rounded-3xl border border-slate-800 relative shadow-inner">
                        <!-- Legend & Mode Switcher -->
                        <div class="w-full flex items-center justify-between text-[11px] text-slate-400 mb-2 px-2">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shadow-sm shadow-rose-500"></span>
                                Titik Inflamasi Utama
                            </span>
                            <span class="text-slate-500">Siluet Transparan 2.5D</span>
                        </div>

                        <!-- SVG Anatomi Saluran Pernapasan -->
                        <svg viewBox="0 0 360 460" class="w-full max-w-[320px] h-auto select-none" id="anatomicalSvg">
                            <defs>
                                <!-- Filter Glow Inflamasi -->
                                <filter id="glow-severe" x="-40%" y="-40%" width="180%" height="180%">
                                    <feGaussianBlur stdDeviation="6" result="blur" />
                                    <feMerge>
                                        <feMergeNode in="blur" />
                                        <feMergeNode in="SourceGraphic" />
                                    </feMerge>
                                </filter>
                                <filter id="glow-moderate" x="-30%" y="-30%" width="160%" height="160%">
                                    <feGaussianBlur stdDeviation="4" result="blur" />
                                    <feMerge>
                                        <feMergeNode in="blur" />
                                        <feMergeNode in="SourceGraphic" />
                                    </feMerge>
                                </filter>

                                <!-- Gradien Paru-paru & Saluran -->
                                <linearGradient id="grad-body" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#334155" stop-opacity="0.25" />
                                    <stop offset="100%" stop-color="#1e293b" stop-opacity="0.15" />
                                </linearGradient>
                                <linearGradient id="grad-lung-normal" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.3" />
                                    <stop offset="100%" stop-color="#0284c7" stop-opacity="0.5" />
                                </linearGradient>
                                <linearGradient id="grad-lung-inflamed" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#fb7185" stop-opacity="0.85" />
                                    <stop offset="100%" stop-color="#e11d48" stop-opacity="0.95" />
                                </linearGradient>
                                <linearGradient id="grad-bronchi" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#38bdf8" />
                                    <stop offset="100%" stop-color="#0284c7" />
                                </linearGradient>
                            </defs>

                            <!-- 1. Siluet Luar Tubuh Manusia (Transparan Glass) -->
                            <path d="M 180,25 C 150,25 140,55 140,85 C 140,115 155,135 160,145 C 130,150 90,165 70,210 C 50,255 45,340 45,430 L 315,430 C 315,340 310,255 290,210 C 270,165 230,150 200,145 C 205,135 220,115 220,85 C 220,55 210,25 180,25 Z" 
                                  fill="url(#grad-body)" stroke="#475569" stroke-width="1.5" stroke-dasharray="4 3" opacity="0.7"/>

                            <!-- Garis Tulang Rusuk Skematis (Background depth) -->
                            <g stroke="#334155" stroke-width="1" opacity="0.4" fill="none">
                                <path d="M 140,230 Q 180,240 220,230" />
                                <path d="M 130,260 Q 180,275 230,260" />
                                <path d="M 125,290 Q 180,310 235,290" />
                                <path d="M 125,320 Q 180,345 235,320" />
                            </g>

                            <!-- 2. Rongga Hidung & Sinus (Upper Tract) -->
                            <g id="svg_part_nasal" class="organ-node cursor-pointer transition-all duration-300" onclick="selectOrgan('nasal')">
                                <path d="M 175,55 Q 180,50 185,55 L 188,72 Q 180,78 172,72 Z" 
                                      id="shape_nasal" fill="#0284c7" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                                <circle cx="180" cy="65" r="14" fill="#38bdf8" fill-opacity="0.1" stroke="#38bdf8" stroke-width="1" stroke-dasharray="2 2" />
                            </g>

                            <!-- 3. Faring & Pangkal Tenggorokan -->
                            <g id="svg_part_pharynx" class="organ-node cursor-pointer transition-all duration-300" onclick="selectOrgan('pharynx')">
                                <path d="M 172,88 L 188,88 L 186,115 L 174,115 Z" 
                                      id="shape_pharynx" fill="#0284c7" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                            </g>

                            <!-- 4. Laring & Trakea (Batang Tenggorokan) -->
                            <g id="svg_part_trachea" class="organ-node cursor-pointer transition-all duration-300" onclick="selectOrgan('trachea')">
                                <rect x="174" y="118" width="12" height="42" rx="3" 
                                      id="shape_trachea" fill="#0284c7" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                                <!-- Cincin Tulang Rawan Trakea -->
                                <line x1="174" y1="126" x2="186" y2="126" stroke="#bae6fd" stroke-width="1.5" />
                                <line x1="174" y1="134" x2="186" y2="134" stroke="#bae6fd" stroke-width="1.5" />
                                <line x1="174" y1="142" x2="186" y2="142" stroke="#bae6fd" stroke-width="1.5" />
                                <line x1="174" y1="150" x2="186" y2="150" stroke="#bae6fd" stroke-width="1.5" />
                            </g>

                            <!-- 5. Paru-paru Kiri & Kanan (Lungs Parenchyma) -->
                            <g id="svg_part_lungs" class="organ-node cursor-pointer transition-all duration-300" onclick="selectOrgan('lungs')">
                                <!-- Paru Kanan (Sisi Kiri di pandangan depan) -->
                                <path d="M 165,185 C 145,180 100,195 90,240 C 80,285 85,340 105,370 C 120,385 155,375 165,340 C 170,300 170,220 165,185 Z" 
                                      id="shape_lung_right" fill="url(#grad-lung-normal)" stroke="#38bdf8" stroke-width="2"/>
                                
                                <!-- Paru Kiri (Dengan Cardiac Notch untuk Jantung) -->
                                <path d="M 195,185 C 215,180 260,195 270,240 C 280,285 275,340 255,370 C 240,385 210,375 200,345 C 190,315 190,220 195,185 Z" 
                                      id="shape_lung_left" fill="url(#grad-lung-normal)" stroke="#38bdf8" stroke-width="2"/>
                            </g>

                            <!-- 6. Percabangan Bronkus (Bronchial Tree) -->
                            <g id="svg_part_bronchi" class="organ-node cursor-pointer transition-all duration-300" onclick="selectOrgan('bronchi')">
                                <!-- Cabang Utama Kanan -->
                                <path d="M 180,160 Q 165,175 145,190 Q 130,205 115,225" fill="none" id="shape_bronchi_1" stroke="#38bdf8" stroke-width="4.5" stroke-linecap="round"/>
                                <path d="M 145,190 Q 140,215 135,245" fill="none" id="shape_bronchi_2" stroke="#38bdf8" stroke-width="3" stroke-linecap="round"/>
                                <path d="M 130,205 Q 115,215 105,235" fill="none" id="shape_bronchi_3" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>

                                <!-- Cabang Utama Kiri -->
                                <path d="M 180,160 Q 195,175 215,190 Q 230,205 245,225" fill="none" id="shape_bronchi_4" stroke="#38bdf8" stroke-width="4.5" stroke-linecap="round"/>
                                <path d="M 215,190 Q 220,215 225,245" fill="none" id="shape_bronchi_5" stroke="#38bdf8" stroke-width="3" stroke-linecap="round"/>
                                <path d="M 230,205 Q 245,215 255,235" fill="none" id="shape_bronchi_6" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>
                            </g>

                            <!-- 7. Kantung Alveolus (Microscopic Node Hotspots) -->
                            <g id="svg_part_alveoli" class="organ-node cursor-pointer transition-all duration-300" onclick="selectOrgan('alveoli')">
                                <circle cx="115" cy="300" r="10" id="shape_alveoli_1" fill="#38bdf8" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                                <circle cx="140" cy="325" r="9" id="shape_alveoli_2" fill="#38bdf8" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                                <circle cx="245" cy="300" r="10" id="shape_alveoli_3" fill="#38bdf8" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                                <circle cx="220" cy="325" r="9" id="shape_alveoli_4" fill="#38bdf8" fill-opacity="0.4" stroke="#38bdf8" stroke-width="2"/>
                            </g>

                            <!-- Label Penunjuk Interaktif Dinamis -->
                            <g id="activePointer" class="transition-all duration-500">
                                <circle id="pointerTarget" cx="180" cy="90" r="8" fill="#e11d48" stroke="#fff" stroke-width="2" class="animate-ping" opacity="0.8"/>
                                <circle id="pointerCore" cx="180" cy="90" r="5" fill="#e11d48" stroke="#fff" stroke-width="1.5" />
                            </g>
                        </svg>

                        <!-- Pijakan visual / glow lantai -->
                        <div class="w-48 h-3 bg-teal-500/20 rounded-full blur-md mt-2"></div>
                    </div>

                    <!-- Kolom Kanan: Detail Organ & Hubungan Klinis -->
                    <div class="lg:col-span-6 space-y-5">
                        <!-- Tab Navigasi Cepat Organ -->
                        <div class="flex flex-wrap gap-1.5 p-1.5 bg-slate-100 rounded-2xl border border-slate-200">
                            <button type="button" onclick="selectOrgan('nasal')" id="tab_nasal" class="organ-tab px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                                Rongga Hidung
                            </button>
                            <button type="button" onclick="selectOrgan('pharynx')" id="tab_pharynx" class="organ-tab px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                                Faring / Tenggorokan
                            </button>
                            <button type="button" onclick="selectOrgan('trachea')" id="tab_trachea" class="organ-tab px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                                Trakea
                            </button>
                            <button type="button" onclick="selectOrgan('bronchi')" id="tab_bronchi" class="organ-tab px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                                Bronkus
                            </button>
                            <button type="button" onclick="selectOrgan('lungs')" id="tab_lungs" class="organ-tab px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                                Paru-Paru
                            </button>
                            <button type="button" onclick="selectOrgan('alveoli')" id="tab_alveoli" class="organ-tab px-3 py-1.5 rounded-xl text-xs font-semibold transition-all">
                                Alveolus
                            </button>
                        </div>

                        <!-- Panel Informasi Organ Aktif -->
                        <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 shadow-xs relative overflow-hidden transition-all duration-300" id="organDetailCard">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400" id="organCategoryBadge">Saluran Napas Atas</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold" id="organStatusBadge">
                                    Fokus Inflamasi
                                </span>
                            </div>

                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight mb-2" id="organTitle">
                                Rongga Hidung & Sinus
                            </h3>

                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4" id="organDescription">
                                Bagian pertama saluran napas yang berfungsi menyaring, menghangatkan, dan melembapkan udara yang masuk.
                            </p>

                            <div class="pt-4 border-t border-slate-200/80 space-y-2.5">
                                <div class="flex items-start gap-2 text-xs text-slate-700">
                                    <span class="font-bold text-slate-900 shrink-0">Gejala Umum:</span>
                                    <span id="organSymptoms">Hidung tersumbat, bersin, sekret hidung encer/kental.</span>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-slate-700">
                                    <span class="font-bold text-slate-900 shrink-0">Dampak Patologis:</span>
                                    <span id="organPathology">Peradangan mukosa epitel silia memicu hipersekresi lendir (pilek).</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hubungan dengan Penyakit Anda -->
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex items-center gap-3.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 font-black text-xs">
                                ISPA
                            </div>
                            <div class="text-xs text-slate-700 leading-relaxed">
                                Berdasarkan kondisi <strong>{{ $disease->name }}</strong>, titik inflamasi klinis utama Anda terfokus pada <span class="font-bold text-emerald-800 underline decoration-emerald-400" id="organFocusMention">{{ $primaryTarget }}</span>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Edukasi: Etiologi & Patogenesis (Mengapa Kuman Masuk?) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Mengapa Anda Mengalami Gejala Ini? (Etiologi & Patogenesis)</h2>
                        <p class="text-xs text-slate-500">Penjelasan mekanisme infeksi dan faktor yang memicu kerentanan tubuh</p>
                    </div>
                </div>

                @if ($disease->pathogenesis_overview)
                    <div class="p-4 rounded-2xl bg-teal-50/50 border border-teal-100 text-xs sm:text-sm text-slate-700 leading-relaxed mb-6">
                        {{ $disease->pathogenesis_overview }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cara Masuknya Kuman -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            Cara Masuknya Patogen / Kuman
                        </h3>
                        <ul class="space-y-2.5 text-xs text-slate-600">
                            @if ($disease->pathogenesis_causes)
                                @foreach ($disease->pathogenesis_causes as $cause)
                                    <li class="flex items-start gap-2">
                                        <span class="text-teal-600 font-bold shrink-0">&bull;</span>
                                        <span>{{ $cause }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li class="text-slate-500">Terhirup percikan cairan napas (droplet) atau kontak permukaan benda umum.</li>
                            @endif
                        </ul>
                    </div>

                    <!-- Faktor Kerentanan Tubuh -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Faktor Kerentanan Daya Tahan Tubuh
                        </h3>
                        <ul class="space-y-2.5 text-xs text-slate-600">
                            @if ($disease->pathogenesis_risk_factors)
                                @foreach ($disease->pathogenesis_risk_factors as $factor)
                                    <li class="flex items-start gap-2">
                                        <span class="text-amber-600 font-bold shrink-0">&bull;</span>
                                        <span>{{ $factor }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li class="text-slate-500">Kelelahan fisik, stres, kurang tidur, atau paparan perubahan cuaca dan polusi udara.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Grid 2 Kolom: Panduan Mandiri vs Tanda Bahaya -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Tips Pemulihan Mandiri -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                                &check;
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Panduan Perawatan Mandiri di Rumah</h3>
                        </div>
                        <ul class="space-y-3 text-xs sm:text-sm text-slate-600">
                            @if ($disease->recovery_tips)
                                @foreach ($disease->recovery_tips as $tip)
                                    <li class="flex items-start gap-2.5">
                                        <span class="text-emerald-500 font-bold mt-0.5">&check;</span>
                                        <span>{{ $tip }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li>Istirahat cukup 7-8 jam per hari dan perbanyak minum air hangat.</li>
                                <li>Hindari makanan berminyak berlebihan dan paparan asap rokok.</li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Tanda Bahaya (Red Flags) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-rose-200/80 bg-rose-50/20 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-rose-100">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                                !
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Tanda Bahaya (Red Flags)</h3>
                        </div>
                        <p class="text-xs text-rose-700 mb-3 font-semibold">Segera kunjungi Faskes / IGD jika muncul tanda berikut:</p>
                        <ul class="space-y-3 text-xs sm:text-sm text-slate-700">
                            @if ($disease->red_flags)
                                @foreach ($disease->red_flags as $flag)
                                    <li class="flex items-start gap-2.5 text-rose-900">
                                        <span class="text-rose-500 font-bold mt-0.5">&#x26A0;</span>
                                        <span>{{ $flag }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li>Napas sangat cepat atau terasa sesak berat hingga bibir membiru.</li>
                                <li>Demam tinggi lebih dari 39°C yang tidak turun dengan pereda demam.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kemungkinan Kondisi Lainnya (Secondary Assessments) -->
            @if ($secondaryResults->isNotEmpty())
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 mb-8">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Kemungkinan Kondisi Lainnya yang Terdeteksi:</h3>
                    <div class="space-y-3">
                        @foreach ($secondaryResults as $sec)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm">
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $sec->disease->name }}</span>
                                    <span class="text-slate-500 text-xs">{{ $sec->reasoning }}</span>
                                </div>
                                <span class="font-mono font-bold text-slate-600 shrink-0 ml-4">
                                    {{ $sec->confidence_score }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Rujukan Faskes & Telemedika Call to Action -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-8 sm:p-10 text-white shadow-xl mb-12">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    <div class="md:col-span-8 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Langkah Rujukan Lanjutan</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Perlu Pemeriksaan Klinis Lebih Lanjut?</h3>
                        <p class="text-sm text-slate-300 leading-relaxed max-w-xl">
                            Temukan rumah sakit, puskesmas, atau klinik terdekat berdasarkan lokasi Anda saat ini, atau langsung konsultasi dengan dokter spesialis secara online.
                        </p>
                    </div>
                    <div class="md:col-span-4 flex flex-col gap-3">
                        <a href="{{ route('facilities.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-slate-900 bg-emerald-400 hover:bg-emerald-300 transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Faskes Terdekat</span>
                        </a>
                        <a href="{{ route('doctors.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-slate-800 border border-slate-700 hover:bg-slate-700 transition">
                            <span>Konsultasi Dokter Online</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    const primaryInitialTarget = "{{ $primaryTarget ?? 'lungs' }}";

    const organData = {
        nasal: {
            category: 'Saluran Napas Atas',
            title: 'Rongga Hidung & Sinus (Cavum Nasi)',
            description: 'Gerbang utama sistem respirasi yang berfungsi menyaring partikel debu/kuman dengan silia, menghangatkan suhu udara, dan menghasilkan lendir protektif.',
            symptoms: 'Hidung tersumbat, bersin-bersin, rinorea (ingus encer/kental), daya penciuman menurun.',
            pathology: 'Invasi virus mengiritasi epitel mukosa, memicu vasodilatasi kapiler lokal dan hipersekresi lendir (pilek).',
            pointer: { x: 180, y: 65 },
            elementIds: ['shape_nasal'],
            badgeClass: 'bg-teal-100 text-teal-800'
        },
        pharynx: {
            category: 'Saluran Napas Atas',
            title: 'Faring & Amandel (Pharynx & Tonsils)',
            description: 'Saluran persimpangan antara jalur pernapasan dan pencernaan, kaya akan jaringan limfoid pelindung tubuh dari patogen yang tertelan atau terhirup.',
            symptoms: 'Nyeri hebat saat menelan (odinofagia), tenggorokan terasa kering/terbakar, suara serak.',
            pathology: 'Kuman melekat pada sel epitel faring, memicu pelepasan histamin dan bradikinin yang merangsang reseptor nyeri.',
            pointer: { x: 180, y: 100 },
            elementIds: ['shape_pharynx'],
            badgeClass: 'bg-rose-100 text-rose-800'
        },
        trachea: {
            category: 'Saluran Napas Bawah (Peralihan)',
            title: 'Trakea (Batang Tenggorok)',
            description: 'Saluran silinder berlapis cincin tulang rawan yang mengalirkan udara bersih ke percabangan bronkus dengan eskalator mukosilia.',
            symptoms: 'Batuk kering menghentak di dada atas, rasa gatal/panas di pangkal leher bagian bawah.',
            pathology: 'Epitel bersilia mengalami iritasi akut, memicu refleks saraf batuk berulang kali untuk proteksi mekanis.',
            pointer: { x: 180, y: 138 },
            elementIds: ['shape_trachea'],
            badgeClass: 'bg-amber-100 text-amber-800'
        },
        bronchi: {
            category: 'Saluran Napas Bawah',
            title: 'Percabangan Bronkus (Bronchial Tree)',
            description: 'Pipa saluran udara yang membelah ke paru kanan dan kiri, mendistribusikan udara hingga ke percabangan bronkiolus.',
            symptoms: 'Batuk berdahak kental, rasa sesak di dada tengah, suara napas mendesing (mengi/wheezing).',
            pathology: 'Reaksi inflamasi memicu pembengkakan mukosa bronkus dan hipertrofi kelenjar penghasil dahak kental.',
            pointer: { x: 145, y: 195 },
            elementIds: ['shape_bronchi_1', 'shape_bronchi_2', 'shape_bronchi_3', 'shape_bronchi_4', 'shape_bronchi_5', 'shape_bronchi_6'],
            badgeClass: 'bg-amber-100 text-amber-800'
        },
        lungs: {
            category: 'Saluran Napas Bawah (Parenkim)',
            title: 'Paru-Paru (Parenkim Pulmo)',
            description: 'Organ vital spons elastis tempat berlangsungnya ekspansi toraks dan proses pertukaran oksigen (O2) serta karbon dioksida (CO2).',
            symptoms: 'Napas pendek saat bergerak, dada terasa tertekan, rasa cepat lelah.',
            pathology: 'Penurunan kapasitas vital akibat infiltrasi sel-sel inflamasi pada parenkim paru.',
            pointer: { x: 235, y: 260 },
            elementIds: ['shape_lung_right', 'shape_lung_left'],
            badgeClass: 'bg-sky-100 text-sky-800'
        },
        alveoli: {
            category: 'Saluran Napas Bawah (Mikroskopis)',
            title: 'Kantung Udara Alveolus (Alveoli)',
            description: 'Jutaan kantung mikro berdinding tipis terbungkus kapiler darah, tempat difusi molekul oksigen ke hemoglobin darah.',
            symptoms: 'Sesak napas berat, laju respirasi cepat, demam tinggi menggigil, ronkhi basah pada auskultasi.',
            pathology: 'Rongga kantung alveoli terisi cairan eksudat radang/nanah, menghalangi difusi oksigen (kondisi khas Pneumonia).',
            pointer: { x: 220, y: 320 },
            elementIds: ['shape_alveoli_1', 'shape_alveoli_2', 'shape_alveoli_3', 'shape_alveoli_4'],
            badgeClass: 'bg-rose-100 text-rose-800'
        }
    };

    function selectOrgan(key) {
        const data = organData[key];
        if (!data) return;

        // 1. Update text di info panel
        document.getElementById('organCategoryBadge').textContent = data.category;
        document.getElementById('organTitle').textContent = data.title;
        document.getElementById('organDescription').textContent = data.description;
        document.getElementById('organSymptoms').textContent = data.symptoms;
        document.getElementById('organPathology').textContent = data.pathology;

        // 2. Update Status Badge
        const statusBadge = document.getElementById('organStatusBadge');
        if (key === primaryInitialTarget) {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200';
            statusBadge.textContent = '🔥 Fokus Inflamasi Utama Skrining';
        } else {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-200 text-slate-700';
            statusBadge.textContent = 'Struktur Terhubung';
        }

        // 3. Update active tab visual
        document.querySelectorAll('.organ-tab').forEach(tab => {
            tab.classList.remove('bg-white', 'text-emerald-700', 'shadow-xs', 'font-bold');
            tab.classList.add('text-slate-600', 'hover:text-slate-900');
        });
        const activeTab = document.getElementById('tab_' + key);
        if (activeTab) {
            activeTab.classList.remove('text-slate-600', 'hover:text-slate-900');
            activeTab.classList.add('bg-white', 'text-emerald-700', 'shadow-xs', 'font-bold');
        }

        // 4. Update Pointer Animation
        const pointerTarget = document.getElementById('pointerTarget');
        const pointerCore = document.getElementById('pointerCore');
        if (pointerTarget && pointerCore) {
            pointerTarget.setAttribute('cx', data.pointer.x);
            pointerTarget.setAttribute('cy', data.pointer.y);
            pointerCore.setAttribute('cx', data.pointer.x);
            pointerCore.setAttribute('cy', data.pointer.y);
        }

        // 5. Highlight SVG Elements
        resetSvgHighlights();

        data.elementIds.forEach(elemId => {
            const el = document.getElementById(elemId);
            if (el) {
                el.setAttribute('stroke', '#f43f5e');
                el.setAttribute('stroke-width', '3.5');
                if (el.tagName.toLowerCase() === 'path' && (elemId === 'shape_lung_right' || elemId === 'shape_lung_left')) {
                    el.setAttribute('fill', 'url(#grad-lung-inflamed)');
                    el.setAttribute('filter', 'url(#glow-moderate)');
                } else if (el.tagName.toLowerCase() === 'circle') {
                    el.setAttribute('fill', '#f43f5e');
                    el.setAttribute('fill-opacity', '0.8');
                } else if (el.tagName.toLowerCase() === 'rect' || (el.tagName.toLowerCase() === 'path' && elemId.includes('pharynx'))) {
                    el.setAttribute('fill', '#f43f5e');
                    el.setAttribute('fill-opacity', '0.6');
                    el.setAttribute('filter', 'url(#glow-moderate)');
                }
            }
        });
    }

    function resetSvgHighlights() {
        // Reset nasal
        const nasal = document.getElementById('shape_nasal');
        if (nasal) {
            nasal.setAttribute('stroke', '#38bdf8');
            nasal.setAttribute('stroke-width', '2');
            nasal.setAttribute('fill', '#0284c7');
            nasal.setAttribute('fill-opacity', '0.4');
            nasal.removeAttribute('filter');
        }

        // Reset pharynx
        const pharynx = document.getElementById('shape_pharynx');
        if (pharynx) {
            pharynx.setAttribute('stroke', '#38bdf8');
            pharynx.setAttribute('stroke-width', '2');
            pharynx.setAttribute('fill', '#0284c7');
            pharynx.setAttribute('fill-opacity', '0.4');
            pharynx.removeAttribute('filter');
        }

        // Reset trachea
        const trachea = document.getElementById('shape_trachea');
        if (trachea) {
            trachea.setAttribute('stroke', '#38bdf8');
            trachea.setAttribute('stroke-width', '2');
            trachea.setAttribute('fill', '#0284c7');
            trachea.setAttribute('fill-opacity', '0.4');
            trachea.removeAttribute('filter');
        }

        // Reset lungs
        ['shape_lung_right', 'shape_lung_left'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.setAttribute('stroke', '#38bdf8');
                el.setAttribute('stroke-width', '2');
                el.setAttribute('fill', 'url(#grad-lung-normal)');
                el.removeAttribute('filter');
            }
        });

        // Reset bronchi
        ['shape_bronchi_1', 'shape_bronchi_2', 'shape_bronchi_3', 'shape_bronchi_4', 'shape_bronchi_5', 'shape_bronchi_6'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.setAttribute('stroke', '#38bdf8');
                el.setAttribute('stroke-width', id.includes('1') || id.includes('4') ? '4.5' : (id.includes('2') || id.includes('5') ? '3' : '2.5'));
                el.removeAttribute('filter');
            }
        });

        // Reset alveoli
        ['shape_alveoli_1', 'shape_alveoli_2', 'shape_alveoli_3', 'shape_alveoli_4'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.setAttribute('stroke', '#38bdf8');
                el.setAttribute('stroke-width', '2');
                el.setAttribute('fill', '#38bdf8');
                el.setAttribute('fill-opacity', '0.4');
            }
        });
    }

    // Auto-select on page load
    document.addEventListener('DOMContentLoaded', () => {
        selectOrgan(primaryInitialTarget);
    });
</script>
@endpush
