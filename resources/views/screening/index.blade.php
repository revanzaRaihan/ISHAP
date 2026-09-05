@extends('layouts.app')

@section('title', 'Checklist Skrining Mandiri ISPA — ISHAP')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Instructions -->
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-emerald-600 transition mb-3">
                &larr; Kembali ke Beranda
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Skrining Mandiri Gejala ISPA</h1>
                    <p class="text-sm text-slate-600 mt-1">
                        Pilih semua gejala yang sedang Anda rasakan dalam kurun 24–48 jam terakhir.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold self-start sm:self-auto">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Skrining Mandiri &bull; 100% Privat</span>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('screening.submit') }}" method="POST" id="screeningForm">
            @csrf

            <div class="space-y-10 pb-28">
                @foreach ($symptomsByCategory as $category => $symptoms)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80">
                        <!-- Category Title -->
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full {{ $category === 'Saluran Napas Bawah' ? 'bg-amber-500' : ($category === 'Saluran Napas Atas' ? 'bg-emerald-500' : 'bg-cyan-500') }}"></div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $category ?? 'Gejala Umum' }}</h2>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                {{ count($symptoms) }} Gejala
                            </span>
                        </div>

                        <!-- Symptom Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($symptoms as $symptom)
                                <label for="symptom_{{ $symptom->id }}" class="symptom-card relative flex items-start gap-4 p-4 rounded-2xl border-2 border-slate-200 hover:border-emerald-400 bg-slate-50/40 hover:bg-emerald-50/20 cursor-pointer transition-all">
                                    <div class="pt-0.5">
                                        <input type="checkbox" 
                                               name="symptom_ids[]" 
                                               value="{{ $symptom->id }}" 
                                               id="symptom_{{ $symptom->id }}"
                                               class="symptom-checkbox w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 transition cursor-pointer"
                                               onchange="handleSymptomChange(this)">
                                    </div>
                                    <div class="flex-grow">
                                        <span class="font-bold text-slate-900 text-sm block">{{ $symptom->name }}</span>
                                        @if ($symptom->description)
                                            <span class="text-xs text-slate-500 leading-relaxed block mt-1">{{ $symptom->description }}</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-2xl py-4">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-sm shadow-inner" id="selectedBadge">
                            0
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 uppercase font-semibold tracking-wider block">Status Pemilihan</span>
                            <span class="text-sm font-bold text-slate-800" id="selectedText">0 gejala dipilih</span>
                        </div>
                    </div>

                    <button type="submit" 
                            id="submitButton"
                            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-lg shadow-emerald-600/25 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span>Analisis Perkiraan Risiko</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function handleSymptomChange(checkbox) {
        const card = checkbox.closest('.symptom-card');
        if (checkbox.checked) {
            card.classList.remove('border-slate-200', 'bg-slate-50/40');
            card.classList.add('border-emerald-600', 'bg-emerald-50/60', 'shadow-sm');
        } else {
            card.classList.remove('border-emerald-600', 'bg-emerald-50/60', 'shadow-sm');
            card.classList.add('border-slate-200', 'bg-slate-50/40');
        }
        updateCounter();
    }

    function updateCounter() {
        const checkboxes = document.querySelectorAll('.symptom-checkbox:checked');
        const count = checkboxes.length;
        document.getElementById('selectedBadge').textContent = count;
        document.getElementById('selectedText').textContent = count + ' gejala dipilih';
        
        const submitBtn = document.getElementById('submitButton');
        if (count === 0) {
            submitBtn.classList.add('opacity-50', 'pointer-events-none');
        } else {
            submitBtn.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    // Run on initial load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.symptom-checkbox').forEach(cb => {
            if (cb.checked) {
                cb.closest('.symptom-card').classList.add('border-emerald-600', 'bg-emerald-50/60');
            }
        });
        updateCounter();
    });
</script>
@endpush
