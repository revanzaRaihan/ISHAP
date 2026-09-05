@extends('layouts.app')

@section('title', 'Dokter Mitra Telemedika — ISHAP')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-emerald-600 transition mb-3">
                &larr; Kembali ke Beranda
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Konsultasi Dokter Online Mitra</h1>
            <p class="text-sm text-slate-600 mt-1">
                Lakukan konsultasi lanjutan dengan dokter spesialis paru dan respirasi melalui platform telemedika resmi.
            </p>
        </div>

        <!-- Doctor Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
            @foreach ($categories as $cat)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between hover:shadow-md hover:border-emerald-300 transition">
                    <div>
                        <!-- Avatar & Platform Badge -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-xl flex items-center justify-center shadow-md shadow-emerald-500/20">
                                {{ $cat['initial'] ?? 'D' }}
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                                {{ $cat['platform'] }}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900">{{ $cat['title'] }}</h3>
                        <p class="text-xs text-emerald-700 font-semibold mt-1">{{ $cat['specialty'] }}</p>
                        
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            {{ $cat['description'] }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ $cat['url'] }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition shadow-sm">
                            <span>Hubungi di {{ $cat['platform'] }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info disclaimer -->
        <div class="p-6 rounded-3xl bg-emerald-50/60 border border-emerald-200/80 text-xs text-emerald-900 leading-relaxed">
            <h4 class="font-bold mb-1">Pemberitahuan Telemedika:</h4>
            Konsultasi dokter online difasilitasi oleh platform mitra berlisensi resmi (Halodoc). ISHAP tidak memungut biaya perantara atas layanan konsultasi maupun menyimpan data dokter individu. Dokter berwenang memberikan resep elektronik dan pengantar rujukan fisik jika diperlukan.
        </div>

    </div>
</div>
@endsection
