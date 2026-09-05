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
