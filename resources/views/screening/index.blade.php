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

        <!-- AI Assistant: Subjective Complaint Parser (Gemini 2.5 Flash) -->
        <div class="mb-10 rounded-3xl bg-gradient-to-br from-white via-emerald-50/40 to-teal-50/30 border-2 border-emerald-200/80 p-6 sm:p-8 shadow-sm relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-36 h-36 bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-sm shadow-emerald-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                            Asisten Gejala AI ISHAP
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Gemini 2.5 Flash
                            </span>
                        </h2>
                    </div>
                </div>
                <span class="text-xs text-slate-500 font-medium">Bantu pilihkan gejala dari keluhan bebas</span>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                Bingung membedakan istilah medis? Ceritakan apa yang Anda rasakan dengan bahasa sehari-hari. Asisten AI akan menganalisis dan membantu mencentang gejala yang relevan di formulir secara otomatis.
            </p>

            <div class="space-y-3">
                <div class="relative">
                    <textarea id="aiComplaintInput" 
                              rows="3" 
                              maxlength="500" 
                              placeholder="Contoh: &quot;Sejak kemarin tenggorokan rasanya sakit banget buat nelen air liur, hidung mampet dan meler, terus badan rasanya agak sumeng/demam dan lemas...&quot;"
                              class="w-full rounded-2xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-sm text-slate-800 p-4 transition-all resize-none shadow-sm placeholder:text-slate-400 leading-relaxed"></textarea>
                    
                    <div class="flex items-center justify-between mt-2 px-1">
                        <div class="text-[11px] text-slate-400 font-medium" id="aiCharCount">
                            0 / 500 karakter
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    id="btnClearComplaint" 
                                    class="hidden text-xs text-slate-500 hover:text-slate-700 px-2 py-1 rounded-lg hover:bg-slate-100 transition">
                                Bersihkan
                            </button>
                            <button type="button" 
                                    id="btnExtractAI" 
                                    class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-600/20 active:scale-95 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg id="aiBtnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <svg id="aiSpinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="aiBtnText">✨ Bantu Pilih Gejala Otomatis</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- AI Response / Feedback Toast -->
                <div id="aiFeedbackBox" class="hidden transition-all duration-300">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>

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
                                <label for="symptom_{{ $symptom->id }}" id="label_symptom_{{ $symptom->id }}" class="symptom-card relative flex items-start gap-4 p-4 rounded-2xl border-2 border-slate-200 hover:border-emerald-400 bg-slate-50/40 hover:bg-emerald-50/20 cursor-pointer transition-all duration-200">
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

    // AI Symptom Extraction Logic
    const complaintInput = document.getElementById('aiComplaintInput');
    const charCount = document.getElementById('aiCharCount');
    const btnExtractAI = document.getElementById('btnExtractAI');
    const btnClearComplaint = document.getElementById('btnClearComplaint');
    const aiFeedbackBox = document.getElementById('aiFeedbackBox');
    const aiBtnIcon = document.getElementById('aiBtnIcon');
    const aiSpinner = document.getElementById('aiSpinner');
    const aiBtnText = document.getElementById('aiBtnText');

    if (complaintInput) {
        complaintInput.addEventListener('input', () => {
            const length = complaintInput.value.length;
            charCount.textContent = `${length} / 500 karakter`;
            if (length > 0) {
                btnClearComplaint.classList.remove('hidden');
            } else {
                btnClearComplaint.classList.add('hidden');
            }
        });

        btnClearComplaint.addEventListener('click', () => {
            complaintInput.value = '';
            charCount.textContent = '0 / 500 karakter';
            btnClearComplaint.classList.add('hidden');
            aiFeedbackBox.classList.add('hidden');
            complaintInput.focus();
        });

        btnExtractAI.addEventListener('click', async () => {
            const complaint = complaintInput.value.trim();
            if (complaint.length < 4) {
                showAIFeedback('warning', 'Mohon ceritakan keluhan Anda minimal 4 karakter agar asisten AI dapat memahaminya.');
                complaintInput.focus();
                return;
            }

            // Set loading state
            btnExtractAI.disabled = true;
            aiBtnIcon.classList.add('hidden');
            aiSpinner.classList.remove('hidden');
            aiBtnText.textContent = 'Menganalisis keluhan...';
            aiFeedbackBox.classList.add('hidden');

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                              || document.querySelector('input[name="_token"]')?.value;

            try {
                const response = await fetch("{{ route('screening.extract-symptoms') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ complaint: complaint })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const matchedIds = data.matched_symptom_ids || [];
                    if (matchedIds.length > 0) {
                        let matchedNames = [];
                        matchedIds.forEach(id => {
                            const cb = document.getElementById('symptom_' + id);
                            if (cb) {
                                cb.checked = true;
                                handleSymptomChange(cb);

                                // Visual pulse highlight
                                const card = cb.closest('.symptom-card');
                                if (card) {
                                    card.classList.add('ring-4', 'ring-emerald-400', 'ring-offset-2');
                                    setTimeout(() => {
                                        card.classList.remove('ring-4', 'ring-emerald-400', 'ring-offset-2');
                                    }, 2000);
                                    
                                    const titleSpan = card.querySelector('span.font-bold');
                                    if (titleSpan) matchedNames.push(titleSpan.textContent.trim());
                                }
                            }
                        });

                        showAIFeedback('success', data.summary, matchedNames);

                        // Smooth scroll to the first category if needed
                        const firstCard = document.getElementById('symptom_' + matchedIds[0]);
                        if (firstCard) {
                            firstCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        showAIFeedback('info', data.summary || 'Keluhan Anda belum mencerminkan gejala ISPA spesifik pada formulir. Silakan tinjau dan pilih gejala secara manual di bawah.');
                    }
                } else {
                    const errMsg = data.summary || data.message || 'Gagal memproses keluhan dengan AI. Silakan centang gejala secara manual.';
                    showAIFeedback('error', errMsg);
                }
            } catch (err) {
                console.error(err);
                showAIFeedback('error', 'Terjadi kendala jaringan saat menghubungi layanan AI. Silakan centang gejala secara manual di bawah.');
            } finally {
                btnExtractAI.disabled = false;
                aiBtnIcon.classList.remove('hidden');
                aiSpinner.classList.add('hidden');
                aiBtnText.textContent = '✨ Bantu Pilih Gejala Otomatis';
            }
        });
    }

    function showAIFeedback(type, message, tags = []) {
        aiFeedbackBox.classList.remove('hidden');

        let bgClass, borderClass, textClass, iconSvg;

        if (type === 'success') {
            bgClass = 'bg-emerald-50';
            borderClass = 'border-emerald-300';
            textClass = 'text-emerald-900';
            iconSvg = `<svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        } else if (type === 'warning' || type === 'info') {
            bgClass = 'bg-amber-50';
            borderClass = 'border-amber-300';
            textClass = 'text-amber-900';
            iconSvg = `<svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        } else {
            bgClass = 'bg-rose-50';
            borderClass = 'border-rose-300';
            textClass = 'text-rose-900';
            iconSvg = `<svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        }

        let tagsHtml = '';
        if (tags.length > 0) {
            tagsHtml = `
                <div class="mt-2.5 pt-2.5 border-t border-emerald-200/80 flex flex-wrap items-center gap-1.5">
                    <span class="text-xs font-semibold text-emerald-800">Gejala terpilih otomatis:</span>
                    ${tags.map(t => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white text-emerald-700 border border-emerald-300 shadow-2xs">${t}</span>`).join('')}
                </div>
            `;
        }

        aiFeedbackBox.className = `p-4 rounded-2xl border ${bgClass} ${borderClass} ${textClass} text-xs sm:text-sm mt-3 animate-fade-in`;
        aiFeedbackBox.innerHTML = `
            <div class="flex items-start gap-3">
                ${iconSvg}
                <div class="flex-grow">
                    <p class="font-medium leading-relaxed">${message}</p>
                    ${tagsHtml}
                </div>
            </div>
        `;
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
